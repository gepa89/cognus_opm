<?php require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/guardar_registros.php");

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
//crear hash del estante
$pallet = $_POST['pallet'];
$terminal = strtoupper($_POST['terminal']);
$pedido = strtoupper($_POST['pedido']);
$muelle = strtoupper($_POST['muelle']);
$fecingre = date("Y-m-d");
$horingre = time();
$multireferenica = strtoupper($_POST['multiref']);
try {
    if (empty($pedido)) {
        $db->rollback();
        $db->close();
        $response["error"] = TRUE;
        $response['mensaje'] = "Sin pedido";
        guardar_error_log(__FILE__, 'sin pedido');
        echo json_encode($response);
        exit;
    }

    //    reviso si pallet esta vacio
    $sqs = "SELECT SUM(ap_cant) as ap_cant,ap_pedido,ap_mat,artdesc,etnum,ap_usr,cod_alma, pallet_mat.pedpos  from pallet_mat 
        INNER JOIN arti on arti.artrefer = pallet_mat.ap_mat 
        where ap_pedido = '{$pedido}' and ap_pall= {$pallet} GROUP BY ap_mat,ap_pall,etnum";

    $r1 = $db->query($sqs);
    if ($r1->num_rows == 0) {
        $db->rollback();
        $db->close();
        $response["error"] = TRUE;
        $response['mensaje'] = "Ningun articulo encontrado";
        guardar_error_log(__FILE__, $sqs);
        guardar_error_log(__FILE__, 'Ningun articulo encontrado');

        echo json_encode($response);
        exit;
    }
    $id_cabecera = null;
    while ($datos = $r1->fetch_assoc()) {
        $pedrefer = $datos['ap_pedido'];
        $ap_mat = $datos['ap_mat'];
        $artdesc = $datos['artdesc'];
        $ap_cant = $datos['ap_cant'];
        $pedpos = $datos['pedpos'];
        $etnum = $datos['etnum'];
        $user = $datos['ap_usr'];
        $fetubi = date("Y-m-d");
        $horubi = date("H:i:s");
        $cod_alma = $datos['cod_alma'];

        if ($id_cabecera == null) {
            $pedubicod = verificar_pallet($db, $pedrefer, $pallet);
            if (!$pedubicod) {
                $sql_insertar = "INSERT INTO pedubicab set pedrefer='$pedrefer', fecubi='$fetubi', 
                horubi='$horubi', pedclase='UB', situped='PD', cod_alma='$cod_alma', pal_desc='$pallet'";
                if (!$db->query($sql_insertar)) {
                    guardar_error_log(__FILE__, $sql_insertar);
                    guardar_error_log(__FILE__, $db->error);
                    $db->rollback();
                    $db->close();
                    retornar(array('error' => true, 'mensaje' => "Falla 1"));
                }
                $id_cabecera = $db->insert_id;
            } else {
                $id_cabecera = $pedubicod;
            }
        }
        $sql_ubicacion_anterior = "SELECT * FROM pedubidet WHERE pedubicod='$id_cabecera' AND artrefer='$ap_mat' AND etnum='$etnum'";
        $detalle = $db->query($sql_ubicacion_anterior)->fetch_assoc();
        if ($detalle) {
            $detalle = (object) $detalle;
            $cantidad_total = floatval($detalle->cantiu) + floatval($ap_cant);
            $sql = "UPDATE pedubidet SET cantiu = '$cantidad_total', canupen='$cantidad_total' WHERE pedubicod='$id_cabecera' AND artrefer='$ap_mat' AND etnum='$etnum' AND pedpos='$pedpos'";
            $db->query($sql);
        } else {
            $sql = "SELECT * from pedredet where pedrefer='$pedrefer' and pedpos='$pedpos' and artrefer = '$ap_mat'";
            $res = $db->query($sql);
            $excedente = 0;
            $cantidad_esperada = 0;
            if ($res->num_rows == 0) {
                guardar_error_log(__FILE__, "Detalle recepcion no encontrado $sql");
            } else {
                $detalle = $res->fetch_assoc();
                $cantidad_esperada = $detalle['canpedi'];
                $excedente = $detalle['canexp'];
            }
            $desc = htmlentities(htmlspecialchars($artdesc));
            $sql_detalle = "INSERT INTO pedubidet
                                        (pedubicod, posubi, artrefer, artdesc, cantiu, canubi, canupen, etnum, muelle, usuario, fecha,expst,pedpos,exedente,cantidadsap)
                                        VALUES(\"$id_cabecera\", 1, \"$ap_mat\", \"$desc\", $ap_cant, 0, $ap_cant, \"$etnum\", \"$muelle\", null, null,0,$pedpos,$excedente,$cantidad_esperada);";
            $query = $db->query($sql_detalle);
            if (!$query) {
                guardar_error_log(__FILE__, $sql_detalle);
                guardar_error_log(__FILE__, $db->error);
                $db->rollback();
                retornar(array('error' => true, 'mensaje' => "Falla 2"));
            }
        }
    }
    $sq = "update pallets set 
                pal_status = 1
                where 
                pal_desc = {$pallet} and
                pal_pedido = '{$pedido}'";

    $sqDOCKupd = "update ubimapa set
            ubisitu = 'LL'
            where ubirefer = '{$muelle}'";
    $res = $db->query($sqDOCKupd);
    if (!$res) {
        guardar_error_log(__FILE__, $sqDOCKupd);
        guardar_error_log(__FILE__, $db->error);
        $db->rollback();
        retornar(array('error' => true, 'mensaje' => "Falla 3"));
    }
    $sqpa = "SELECT pal_desc, pal_status, (select count(*) as total 
            from pallet_mat where ap_pedido = pal_pedido and ap_pall = pal_desc) as cant_items 
            from pallets where pal_pedido = '{$pedido}'  order by pal_desc";
    $rs = $db->query($sqpa);
    if (!$rs) {
        guardar_error_log(__FILE__, $sqpa);
        guardar_error_log(__FILE__, $db->error);
        $db->rollback();
        retornar(array('error' => true, 'mensaje' => "Falla 4"));
    }
    while ($ax = $rs->fetch_assoc()) {
        if ($ax['cant_items'] > 0 && (int)$ax['pal_status'] == 0) {
            $sq = "update pallets set 
                        pal_status = 1
                        where 
                        pal_desc = {$ax['pal_desc']} and
                        pal_pedido = '{$pedido}'
                        ";
            $res = $db->query($sq);
            if (!$res) {
                guardar_error_log(__FILE__, $sq);
                guardar_error_log(__FILE__, $db->error);
                $db->rollback();
                retornar(array('error' => true, 'mensaje' => "Falla 5"));
            }
        }
    }
    $sqmat = "SELECT ap_mat as artrefer, ap_cant as canprepa, etnum, artlote 
            from pallet_mat where ap_pedido = '{$pedido}' and ap_pall='$pallet' and etnum = '$multireferenica'";
    $xs1 = $db->query($sqmat);
    if (!$xs1) {
        guardar_error_log(__FILE__, $sqmat);
        guardar_error_log(__FILE__, $db->error);
        $db->rollback();
        retornar(array('error' => true, 'mensaje' => "Falla 6"));
    }
    while ($qs = $xs1->fetch_assoc()) {
        $lote = $qs['artlote'];
        $filtro_lote = "";
        $insert_lote = "";

        if (!empty($lote)) {
            $filtro_lote = " AND artlote = '{$lote}' ";
            $insert_lote = ", artlote = '{$lote}' ";
        }
        $sqlstu = "select * from stockubi where ubirefer = '{$muelle}' and artrefer ='{$qs['artrefer']}' and cod_alma='$cod_alma' and etnum ='{$qs['etnum']}'";
        $xs2 = $db->query($sqlstu);
        if ($xs2->num_rows == 0) {

            $sin = "insert into stockubi set ubirefer = '{$muelle}' {$insert_lote} , artrefer ='{$qs['artrefer']}', canti = '{$qs['canprepa']}', etnum = '{$qs['etnum']}', cod_alma='{$cod_alma}', fecingre='{$fecingre}'";
            guardar_error_log(__FILE__, $sin);
            if ($db->query($sin)) {

                //                    registro stockart
                $sqlstu = "select * from stockart where artrefer ='{$qs['artrefer']}' AND cod_alma='{$cod_alma}' limit 1";
                $xs3 = $db->query($sqlstu);
                if ($xs3->num_rows == 0) {
                    $sinsa = "insert into stockart set  artrefer ='{$qs['artrefer']}', canmure = {$qs['canprepa']}, cod_alma='{$cod_alma}'";
                    $res = $db->query($sinsa);
                    if (!$res) {
                        guardar_error_log(__FILE__, $sinsa);
                        guardar_error_log(__FILE__, $db->error);
                        $db->rollback();
                        retornar(array('error' => true, 'mensaje' => "Falla 7 A"));
                    }
                } else {
                    $rs = $xs3->fetch_assoc();
                    $canPrevia = $rs['canmure'];
                    $tot = floatval($canPrevia) + floatval($qs['canprepa']);
                    $sinsa = "update stockart set  canmure = {$tot}, cod_alma='{$cod_alma}' where artrefer ='{$qs['artrefer']}' AND cod_alma='{$cod_alma}'";
                    $res = $db->query($sinsa);
                    if (!$res) {
                        guardar_error_log(__FILE__, $sinsa);
                        guardar_error_log(__FILE__, $db->error);
                        $db->rollback();
                        retornar(array('error' => true, 'mensaje' => "Falla 8"));
                    }
                }
            } else {
                guardar_error_log(__FILE__, $sin);
                guardar_error_log(__FILE__, $db->error);
                $db->rollback();
                retornar(array('error' => true, 'mensaje' => "Falla 9"));
            }
            guardar_movimiento_stock($db, $pedido, $qs['artrefer'], $qs['canprepa'], $muelle, 'UBMUS', $user, $fecingre, $horubi, $cod_alma, $qs['etnum']);
        } else {
            $dataArt = $xs2->fetch_assoc();
            $cCanti = $qs['canprepa'] + $dataArt['canti'];
            $sin = "update stockubi set canti = {$cCanti} where ubirefer = '{$muelle}' {$filtro_lote} and artrefer ='{$qs['artrefer']}' and etnum ='{$qs['etnum']}'";
            guardar_error_log(__FILE__, $sin);

            if ($db->query($sin)) {
                //                    registro stockart
                $sqlstu = "select * from stockart where artrefer ='{$qs['artrefer']}' AND cod_alma='{$cod_alma}' limit 1";
                $xs3 = $db->query($sqlstu);
                if ($xs3->num_rows == 0) {
                    $sinsa = "insert into stockart set artrefer ='{$qs['artrefer']}', canmure = {$qs['canprepa']}, cod_alma='{$cod_alma}'";
                    $res = $db->query($sinsa);
                    if (!$res) {
                        guardar_error_log(__FILE__, $sinsa);
                        guardar_error_log(__FILE__, $db->error);
                        $db->rollback();
                        retornar(array('error' => true, 'mensaje' => "Falla 7 B"));
                    }
                } else {
                    $rs = $xs3->fetch_assoc();
                    $canPrevia = $rs['canmure'];
                    $tot = $canPrevia + $qs['canprepa'];
                    $sinsa = "update stockart set  canmure = {$tot}, cod_alma='{$cod_alma}' where artrefer ='{$qs['artrefer']}' AND cod_alma='{$cod_alma}'";
                    $db->query($sinsa);
                }
            } else {
                guardar_error_log(__FILE__, $sinsa);
                guardar_error_log(__FILE__, $db->error);
                $db->rollback();
                retornar(array('error' => true, 'mensaje' => "Falla 10"));
            }
        }
    }
    $response["error"] = FALSE;
    $response['mensaje'] = "Pallet " . $pallet . " cerrado correctamente. ";
    $response['pedubicod'] = $id_cabecera; //se utiliza para trabajo piso recepcion
    $db->commit();
    $db->close();
    echo json_encode($response);
} catch (\Throwable $th) {
    $response["error"] = true;
    $response['mensaje'] = $th->getMessage();
    guardar_error_log(__FILE__, json_encode($_POST) . " " . $th->getMessage());
    $db->rollback();
    $db->close();
    echo json_encode($response);
}

function retornar($dato)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dato);
    exit;
}

function verificar_pallet($db, $pedido, $pallet)
{
    $sql = "SELECT pedubicod FROM pedubicab WHERE pedrefer = '$pedido' AND pal_desc = '$pallet'";
    $res = $db->query($sql);
    return $res->num_rows > 0 ? $res->fetch_assoc()['pedubicod'] : null;
}
