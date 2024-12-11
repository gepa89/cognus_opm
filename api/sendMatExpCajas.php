<?php
require_once('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
header('Content-type: application/json; charset=utf-8');

guardar_custom_log("expedicion material", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$pedido = $_POST['pedido'];
$material = $_POST['material'];
$ean = $_POST['ean'];
$terminal = $_POST['terminal'];
$usuario = $_POST['usuario'];
$cantidad = (int) $_POST['cantidad'];
$stockRE = (int) $_POST['stockRE'];
$stockPI = (int) $_POST['stockPI'];
$tiene_presentacion = $_POST['tiene_presentacion'];
$es_trabajo_piso = $_POST['tipo_almacenaje'] == "PS";

$posicion = (int) htmlspecialchars(trim($_POST['posicion']));
$lote_seleccionado = htmlspecialchars(trim($_POST['lote']));
$reposicion = htmlspecialchars(trim($_POST['reposicion']));
$forzar_cierre_pedido = htmlspecialchars(trim($_POST['forzar'])) === "1";
$forzar_cierre_ubicacion = htmlspecialchars(trim($_POST['forzar_cierre_ubicacion'])) === "1";

$tipdoc = $_POST['tipdoc'];
$ubic = str_replace('-', '', $_POST['ubic']);
$svtExpt = $_POST['svtExpt'];
$response = array();
//obtengo la clase de pedido
$sq0 = "select pedclase, almrefer from pedexcabcajas where pedexentre = '{$pedido}' limit 1";

$rs0 = $db->query($sq0);
$rf0 = $rs0->fetch_assoc();
$clase = $rf0['pedclase'];
$cod_alma = $rf0['almrefer'];
$terminal_pedido = 0;

// Verificar si articulo esta loteado

$sql = "SELECT arti.artlotemar, artipresen.canpresen from arti 
INNER JOIN artipresen ON arti.artrefer=artipresen.artrefer 
WHERE arti.artrefer = '$material'";
$resultado = $db->query($sql)->fetch_assoc();

$filtro_lote = "";
$insertar_lote = "";
$cantidad_por_presentacion = (int) $resultado['canpresen'];
$es_loteado = $resultado['artlotemar'] === "SI";

$parametros_ubicacion = new stdClass();
$parametros_ubicacion->artlotemar = $resultado['artlotemar'];
$parametros_ubicacion->artrefer = $material;
$parametros_ubicacion->pedido = $pedido;
$parametros_ubicacion->almrefer = $cod_alma;


$generar_reposicion = 0;
if ($resultado['artlotemar'] === "SI") {
    $filtro_lote = " AND artlote = '$lote_seleccionado'";
    $insertar_lote = ",artlote='$lote_seleccionado'";
    if (empty($lote_seleccionado) || $lote_seleccionado === "SELECCIONAR") {
        guardar_error_log("send max exp", "lote no enviado");
        $db->rollback();
        $db->close();
        $response = array();
        $response["error"] = TRUE;
        $response['mensaje'] = "Lote no enviado";
        echo json_encode($response);
        exit;
    }
}

//print_r($rf0);
//echo $clase;
//reviso stockubi
$sq1 = "select sum(canti) as total from stockubi where ubirefer = '{$ubic}' and artrefer = '{$material}' AND cod_alma='{$cod_alma}' {$filtro_lote} limit 1";
//print_r($sq1);
$rs1 = $db->query($sq1);
$rf1 = $rs1->fetch_assoc();
$stUbiOrig = (int) $rf1['total'];
$canStUbi = (int) $rf1['total'];

//modifico stockart
$sq3 = "select candispo,canmure,canpedven,cantransfe, candevol from stockart where artrefer = '{$material}'  AND cod_alma='{$cod_alma}' limit 1";
$rs3 = $db->query($sq3);
$rf3 = $rs3->fetch_assoc();

//modifico pedexdet
$sq7 = "select canpedi,canprepa,canpendi, pedpos from pedexdetcajas where pedexentre = '{$pedido}' and artrefer = '{$material}' AND pedpos='$posicion' limit 1";
$rs7 = $db->query($sq7);
$rf7 = $rs7->fetch_assoc();

$customMsg = '';

$pendientes_preparacion = (int) $rf7['canpedi'] - (int) $rf7['canprepa'];

//validacion de cantidad de pedidos
if ($cantidad > $pendientes_preparacion) {
    guardar_error_log("Cantidad ingresada supera cantidad de preparacion disponible", $cantidad);
    $db->close();
    $response["error"] = TRUE;
    $response['mensaje'] = "Cantidad ingresada supera cantidad de preparacion disponible";
    echo json_encode($response);
    exit;
}
if ($forzar_cierre_pedido || $forzar_cierre_ubicacion) {
    $sql = "INSERT INTO expeartce SET id_pedido='$pedido',id_articulo='$material',cod_alma='$cod_alma',ubicacion='$ubic' {$insertar_lote} ";
    $query = $db->query($sql);
    if (!$query) {
        guardar_error_log("error insert cantidad cerrados", $sql);
        $db->rollback();
        $db->close();
        $response["error"] = TRUE;
        $response['mensaje'] = "error insert cantidad cerrados";
        echo json_encode($response);
        exit;
    }
}
$tiene_ubicacion_disponible = obtenerUbicacion($db, $parametros_ubicacion, ["RE", "PS"]) != null;
// reposicion en curso si hay stock en altura
//descuento el material preparado de stockubi
if ($posicion % 10 == 0) {

    //validacion cantidad en ubicacion stock

    /* Si la cantidad ingresada es menor a la cantidad de stock del articulo */
    if ($stUbiOrig < $cantidad) {
        /*if (!$es_loteado) {
            guardar_error_log("Cantidad ingresada supera cantidad de stock disponible", $cantidad);
            $db->rollback();
            $db->close();
            $response["error"] = TRUE;
            $response['mensaje'] = "Cantidad ingresada supera cantidad de stock disponible";
            echo json_encode($response);
            exit;
        }*/

        $cantidad = $stUbiOrig;

        /*$errors[] = 1;
        $customMsg = " Stock en ubicación inferior a lo ingresado. ";*/
    }

    $canStUbi = $canStUbi - (int) $cantidad;
    if ($canStUbi == 0) {
        $sq2 = "delete from stockubi where ubirefer = '{$ubic}' and artrefer = '{$material}' AND cod_alma='{$cod_alma}' {$filtro_lote}";

        if (!$db->query($sq2)) {
            guardar_error_log("modificart pedido", "Error eliminar stockubi");
            $errors[] = 1;
        }
    } else {
        $negCant = -1 * $cantidad;
        //        $sq2 = "update stockubi set canti = {$canStUbi} where artrefer = '{$material}' and ubirefer = '{$ubic}'";
        $sq2 = "insert into stockubi set 
        canti = {$negCant}, 
        artrefer = '{$material}', 
        ubirefer = '{$ubic}', 
        etnum = 'salida', 
        cod_alma='{$cod_alma}' {$insertar_lote}";
        if (!$db->query($sq2)) {
            guardar_error_log("modificart pedido", "Error insertar stockubi");

            $errors[] = 1;
        }
    }
}

$candispo = (int) $rf3['candispo'];
$canmure = (int) $rf3['canmure'];
$canpedven = (int) $rf3['canpedven'];
$cantransfe = (int) $rf3['cantransfe'];
$candevol = (int) $rf3['candevol'];
//resto de mi disponible
$candispo = $candispo - (int) $cantidad;
switch ($clase) {
    case 'ZUB':
        $canpedven = $canpedven + (int) $cantidad;
        break;
    case 'EUB':
        $canpedven = $canpedven + (int) $cantidad;
        break;
    default:
        $cantransfe = $cantransfe + (int) $cantidad;
        break;
}
guardar_custom_log(__FILE__, $rf7);
$canpedi = (int) $rf7['canpedi'];
$canprepa = (int) $rf7['canprepa'];
$canpendi = (int) $rf7['canpendi'];
$canprepa = $canprepa + (int) $cantidad;
$canpendi = $canpedi - $canprepa;
$canpendi_cajas = $canpendi;
if ($canpendi < $cantidad_por_presentacion) {
    $canpendi_cajas = 0;
}
guardar_custom_log("cantidad cajas", $canpendi_cajas);
$closed = "";
if ($canpendi_cajas == 0 || $forzar_cierre_pedido || !$tiene_ubicacion_disponible) {
    $closed = ",expst=1 ";
}

$sq8 = "UPDATE pedexdetcajas set  "
    . "canprepa = {$canprepa},"
    . "canpendi = {$canpendi},"
    . "fechcie = now(),"
    . "horcie = now(),"
    . "repocurso = $generar_reposicion, "
    . "usuario = '{$usuario}'"
    . $closed
    . " where pedpos = '$posicion' AND pedexentre = '{$pedido}' and artrefer = '{$material}'";
guardar_custom_log(__FILE__, $sq8);
$cerrar_posicion = $canpendi == 0 ? ",expst=1" : "";
$sql_actualizar = "UPDATE pedexdet set  "
    . "canprepa = {$canprepa},"
    . "canpendi = {$canpendi},"
    . "fechcie = now(),"
    . "horcie = now(),"
    . "usuario = '{$usuario}' $cerrar_posicion"
    . " where pedpos = '$posicion' AND pedexentre = '{$pedido}' and artrefer = '{$material}'";
guardar_custom_log(__FILE__, $sql_actualizar);
if (!$db->query($sql_actualizar)) {
    guardar_error_log("modificart pedido", "Error actualizar pedexdet");
    guardar_error_log("modificart pedido", $sq8);
    $errors[] = 1;
}
if (!$db->query($sq8)) {
    guardar_error_log("modificart pedido", "Error actualizar pedexdet");
    guardar_error_log("modificart pedido", $sq8);
    $errors[] = 1;
}
if ($posicion % 10 == 0) {

    //actualizo stockart
    $sq3 = "update stockart set  "
        . "candispo = {$candispo},"
        . "canmure = {$canmure},"
        . "canpedven = {$canpedven},"
        . "cantransfe = {$cantransfe},"
        . "candevol = {$candevol}"
        . " where  artrefer = '{$material}' AND cod_alma='{$cod_alma}' ";
    guardar_info_log("expedicion material", $sq3);

    if (!$db->query($sq3)) {
        guardar_error_log("modificart pedido", "Error actualizar stockart");
        $errors[] = 1;
    }
}
if ($tipdoc == 'ZCHA') {
    //inhabilito el nro de serie usado
    $sq4 = "update serati set serprep = 1, artentr = '{$pedido}' where artserie = '{$ean}'";
    if (!$db->query($sq4)) {
        $errors[] = 1;
    }
}
$response['pedidos_con_reposicion'] = [];
if (!in_array(1, $errors)) {
    if ($db->commit()) {
        $response['mensaje'] = "Material Registrado.";
        $response["error"] = FALSE;
        $response['reposicion_generada'] = $generar_reposicion === 1;
        $response['pedido_reposicion'] = array(
            "artrefer" => $material,
            "pedexentre" => $pedido,
            "pedpos" => $posicion
        );
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo  material." . $customMsg;
        $db->rollback();
    }
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "No se pudo registrar material." . $customMsg;
    $db->rollback();
}
$db->close();

echo json_encode($response);
exit;

function obtenerPedidosConReposicionPendientes($db, $pedido)
{
    $sql = "SELECT * FROM pedexdetcajas 
    WHERE  pedexentre = '$pedido' 
    AND repocurso = true 
    AND expst=0";
    $resultado = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}

function obtenerUbicacion($db, $fila, $tipoUbicacion = ['RE'])
{
    $in_ubicaciones = implode("','", $tipoUbicacion);
    $ubicacion = null;
    if ($fila->artlotemar === "SI") {
        $sql = "SELECT stockubi.*, SUM(stockubi.canti) AS cantidad from loteart 
            inner join stockubi ON stockubi.artlote = loteart.artlote AND stockubi.cod_alma = loteart.cod_alma 
            left outer join expeartce ON expeartce.id_articulo = stockubi.artrefer
            AND expeartce.cod_alma = stockubi.cod_alma
            AND expeartce.ubicacion = stockubi.ubirefer 
            AND expeartce.id_pedido = '$fila->pedido' 
            WHERE stockubi.artrefer = '$fila->artrefer' 
            AND stockubi.cod_alma='$fila->almrefer' 
            AND expeartce.id is null 
            GROUP BY stockubi.artlote 
            HAVING SUM(stockubi.canti) > 0 
            order by fecaduc ASC LIMIT 1";
        $query = $db->query($sql);
        if ($query->num_rows > 0) {
            $dato = $query->fetch_object();
            $ubicacion = [
                "ubirefer" => $dato->ubirefer,
                "cantidad" => $dato->canti,
                "lote" => $dato->artlote
            ];
        }
    } else {
        $sql = "SELECT ubimapa.ubirefer, sum(stockubi.canti) as cantidad 
            FROM stockubi inner join ubimapa 
            ON ubimapa.ubirefer = stockubi.ubirefer AND ubimapa.cod_alma=stockubi.cod_alma 
            left outer join expeartce ON expeartce.id_articulo = stockubi.artrefer
            AND expeartce.cod_alma = stockubi.cod_alma
            AND expeartce.ubicacion = stockubi.ubirefer 
            AND expeartce.id_pedido = '$fila->pedido' 
            WHERE stockubi.artrefer = '$fila->artrefer' 
            AND ubimapa.cod_alma = '$fila->almrefer' 
            AND ubimapa.ubitipo IN ('$in_ubicaciones') 
            AND ubimapa.zoncodpre = 'A' 
            AND expeartce.id is null 
            GROUP BY  stockubi.artrefer,ubimapa.ubirefer,stockubi.cod_alma 
            HAVING sum(stockubi.canti) > 0 ORDER BY ubimapa.ubirefer ASC";
        $query = $db->query($sql);
        $ubicaciones_probables = $query->fetch_all(MYSQLI_ASSOC);
        if (count($ubicaciones_probables) > 0) {
            $ubicacion = $ubicaciones_probables[0];
        }
    }
    return $ubicacion;
}
