<?php

require_once("../utils/sanitizador.php");
require_once('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/guardar_registros.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
guardar_entrada_salida_log("entrada", json_encode($_POST, JSON_PRETTY_PRINT));
$db->begin_transaction();
$respuesta = [
    "mensaje" => "exito"
];
try {
    $estado = actualizarDatos($db);
    $respuesta["recargar_rutas"] = $estado == 0;
    $db->commit();
} catch (\Throwable $th) {
    $db->rollback();
    $db->close();
    guardar_error_log(__FILE__, $th->getMessage());
    retorna_resultado(array("mensaje" => "Ocurrio un error"), 500);
}
//$db->rollback();
$db->close();
guardar_info_log("guardar ubicacion maquinista", json_encode($respuesta));

retorna_resultado($respuesta, 200);
function actualizarDatos($db)
{
    //Actualizar pedubidet con cantidades leas
    guardar_info_log("actualizar datos", null);

    $cantidad_a_ubicar = floatval(sanitizar($_POST['cantidad_a_ubicar']));
    $codigo_articulo = sanitizar($_POST['codigo_articulo']);
    $codigo_pedido = sanitizar($_POST['codigo_pedido']);
    $cod_almacen = sanitizar($_POST['cod_almacen']);
    $ubicacion_ref = sanitizar($_POST['ubicacion_ref']);
    $multireferencia = sanitizar($_POST['multireferencia']);
    $posicion = sanitizar($_POST['posicion']);
    $muelle = sanitizar($_POST['muelle']);
    $usuario = sanitizar($_POST['usuario']);
    guardar_info_log("datos", json_encode($_POST));
    $hoy = date("Y-m-d h:i:s");
    $hoyhora = date("H:i:s");

    $sql = "SELECT pedubidet.cantiu, pedubidet.canubi, pedubidet.canupen, pedubicab.pedrefer 
            FROM pedubidet
            INNER JOIN pedubicab ON pedubicab.pedubicod = pedubidet.pedubicod 
            WHERE pedubidet.artrefer='$codigo_articulo' 
            AND pedubidet.posubi='$posicion' 
            AND pedubidet.pedubicod='$codigo_pedido'";

    $query = $db->query($sql);
    $pedido = $query->fetch_assoc();
    if (!$pedido) {
        retorna_resultado(array("mensaje" => "No Existe pedido"), 404);
        return;
    }
    $referencia_pedido = $pedido['pedrefer'];
    //validad cantidad ingresada
    $cantidad_pendiente_previa = $pedido['canupen'] ?? $pedido['cantiu'];
    $cantidad_pendiente_previa = floatval($cantidad_pendiente_previa);
    if ($cantidad_a_ubicar > $cantidad_pendiente_previa || $cantidad_a_ubicar < 0) {
        retorna_resultado(array("mensaje" => "Cantidad a ubicar no valido"), 400);
    }

    $cantidad_pendiente = $cantidad_pendiente_previa - $cantidad_a_ubicar;
    $cantidad_ubicada_nueva = (floatval($pedido['canubi']) ?? 0.0) + $cantidad_a_ubicar;
    $estado = $cantidad_pendiente == 0 ? 1 : 0;
    $sql = "UPDATE pedubidet SET 
    canubi='$cantidad_ubicada_nueva', 
    canupen='$cantidad_pendiente', 
    fecha='$hoy',
    hora= '$hoyhora',
    expst=$estado,
    usuario='$usuario' 
    WHERE artrefer='$codigo_articulo' 
    AND posubi='$posicion' 
    AND pedubicod='$codigo_pedido'";
    $res = $db->query($sql);
    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "verifique update pedubidet"), 500);
    }

    $sql = "UPDATE ubimapa 
    SET ubisitu='LL' 
    WHERE ubirefer='$ubicacion_ref' AND cod_alma='$cod_almacen'";
    $db->query($sql);
    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "verifique update ubimapa"), 500);
    }
    $sql = "UPDATE ubimapa 
    SET ubisitu='VA' 
    WHERE ubirefer='$muelle' AND cod_alma='$cod_almacen'";
    $db->query($sql);
    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "verifique update ubimapa muelle"), 500);
    }

    /* Verificar si se llevo el total del articulo a la ubicacion referenciada */
    if ($cantidad_pendiente == 0) {
        $sql = "UPDATE ubitempo SET estado_ubicacion=True 
            WHERE ubirefer='$ubicacion_ref' AND multiref='$multireferencia' AND cod_alma='$cod_almacen' AND pedido='$codigo_pedido'";
        $db->query($sql);
        guardar_info_log("actualizarubitempo", $sql);
    }

    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "verifique update ubimapa muelle"), 500);
    }

    $sql = "SELECT ubitipo FROM ubimapa 
            WHERE ubirefer='$ubicacion_ref' AND cod_alma='$cod_almacen' limit 1";
    $query = $db->query($sql);
    if (!$query) {
        retorna_resultado(array("mensaje" => "Verificar select arubiref"), 404);
        return;
    }

    $resultado = $query->fetch_assoc();
    if (!$resultado) {
        $db->rollback();
        $db->close();
        retorna_resultado(array("mensaje" => "Ubicacion no existe"), 500);
    }
    $tipo = $resultado['ubitipo'];
    $sql = "SELECT COUNT(*) as cantidad FROM artubiref WHERE 
    ubirefer='$ubicacion_ref' AND 
    artrefer='$codigo_articulo' AND 
    ubitipo='$tipo' AND 
    cod_alma='$cod_almacen'";

    $query = $db->query($sql);
    if (!$query) {
        retorna_resultado(array("mensaje" => "Verificar select arubiref"), 404);
        return;
    }
    $res = $query->fetch_assoc();
    guardar_info_log("cantidad arubiref", $res['cantidad']);
    if ($res['cantidad'] == 0) {
        $sql = "INSERT INTO artubiref 
        SET ubirefer='$ubicacion_ref', 
        artrefer='$codigo_articulo', 
        ubitipo='$tipo', 
        cod_alma='$cod_almacen'";
        guardar_info_log("creando arubiref", $sql);
        $db->query($sql);
        if (!$res) {
            print_r("error 0");
            exit;
        }
    }

    $sql = "SELECT COUNT(*) as cantidad FROM artubiref WHERE 
    artrefer='$codigo_articulo' 
    AND cod_alma='$cod_almacen'";

    $query = $db->query($sql);
    if (!$query) {
        retorna_resultado(array("mensaje" => "Verificar select total arubiref"), 404);
        return;
    }
    $res = $query->fetch_assoc();
    if ($res['cantidad'] > 1) {
        $sql = "UPDATE artubiref 
        SET ubilibre=True
        WHERE 
        artrefer='$codigo_articulo' 
        AND ubitipo='$tipo' 
        AND ubirefer='$ubicacion_ref'
        AND cod_alma='$cod_almacen'";
        $res = $db->query($sql);
        if (!$res) {
            print_r("error 1");
            exit;
        }
    }

    $sql = "SELECT COUNT(*) as cantidad FROM artubiref WHERE 
    artrefer='$codigo_articulo' 
    AND ubitipo='$tipo' 
    AND cod_alma='$cod_almacen' AND ubilibre=False";
    $res = $db->query($sql);
    if (!$res) {
        print_r("error 2");
        exit;
    }
    $resultado = $res->fetch_assoc();
    if ($resultado['cantidad'] == 0 || floatval($pedido['canupen']) == $cantidad_a_ubicar) {
        $sql = "UPDATE artubiref 
        SET ubilibre=False WHERE artrefer='$codigo_articulo' AND cod_alma='$cod_almacen'";
        $res = $db->query($sql);
        if (!$res) {
            print_r("error 3");
            exit;
        }
    }

    actualizarStockUbicacion(
        $db,
        $cod_almacen,
        $codigo_articulo,
        $ubicacion_ref,
        $cantidad_a_ubicar,
        $multireferencia
    );
    actualizarStockArticulo($db, $cantidad_a_ubicar, $codigo_articulo, $cod_almacen, $referencia_pedido, $ubicacion_ref, $usuario, $hoy, $hoyhora, $multireferencia);

    actualizarCabeceraPedidos($db, $codigo_pedido);
    actualizarPedidosMuelle($db, $referencia_pedido);

    return $estado;
}

