<?php
require('../conect.php');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
$db->begin_transaction();
$ajUbi = $_POST['ajUbi'];
$ajArt = $_POST['ajArt'];
$ajCPres = floatval($_POST['ajCPres']);
$ajPres = floatval($_POST['ajPres']);
$ajUni = floatval($_POST['ajUni']);
$ajSAct = $_POST['ajSAct'];
$ajTip = $_POST['ajTip'];
$codalma = $_POST['codalma'];
$ajMot = $_POST['ajMot'];
$ajDir = "" . $_POST['ajDir'];
$ajAlm = $_POST['codalma'];
$lote = $_POST['lote'];
$fecha_ajuste = $_POST['fecha_ajuste'];
$ajdes = 'Ajuste';
if ($ajDir == "") {
    $sqIn1 = "select ensal from artmov where movref='{$ajTip}' limit 1";
    //    echo $sqIn1;
    $rs = $db->query($sqIn1);
    $ax = $rs->fetch_assoc();
    $ajDir = $ax['ensal'];
}


//var_dump($ajDir);
//
$ajPres = $ajPres * $ajCPres;
if ($ajDir == 'out') {
    $ajPres = (-1) * $ajPres;
    $ajUni = (-1) * $ajUni;
}

$flg = $cont = 0;

$total = ($ajPres) + $ajUni;
//        echo $total." - ".$ajSAct;



