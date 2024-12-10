<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/guardar_registros.php");
header('Content-type: application/json; charset=utf-8');

guardar_info_log("expedicion material", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
//params.put("pedido", ped);
//params.put("material", mat);
//params.put("ean", ean);
//params.put("terminal", term);
//params.put("usuario", usu);
//params.put("cantidad", cant);
//params.put("tipdoc", tip);
//
//
//B3
//{ubic=A00200110A, ean=9PAAHBBA6LA007283, tipdoc=ZCHA, material=59012110FA, pedido=0013192408, usuario=GUPA, terminal=TR01, cantidad=1}
//$db->autocommit(false);
$hoy = date("Y-m-d");
$hoyhora = date("H:i:s");
$pedido = $_POST['pedido'];
$material = $_POST['material'];
$ean = $_POST['ean'];
$terminal = $_POST['terminal'];
$usuario = $_POST['usuario'];
$cantidad = floatval($_POST['cantidad']);
$stockRE = floatval($_POST['stockRE']);
$stockPI = floatval($_POST['stockPI']);
$tiene_presentacion = $_POST['tiene_presentacion'];

$posicion = (int) htmlspecialchars(trim($_POST['posicion']));
$lote_seleccionado = htmlspecialchars(trim($_POST['lote']));
$tipo_ubicacion = htmlspecialchars(trim($_POST['tipo_ubicacion']));
$reposicion = htmlspecialchars(trim($_POST['reposicion']));
$forzar_cierre_pedido = htmlspecialchars(trim($_POST['forzar'])) === "1";
$forzar_cierre_ubicacion = htmlspecialchars(trim($_POST['forzar_cierre_ubicacion'])) === "1";
$mensaje_cierre = htmlspecialchars(trim($_POST['mensaje_cierre']));

$tipdoc = $_POST['tipdoc'];
$ubic = str_replace('-', '', $_POST['ubic']);
$svtExpt = $_POST['svtExpt'];
$response = array();
//obtengo la clase de pedido
$sq0 = "select pedclase, almrefer from pedexcab where pedexentre = '{$pedido}' limit 1";

$rs0 = $db->query($sq0);
$rf0 = $rs0->fetch_assoc();
$clase = $rf0['pedclase'];
$cod_alma = $rf0['almrefer'];
$terminal_pedido = 0;

// Verificar si articulo esta loteado

$sql = "SELECT * from arti WHERE artrefer = '$material'";
$resultado = $db->query($sql)->fetch_assoc();

$filtro_lote = "";
$insertar_lote = "";
$es_loteado = $resultado['artlotemar'] === "SI";
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
guardar_info_log("expedicion material", $sq1);

$rs1 = $db->query($sq1);
$rf1 = $rs1->fetch_assoc();
$stUbiOrig = floatval($rf1['total']);
$canStUbi = floatval($rf1['total']);

//modifico stockart
$sq3 = "select candispo,canmure,canpedven,cantransfe, candevol from stockart where artrefer = '{$material}'  AND cod_alma='{$cod_alma}' limit 1";
$rs3 = $db->query($sq3);
$rf3 = $rs3->fetch_assoc();

//modifico pedexdet
$sq7 = "select canpedi,canprepa,canpendi, pedpos from pedexdet where pedexentre = '{$pedido}' and artrefer = '{$material}' AND pedpos='$posicion' limit 1";
$rs7 = $db->query($sq7);
$rf7 = $rs7->fetch_assoc();

$customMsg = '';

$pendientes_preparacion = floatval($rf7['canpedi']) - floatval($rf7['canprepa']);


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
    $fetubi = date("Y-m-d");
    $horubi = date("H:i:s");
    $sql = "INSERT INTO expeartce SET id_pedido='$pedido',pedpos='$posicion',id_articulo='$material',cod_alma='$cod_alma',ubicacion='$ubic' {$insertar_lote},fecha='$fetubi',hora='$horubi',usuario='$usuario',stock='$stockPI',inci='INCI'";
    $fetubi = date("Y-m-d");
    $horubi = date("H:i:s");
    $incidencia = floatval($rf7['canpedi']) - floatval($rf7['canprepa']);
    //guardar_movimiento_stock($db, $pedido, $material, $incidencia, $ubic, 'INCI', $user, $fetubi, $horubi, $cod_alma, $ubic);
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
// reposicion en curso si hay stock en altura
//descuento el material preparado de stockubi

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
if ($tipo_ubicacion == "PI") {
    if (
        ($forzar_cierre_ubicacion || $stockPI < $cantidad) && $stockRE > 0 &&
        $pendientes_preparacion > $cantidad
    ) {
        $generar_reposicion = 1;
    }
}


$canStUbi = $canStUbi - $cantidad;
if ($canStUbi == 0) {
    $sq2 = "delete from stockubi where ubirefer = '{$ubic}' and artrefer = '{$material}' AND cod_alma='{$cod_alma}' {$filtro_lote}";

    if (!$db->query($sq2)) {
        guardar_error_log("modificart pedido", "Error eliminar stockubi");
        $errors[] = 1;
    }
    $sqc = "SELECT 
            artubiref.artrefer,
            artubiref.ubirefer,
            COALESCE(sum(stockubi.canti), 0) as total 
            FROM artubiref 
            LEFT JOIN stockubi on artubiref.ubirefer=stockubi.ubirefer and artubiref.artrefer=stockubi.artrefer
            WHERE artubiref.ubirefer='{$ubic}' AND artubiref.cod_alma='{$cod_alma}'
            group by stockubi.ubirefer, stockubi.artrefer,stockubi.cod_alma 
            HAVING COALESCE(sum(stockubi.canti), 0) > 0 ";
    $resultado = $db->query($sqc);
    if ($resultado->num_rows == 0) {
        $sql = "UPDATE ubimapa SET ubisitu='VA' WHERE ubirefer='$ubic' AND cod_alma='$cod_alma'";
        if (!$db->query($sql)) {
            guardar_error_log("actualizar ubimapa", $db->error);
            $errors[] = 1;
        }
    }
} else {
    $hoy = date("Y-m-d");
    $hoyhora = date("H:i:s");
    $negCant = -1 * $cantidad;
    //        $sq2 = "update stockubi set canti = {$canStUbi} where artrefer = '{$material}' and ubirefer = '{$ubic}'";
    $sq2 = "insert into stockubi set 
        canti = {$negCant}, 
        artrefer = '{$material}', 
        ubirefer = '{$ubic}', 
        etnum = 'salida', 
        cod_alma='{$cod_alma}' {$insertar_lote}";
    /*guardar_movimiento_stock($db, $pedido, $material, $cantidad, $ubic, 'SAVE', $usuario, $hoy, $hoyhora, $cod_alma, $ubic);*/
    if (!$db->query($sq2)) {
        guardar_error_log("modificart pedido", "Error insertar stockubi");
        /*guardar_movimiento_stock($db, $pedido, $material, $cantidad, $stockPI, 'SAVE', $usuario, $hoy, $hoyhora, $cod_alma, $stockPI);*/
        $errors[] = 1;
    }
}




$candispo = floatval($rf3['candispo']);
$canmure = floatval($rf3['canmure']);
$canpedven = floatval($rf3['canpedven']);
$cantransfe = floatval($rf3['cantransfe']);
$candevol = floatval($rf3['candevol']);
//resto de mi disponible
$candispo = $candispo - $cantidad;
switch ($clase) {
    case 'ZUB':
        $canpedven = $canpedven + $cantidad;
        break;
    case 'EUB':
        $canpedven = $canpedven + $cantidad;
        break;
    default:
        $cantransfe = $cantransfe + $cantidad;
        break;
}

$canpedi = floatval($rf7['canpedi']);
$canprepa = floatval($rf7['canprepa']);
$canpendi = floatval($rf7['canpendi']);
$canprepa = $canprepa + $cantidad;
$canpendi = $canpedi - $canprepa;
$closed = '';
$sql = "SELECT COUNT(*) AS cantidad 
        from pedexdetcajas 
        WHERE pedexentre='$pedido' AND artrefer='$material'";
$cantidad_por_cajas = $db->query($sql)->fetch_object()->cantidad;
if ($canpendi == 0 || ($forzar_cierre_pedido && !$generar_reposicion) || $cantidad_por_cajas > 0) {
    $closed = ",expst=1 ";
}

$sq8 = "UPDATE pedexdet set  "
    . "canprepa = {$canprepa},"
    . "canpendi = {$canpendi},"
    . "fechcie = now(),"
    . "horcie = now(),"
    . "repocurso = $generar_reposicion, "
    . "incidencia = '{$mensaje_cierre}',"
    . "usuario = '{$usuario}'"
    . $closed
    . " where pedpos = '$posicion' AND pedexentre = '{$pedido}' and artrefer = '{$material}'";
guardar_movimiento_stock($db, $pedido, $material, $cantidad, $ubic, 'SAVE', $usuario, $hoy, $hoyhora, $cod_alma, $ubic);
if (!$db->query($sq8)) {
    guardar_error_log("modificart pedido", "Error actualizar pedexdet");
    guardar_error_log("modificart pedido", $sq8);
    $errors[] = 1;
}
$sql_actualizar = "UPDATE pedexdetcajas set  "
    . "canprepa = {$canprepa},"
    . "canpendi = {$canpendi},"
    . "fechcie = now(),"
    . "horcie = now(),"
    . "usuario = '{$usuario}'"
    . " where pedpos = '$posicion' AND pedexentre = '{$pedido}' and artrefer = '{$material}'";

if (!$db->query($sql_actualizar)) {
    guardar_error_log("modificart pedido", "Error actualizar pedexdet");
    guardar_error_log("modificart pedido", $sq8);
    $errors[] = 1;
}

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

    //    echo $db->error;
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
    $response['pedidos_con_reposicion'] = obtenerPedidosConReposicionPendientes($db, $pedido);

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
    $sql = "SELECT * FROM pedexdet 
    WHERE  pedexentre = '$pedido' 
    AND repocurso = true 
    AND expst=0";
    $resultado = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}