function actualizarPedidosMuelle($db, $referencia_pedido)
{
    $sql = "UPDATE pedmuelle SET pedstatus='1' WHERE pedrefer='$referencia_pedido'";
    $db->query($sql);
}
function actualizarCabeceraPedidos($db, $codigo_pedido)
{
    guardar_info_log("actualizar-cabecera", $codigo_pedido);
    $resultados = null;
    //obtenenmos cantidad de pedidos abiertos expst=0
    $sql = "SELECT COUNT(*) as cantidad FROM pedubidet WHERE pedubicod='$codigo_pedido' AND expst=0";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    guardar_info_log("actualizar-cabecera resultado", $resultados);

    if ($resultado->fetch_object()->cantidad == 0) {
        guardar_info_log("actualizar cabecera", "CE");
        $sql = "UPDATE pedubicab SET situped='CE' WHERE pedubicod='$codigo_pedido'";
        $db->query($sql);
        actualizarAsignacionPedidos($db, $codigo_pedido);
        eliminarUbicacionTemporal($db, $codigo_pedido);
    }
}

function eliminarUbicacionTemporal($db, $codigo_pedido)
{
    guardar_info_log("eliminar-ubicacion", $codigo_pedido);

    $sql = "DELETE FROM ubitempo WHERE pedido='$codigo_pedido'";
    $db->query($sql);
}
function actualizarAsignacionPedidos($db, $codigo_pedido)
{
    guardar_info_log("actualizar-asignacion-pedido", $codigo_pedido);

    $sql = "UPDATE assig_ped SET st=1 WHERE pedido='$codigo_pedido'";
    $db->query($sql);
}
function actualizarStockUbicacion(
    $db,
    $cod_almacen,
    $codigo_articulo,
    $ubicacion_ref,
    $cantidad_a_ubicar,
    $multireferencia
) {
    // verifica si existe el articulo en el almacen
    $sql = "SELECT stockubi.artlote FROM stockubi
        INNER JOIN ubimapa ON ubimapa.ubirefer = stockubi.ubirefer 
        AND stockubi.cod_alma = ubimapa.cod_alma
        WHERE stockubi.cod_alma='$cod_almacen' 
        AND stockubi.etnum='$multireferencia' 
        AND stockubi.artrefer='$codigo_articulo' 
        AND ubimapa.ubitipo='MR' ";
    guardar_info_log("guardar trabajo maquinista", $sql);
    $artlote = $db->query($sql)->fetch_assoc()['artlote'];
    guardar_info_log("resultado", $artlote);
    $filtro_lote = "";
    $insertar_lote = "";
    if (!empty($artlote)) {
        $filtro_lote = " AND stockubi.artlote = '$artlote'";
        $insertar_lote = ",artlote = '$artlote'";
    }

    $sql = "SELECT COUNT(*) AS cantidad 
    FROM stockubi 
    INNER JOIN ubimapa ON ubimapa.ubirefer = stockubi.ubirefer AND stockubi.cod_alma = ubimapa.cod_alma
    WHERE artrefer='$codigo_articulo' 
        AND stockubi.cod_alma='$cod_almacen' 
        AND ubitipo='MR' 
        AND etnum='$multireferencia' 
        AND canti='$cantidad_a_ubicar'
        {$filtro_lote}";
    $db->query($sql);
    $res = $db->query($sql);
    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "error al verificar stok ubi"), 500);
    }
    $existe = $res->fetch_assoc()['cantidad'] > 0;
    $hoy = date("Y-m-d");

    if ($existe) {
        //actualiza la ubicacion de referencia del articulo
        $sql = "UPDATE
                    stockubi
                INNER JOIN ubimapa ON
                    stockubi.ubirefer = ubimapa.ubirefer
                SET
                    stockubi.ubirefer = '$ubicacion_ref',
                    fecingre = '$hoy' 
                WHERE
                    stockubi.artrefer = '$codigo_articulo'
                    AND stockubi.cod_alma = '$cod_almacen'
                    AND etnum='$multireferencia'  
                    AND ubimapa.ubitipo = 'MR' 
                    {$filtro_lote}";
        guardar_info_log(__FILE__, $sql);
        $res = $db->query($sql);
        if (!$res) {
            $db->rollback();
            retorna_resultado(array("mensaje" => "error al actualizar stok ubi"), 500);
        }
    } else {
        $sql = "UPDATE
                    stockubi
                INNER JOIN ubimapa ON
                    stockubi.ubirefer = ubimapa.ubirefer
                SET
                    stockubi.canti = stockubi.canti-{$cantidad_a_ubicar},
                    fecingre = '$hoy' 
                WHERE
                    stockubi.artrefer = '$codigo_articulo'
                    AND stockubi.cod_alma = '$cod_almacen'
                    AND stockubi.etnum='$multireferencia'  
                    AND ubimapa.ubitipo = 'MR'
                    {$filtro_lote}";
        $res = $db->query($sql);
        if (!$res) {
            $db->rollback();
            retorna_resultado(array("mensaje" => "error al actualizar stok ubi"), 500);
        }

        $sql = "INSERT INTO stockubi SET ubirefer = '$ubicacion_ref', 
        artrefer='$codigo_articulo',
        fecingre = '$hoy',
        cod_alma='$cod_almacen',
        canti='$cantidad_a_ubicar',
        etnum='$multireferencia' 
        {$insertar_lote}";
        $res = $db->query($sql);
        if (!$res) {
            $db->rollback();
            retorna_resultado(array("mensaje" => "error al insertar stok ubi"), 500);
        }
    }
}

function actualizarStockArticulo($db, $cantidad_a_ubicar, $cod_articulo, $cod_almacen, $referencia_pedido, $ubicacion_ref, $usuario, $hoy, $hoyhora, $multireferencia)
{
    $sql = "UPDATE stockart SET candispo=candispo+$cantidad_a_ubicar, 
    canmure=canmure-$cantidad_a_ubicar WHERE
    artrefer='$cod_articulo' AND cod_alma='$cod_almacen'";
    $res = $db->query($sql);
    if (!$res) {
        $db->rollback();
        retorna_resultado(array("mensaje" => "error al actualizar stock articulo"), 500);
    }
    guardar_movimiento_stock($db, $referencia_pedido, $cod_articulo, $cantidad_a_ubicar, $ubicacion_ref, 'UBAL', $usuario, $hoy, $hoyhora, $cod_almacen, $multireferencia);
}

function retorna_resultado($respuesta, $estado)
{
    guardar_entrada_salida_log("salida", json_encode($respuesta, JSON_PRETTY_PRINT));
    header('Content-type: application/json; charset=utf-8', true, $estado);
    echo json_encode($respuesta);
    exit;
}