//if($ajDir == 'out'){
//    $ajdes = 'Ajuste-';
//}else{
//    $ajdes = 'Ajuste+';
//}
if (abs($total) > $ajSAct && $ajDir == 'out') {
    $flg = 1;
}
if ($flg == 0) {

    $squbi = "select ubirefer, ubitipo, ubisitu from ubimapa where ubirefer = '{$ajUbi}' and cod_alma = '{$codalma}'";
    $rubi = $db->query($squbi);
    $ubic = $rubi->fetch_assoc();
    if ($rubi->num_rows > 0) {

        if ($ubic['ubitipo'] == 'PI') {
            $sel = "select * from artubiref where ubirefer = '{$ajUbi}' and artrefer = '{$ajArt}' and cod_alma = '{$codalma}'";
            $rx = $db->query($sel);
            if ($rx->num_rows == 0) {
                $err = 1;
                $msg = "Ubicación PI no corresponde al material.";
                echo json_encode(array('err' => $err, 'msg' => $msg));
                die();
            }
        }


        $sql = "";
        if (!empty($lote)) {
            $filtro_lote = "";
            $sql = "SELECT loteart.artlote,stockubi.ubirefer , COALESCE(SUM(stockubi.canti),0) as cantidad 
            FROM loteart 
            LEFT OUTER JOIN stockubi ON stockubi.artlote = loteart.artlote
            WHERE loteart.artrefer = '$ajArt'
            AND loteart.cod_alma = '$codalma'
            AND stockubi.ubirefer = '$ajUbi' 
            AND loteart.artlote = '$lote' 
            GROUP BY loteart.artlote, stockubi.ubirefer";
        } else {
            $sql = "SELECT ubirefer , COALESCE(SUM(stockubi.canti),0) as cantidad 
            FROM  stockubi
            WHERE artrefer = '$ajArt'
            AND cod_alma = '$codalma'
            AND ubirefer = '$ajUbi'";
        }

        $resultado = $db->query($sql)->fetch_assoc();
        $cantidad = floatval($resultado['cantidad']) + $total;
        if ($cantidad < 0) {
            $db->rollback();
            $db->close();
            echo json_encode(array('err' => 1, 'msg' => "Cantidad de ajuste Negativo invalido"));
            exit;
        }
        //registro en stockubi
        $sqIn1 = "insert into stockubi set ubirefer = '{$ajUbi}',
        canti = {$total},
        artrefer = '{$ajArt}',
        cod_alma = '{$codalma}',  
        etnum = '{$ajdes}',
        artlote = '{$lote}',
        fecingre = '{$fecha_ajuste}'
        ";
        //    echo $sqIn1;
        if ($db->query($sqIn1)) {
            $sq = "select candispo from stockart where artrefer = '{$ajArt}' and cod_alma = '{$codalma}'";
            $rs = $db->query($sq);
            $cantD = $rs->fetch_assoc();
            $totalSA = $total + floatval($cantD['candispo']);
            if ($cantD) {
                $sqx = "update stockart set candispo = '{$totalSA}' where artrefer = '{$ajArt}' and cod_alma = '{$codalma}'";
                if ($db->query($sqx)) {
                    $ins = "insert into ajuart set 
                        ubirefer = '{$ajUbi}',
                        artrefer = '{$ajArt}',
                        almrefer = '{$ajAlm}',
                        posicion = '1',
                        stock = '{$ajSAct}',
                        canajuscaj = '{$ajPres}',
                        canajusun = '{$ajUni}',
                        ajuref = '{$ajTip}',
                        movref = '{$ajMot}',
                        fecha = now(),
                        hora = now(),
                        usuario = '" . $_SESSION['user'] . "'";
                    if ($db->query($ins)) {

                        $err = 0;
                        $msg = "Ajuste registrado.";
                    } else {
                        $db->rollback();
                        $db->close();
                        echo json_encode(array('err' => 1, 'msg' => "Error al registrar ajuste"));
                        exit;
                    }
                } else {
                    $db->rollback();
                    $db->close();
                    echo json_encode(array('err' => 1, 'msg' => "Error al registrar ajuste 2"));
                    exit;
                }
            } else {
                $sqx = "insert stockart set candispo = {$totalSA}, artrefer = '{$ajArt}', cod_alma = '{$codalma}'";
                                echo $sqx;
                if ($db->query($sqx)) {
                    $ins = "insert into ajuart set 
                        ubirefer = '{$ajUbi}',
                        artrefer = '{$ajArt}',
                        almrefer = '{$ajAlm}',
                        posicion = '1',
                        stock = '{$ajSAct}',
                        canajuscaj = '{$ajPres}',
                        canajusun = '{$ajUni}',
                        ajuref = '{$ajTip}',
                        movref = '{$ajMot}',
                        fecha = now(),
                        hora = now(),
                        usuario = '" . $_SESSION['user'] . "'";
                    if ($db->query($ins)) {
                        $err = 0;
                        $msg = "Ajuste registrado.";
                    } else {
                        $db->rollback();
                        $db->close();
                        echo json_encode(array('err' => 1, 'msg' => "Error al registrar ajuste 3"));
                        exit;
                    }
                } else {
                    $db->rollback();
                    $db->close();
                    echo json_encode(array('err' => 1, 'msg' => "Error al registrar ajuste 4"));
                    exit;
                }
            }
        } else {
            $db->rollback();
            $db->close();
            echo json_encode(array('err' => 1, 'msg' => "No se pudo registrar ajuste"));
            exit;
        }

        $sql = "SELECT SUM(canti) as cantidad FROM stockubi WHERE ubirefer = '{$ajUbi}' and artrefer = '{$ajArt}' and cod_alma = '{$codalma}'";
        $res = $db->query($sql);
        if (!$res) {
            $db->rollback();
            $db->close();
            echo json_encode(array('err' => 1, 'msg' => "No se pudo obtener cantidad stockubi"));
            exit;
        }
        $cantidad = $res->fetch_object()->cantidad;
        $estado = "LL";
        if ($cantidad == 0) {
            $estado = "VA";
        }
        $squbi = "update ubimapa set ubisitu = '$estado' where ubirefer = '{$ajUbi}' and cod_alma = '{$codalma}'";
        $db->query($squbi);
        if (!$res) {
            $db->rollback();
            $db->close();
            echo json_encode(array('err' => 1, 'msg' => "No se pudo actualizar ubimapa"));
            exit;
        }
    } else {
        $db->rollback();
        $db->close();
        echo json_encode(array('err' => 1, 'msg' => "Ubicación no existe."));
        exit;
    }
} else {
    $db->rollback();
    $db->close();
    echo json_encode(array('err' => 1, 'msg' => "Ajuste negativo no puede superar al Stock Actual."));
    exit;
}
//$db->rollback();
$db->commit();
$db->close();
echo json_encode(array('err' => $err, 'msg' => $msg));
