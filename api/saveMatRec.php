<?php
require('../conect.php');
require_once("../utils/validadores.php");
require_once("../utils/respuesta.php");
require_once("../logger/logger.php");
require_once("../utils/guardar_registros.php");
require_once("../utils/respuesta.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
//crear hash del estante
$user = $_POST['usuario'];
$pedido = strtoupper($_POST['pedido']);
$lote_articulo = htmlspecialchars(trim($_POST['lote']));
$material = htmlspecialchars(trim($_POST['material']));
$posicion_sap = htmlspecialchars(trim($_POST['posicion_sap']));
$cantidad_a_guardar = $_POST['cantidad'];
$cantidad_ingresada = $_POST['cantidad'];
$multireferencia = $_POST['multiRef'];

$sql = "SELECT artlotemar FROM arti WHERE artrefer = '$material'";
$resultado = $db->query($sql)->fetch_assoc();

$loteado = $resultado['artlotemar'] === "SI";

if ($loteado && empty($lote_articulo)) {
    retorna_resultado(422, ['mensaje' => "lote no enviado"]);
    exit;
}

if (isset($pedido)) {
    validaciones($db);
    $selPa = "select pal_status from pallets where pal_pedido = '{$pedido}' and pal_desc = {$_POST['pallet']}";
    $r1 = $db->query($selPa);
    if ($r1->num_rows == 0) {
        $inPal = "insert into pallets set 
            pal_status = 0,
            pal_desc = {$_POST['pallet']},
            pal_pedido = '{$pedido}',
            pal_usr = '{$user}',
            pat_ts = now()
        ";
        $db->query($inPal);
    }
    /*$selMa = "SELECT sum(a.canpedi) as canpedi, b.artser FROM pedredet a left join arti b on a.artrefer = b.artrefer  where a.pedrefer = '{$pedido}' and b.artrefer = '{$_POST['material']}' GROUP BY a.pedrefer, a.artrefer limit 1";
    $r2 = $db->query($selMa);
    $r2a = $r2->fetch_assoc(); //cantidad total en el pedido en todas las posiciones

    $selMa = "select sum(ap_cant) as total from pallet_mat where ap_pedido = '{$pedido}' and ap_mat = '{$_POST['material']}' and pedpos='$posicion_sap'";
    $r3 = $db->query($selMa);
    $r3a = $r3->fetch_assoc(); // cantidad total del material ingresados distribuidos en los diferentes pallets
    guardar_sql_log(json_encode($selMa));
    guardar_sql_log(json_encode($r3a));*/

    $total_actual = floatval($r3a['total']);
    $canti = $total_actual + floatval($_POST['cantidad']); // total de lo registrado en los pallets + el nuevo ingreso
    $pen = floatval($r2a['canpedi']) - $canti; // cantidad de materiales pendientes luego del total ingresado


    $terminal = $_POST['terminal'];
    $sql = "SELECT almrefer FROM termi WHERE tercod = '{$terminal}' LIMIT 1";
    $resultado = $db->query($sql);
    $datos_terminal = $resultado->fetch_assoc();
    $cod_alma = $datos_terminal['almrefer'];
    $insert_lote = "";
    if ($loteado) {
        $insert_lote = " artlote = '{$lote_articulo}',";
    }
    /*$total_maxima = obtener_cantidad_maxima($db, $pedido, $_POST['material'], $posicion_sap);
        if ($cantidad_a_guardar > ($total_maxima - $total_actual)) {
            $cantidad_a_guardar = $total_maxima - $total_actual;
        }*/


    if ($cantidad_a_guardar <> 0) {

        // obtenemos las posiciones de pedredet
        $datos_recepcion = obtener_datos_recepcion($db, $material, $pedido);
        $cantidad = $cantidad_a_guardar;

        /* Por cada posicion obtenemos la cantidad sujeta a la misma y llenamos con los valores insertados desde la terminal */
        foreach ($datos_recepcion as $recepcion) {
            if ($cantidad_a_guardar <= 0) {
                break;
            }
            /*Se calcula la cantidad limite a ingresar por posicion*/
            $cantidad_recepcion = $recepcion['canpedi'] - $recepcion['canprepa'];
            $posicion_interna = $recepcion['pedpos'];
            $material_interno = $recepcion['artrefer'];
            if ($cantidad_a_guardar >= $cantidad_recepcion) {
                $cantidad = $cantidad_recepcion;
            } else {
                $cantidad = $cantidad_a_guardar;
            }
            $cantidad_a_guardar = $cantidad_a_guardar - $cantidad;

            // se inserta en palletmat el valor correspondiente a la posicion dependiendo del valor de los datos
            $inMat = "insert into pallet_mat set 
                pedpos = '$posicion_interna',
                ap_pedido = '{$pedido}',
                ap_mat = '{$_POST['material']}',
                ap_cant = {$cantidad_ingresada},
                ap_termi = '{$terminal}', 
                ap_pall = {$_POST['pallet']},
                etnum = '$multireferencia',                       
                ap_usr = '{$user}', 
                cod_alma = '{$cod_alma}',
                {$insert_lote}
                ap_ts = now()";

            if (!$db->query($inMat)) {
                $db->rollback();
                retorna_resultado(200, ['error' => true, 'mensaje' => 'error inseert palletmat']);
                exit;
            }

            // si la cantidad pendiente es 0 entonces se cierra la posicion
            $cantpendi = $cantidad_recepcion - $cantidad;
            $canprepa = $cantidad + $recepcion['canprepa'];
            $expst = "expst=0";
            if ($cantpendi == 0) {
                $expst = "expst=1 ";
            }

            $rgMat = "UPDATE pedredet set 
                canprepa = '$canprepa',
                canpendi = '$cantpendi',
                {$expst} 
                where pedrefer = '$pedido' and artrefer = '$material_interno' and pedpos = {$posicion_interna}";
            if (!$db->query($rgMat)) {
                $db->rollback();
                retorna_resultado(200, ['error' => true, 'mensaje' => 'error inseert pedredet']);
                exit;
            }
            $rgMat = "UPDATE pedrecab set pedresitu = 'PP' where pedrefer = '$pedido' and pedresitu = 'PD'";
            if (!$db->query($rgMat)) {
                $db->rollback();
                retorna_resultado(200, ['error' => true, 'mensaje' => 'error inseert pedrecab']);
                exit;
            }

            if ($_POST['serie'] != '') {
                $rgMatser = "UPDATE serati set 
                                serrecep = 1
                                where artped  = '{$pedido}' and artserie = '{$_POST['serie']}'
                            ";
                $db->query($rgMatser);
            }
        }

        if ($cantidad_a_guardar > 0) {
            $sq = "UPDATE pedredet 
                        SET canexp='$cantidad_a_guardar' 
                        WHERE artrefer='$material_interno' AND pedrefer='$pedido' AND pedpos='$posicion_interna'";
            if (!$db->query($sq)) {
                $db->rollback();
                retorna_resultado(200, ['error' => true, 'mensaje' => $db->error]);
                exit;
            }
            $sq = " INSERT into artexdentrec set pedrefer= '{$pedido}',
                        artrefer = '$material',
                        cant = '$cantidad_a_guardar',
                        artts = now()";
            if (!$db->query($sq)) {
                $db->rollback();
                retorna_resultado(200, ['error' => true, 'mensaje' => "#2 error al guardar execente $cantidad_a_guardar " . $db->error]);
                exit;
            }
        }
    } else {
        //guardar_error_log(__FILE__,)
        $db->rollback();
        retorna_resultado(200, ['error' => false, 'mensaje' => "Material registrado. 2" . $msg]);
        exit;
    }
}
$db->commit(); //coimit
$db->close();
$response["error"] = false;
$response['mensaje'] = "Guardado";
echo json_encode($response);
exit;

function actualizarPadreKit($db, $pedido, $material)
{
    $sql = "SELECT canprepa, canpendi, idpadre FROM pedredet WHERE pedrefer  = '{$pedido}' and artrefer = '{$material}'";
    $material_pedido = (object) $db->query($sql)->fetch_assoc();
    if (empty($material_pedido->idpadre)) {
        return;
    }
    $sql = "UPDATE pedredet set canprepa='{$material_pedido->canprepa}', 
            canpendi='{$material_pedido->canpendi}' WHERE artrefer = '{$material_pedido->idpadre}' and pedrefer='$pedido' ";
    $res = $db->query($sql);
    if (!$res) {
        print_r("falla");
        $db->rollback();
        exit;
    }
}

function obtener_cantidad_maxima($db, $pedido, $material, $posicion_sap): float
{
    $sql = "SELECT canpedi FROM pedredet WHERE pedrefer = '$pedido' AND artrefer='$material'";
    guardar_info_log(__file__, $sql);

    $res = $db->query($sql);
    if (!$res) {
        guardar_error_log(__FILE__, $db->error);
        $db->rollback();
        exit;
    }
    return floatval($res->fetch_assoc()['canpedi']);
}

function obtener_datos_recepcion($db, $articulo, $pedido)
{
    $sql = "SELECT * FROM pedredet WHERE artrefer='$articulo' AND pedrefer='$pedido' AND expst=0";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}


function obtener_presentacion_por_pallet($db, $articulo)
{
    $sql = "SELECT canpresen as cantidad_presentacion, preseref as presentacion FROM artipresen where artrefer = '$articulo' AND preseref = 'PAL'";
    $res = $db->query($sql);
    $resultado = $res->fetch_assoc();
    return $resultado ? (object) $resultado : null;
}


function validaciones($db)
{
    try {
        $material = htmlspecialchars(trim($_POST['material']));
        $cantidad = floatval($_POST['cantidad']);
        $id_pallet = $_POST['pallet'];
        $pedido = $_POST['pedido'];
        $sql = "SELECT SUM(ap_cant) as cantidad from pallet_mat where ap_mat = '$material' and ap_pall = '$id_pallet' and ap_pedido = '$pedido'";
        $res = $db->query($sql);
        $total_ubicado = $res->fetch_assoc()['cantidad'];
        $cantidad_total = $cantidad + $total_ubicado;
        $presentacion_pallet = obtener_presentacion_por_pallet($db, $material);
        if ($presentacion_pallet && $cantidad_total > $presentacion_pallet->cantidad_presentacion) {
            $db->rollback();
            retorna_resultado(200, ['error' => true, 'mensaje' => "Cantidad mayor a la presentacion por pallet"]);
            exit;
        }
    } catch (\Throwable $th) {
        retorna_resultado(200, ['error' => true, 'mensaje' => $th->getMessage()]);
    }
}
