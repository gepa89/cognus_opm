<?php
require('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);

//crear hash del estante
$datainput = $_POST['muelle'];
$matIn = $_POST['material'];
$destino = $_POST['destino'];
$cant = $_POST['cant'];
$modo = $_POST['modo'];
$cod_alma = $_POST['cod_almacen'];
$sqCnd = '';
$sqall = '';
//$db->autocommit(false);
if ($matIn != 'Todo') {
    $sqall = "and b.artrefer = '{$matIn}'";
}

$response = array();

$ckubi = "select * from ubimapa where ubirefer = '{$destino}' AND cod_alma = '{$cod_alma}'";
$rubi = $db->query($ckubi);
//echo $ckubi;
//Si es que la ubicación de destino existe
if ($rubi->num_rows > 0) {
    $rx = $rubi->fetch_assoc();
    $sigo = true;
    //    var_dump($rx);
    if ($rx['ubitipo'] == 'PI') {
        $sq = "select * from artubiref where artrefer = '{$_POST['material']}' and ubitipo = 'PI' AND cod_alma = '{$cod_alma}'";
        //        echo $sq;
        $rart = $db->query($sq)->fetch_assoc();
        $valTxt = $rart['ubirefer'] . "!=" . $_POST['destino'];
        //        echo $valTxt;
        if ($rart['ubirefer'] != $_POST['destino']) {
            $sigo = false;
        }
    }
    if ($sigo) {
        if ($modo == 0) { //se hace todo con el muelle como clave..
            $lblb = "muelle";
            $qry = "SELECT a.muelle, a.pedrefer, b.artrefer, b.canti, c.artdesc FROM pedmuelle a 
                   inner join stockubi b on a.muelle = b.ubirefer 
                   inner join arti c on b.artrefer = c.artrefer               
                   where 1=1 and a.muelle = '{$datainput}' {$sqall} and a.pedstatus = 0";
            $cc = 0;
            $r1 = $db->query($qry);
            while ($row = $r1->fetch_assoc()) {
                $muelle = $row['muelle'];
                $pedido = $row['pedrefer'];
                $material = $row['artrefer'];
                $sq = "select artrefer, candispo, canmure from stockart where artrefer = '{$material}' AND cod_alma='{$cod_alma}'";
                $rs = $db->query($sq);
                $rx = $rs->fetch_assoc();
                $cantDipo = (int) $rx['candispo'];
                $cantMuelle = (int) $rx['canmure'];

                $val2stract = 0;
                if ($cant > 0) {
                    $val2stract = (int) $cant;
                    $val4stockubi = abs((int) $row['canti'] - (int) $cant);
                    $stU = ", canti = {$val2stract}";
                } else {
                    $stU = '';
                    $val2stract = (int) $row['canti'];
                }


                //        Actualizo stockubi
                // el registro del muelle se vuelve el registro con la nueva ubicacion
                $sq1 = "update stockubi set ubirefer = '{$destino}' {$stU} where artrefer = '{$material}' and ubirefer = '{$muelle}'";
                if ($db->query($sq1)) {
                    if ($cant < $row['canti']) {
                        //                    creo un nuevo registro para el muelle con lo que no se movio
                        $sIn = "insert into stockubi set ubirefer = '{$muelle}', artrefer = '{$material}', canti = {$val4stockubi}, etnum = '{$mrefAssi}'";
                        //                        echo $sIn;
                        if (!$db->query($sIn)) { //si no se pudo crear, puede que ya exista, entonces actualizo
                            $sq1 = "update stockubi set ubirefer = '{$muelle}', canti = {$val4stockubi} where artrefer = '{$material}' and ubirefer = '{$muelle}'";
                            $db->query($sq1);
                        }

                    }
                    $val2stract = 0;
                    if ($cant > 0) {
                        $val2stract = (int) $cant;
                    } else {
                        $val2stract = (int) $row['canti'];
                    }
                    $cantDipo = $cantDipo + $val2stract;
                    $cantMuelle = $cantMuelle - $val2stract;

                    //            cambio el estado de la ubicacion
                    $sq2 = "update ubimapa set ubisitu = 'LL' where ubirefer = '{$destino}'";
                    $db->query($sq2);
                    //            actaulizo la tabla stock
                    $sq3 = "update stockart set candispo = {$cantDipo}, canmure = {$cantMuelle} where artrefer = '{$material}' AND cod_alma='{$cod_alma}'";
                    //                    echo $sq3;
                    $db->query($sq3);
                    $sq7 = "update serati set serubic = '{$destino}' where artrefer = '{$material}' and artped = '{$pedido}' ";
                    $db->query($sq7);
                    $response["error"] = FALSE;
                    $response['mensaje'] = '';
                } else {

                    $response["error"] = TRUE;
                    $response['mensaje'] = "No se pudo actualizar ubicacion";
                }
            }
            $qry = "SELECT a.muelle, a.pedrefer, b.artrefer, b.canti, c.artdesc FROM pedmuelle a 
                   inner join stockubi b on a.muelle = b.ubirefer 
                   inner join arti c on b.artrefer = c.artrefer
                   where muelle = '{$muelle}' and pedstatus = 0";
            //     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
            $cc = 0;
            $rx1 = $db->query($qry);
            if ($rx1->num_rows == 0) {
                $sq4 = "update pedmuelle set pedstatus = 1 where muelle = '{$muelle}' and pedrefer = '{$pedido}'";
                $db->query($sq4);
                $sq5 = "update pedrecab set pedresitu = 'UB' where pedrefer = '{$pedido}'";
                $db->query($sq5);
                $sq6 = "update ubimapa set ubisitu = 'VA' where ubirefer = '{$muelle}'";
                $db->query($sq6);
            }
        } else { //se hace todo con la mr como clave
            $sqCnd = " and d.etnum = '{$datainput}'";
            $lblb = "Multi referencia";
            //            echo $lblb;
            $qry = "SELECT a.muelle,b.etnum, a.pedrefer, b.artrefer, b.canti, c.artdesc FROM pedmuelle a 
                   inner join stockubi b on a.muelle = b.ubirefer 
                   inner join arti c on b.artrefer = c.artrefer               
                   where 1=1 and b.etnum = '{$datainput}' {$sqall} and a.pedstatus = 0";
            $cc = 0;
            $r1 = $db->query($qry);
            while ($row = $r1->fetch_assoc()) {
                $muelle = $row['muelle'];
                $mrefAssi = $row['etnum'];
                $pedido = $row['pedrefer'];
                $material = $row['artrefer'];
                $codalma = $row['artrefer'];

                $sq = "select artrefer, candispo, canmure from stockart where artrefer = '{$material}'";
                $rs = $db->query($sq);
                $rx = $rs->fetch_assoc();
                $cantDipo = (int) $rx['candispo'];
                $cantMuelle = (int) $rx['canmure'];


                $val2stract = 0;
                if ($cant > 0) {
                    $val2stract = (int) $cant;
                    $val4stockubi = abs((int) $row['canti'] - (int) $cant);
                    $stU = ", canti = {$val2stract}";
                } else {
                    $stU = '';
                    $val2stract = (int) $row['canti'];
                }


                //        Actualizo stockubi
                // el registro del muelle se vuelve el registro con la nueva ubicacion
                $sq1 = "update stockubi set ubirefer = '{$destino}' {$stU} where artrefer = '{$material}' and ubirefer = '{$muelle}'";
                //                echo $sq1;
                if ($db->query($sq1)) {
                    if ($cant < $row['canti']) {
                        //                    creo un nuevo registro para el muelle con lo que no se movio
                        $sIn = "insert into stockubi set ubirefer = '{$muelle}', artrefer = '{$material}', canti = {$val4stockubi}, etnum = '{$mrefAssi}'";
                        //                        echo $sIn;
                        if (!$db->query($sIn)) { //si no se pudo crear, puede que ya exista, entonces actualizo
                            $sq1 = "update stockubi set ubirefer = '{$muelle}', canti = {$val4stockubi} where artrefer = '{$material}' and ubirefer = '{$muelle}'";
                            $db->query($sq1);
                        }

                    }
                    $cantDipo = $cantDipo + $val2stract;
                    $cantMuelle = $cantMuelle - $val2stract;
                    //                $ckUbi = "update ubimapa set ubisitu = 'LL' where ubirefer = '{$destino}'"
                    //            cambio el estado de la ubicacion
                    $sq2 = "update ubimapa set ubisitu = 'LL' where ubirefer = '{$destino}'";
                    $db->query($sq2);
                    //            actaulizo la tabla stock
                    $sq3 = "update stockart set candispo = {$cantDipo}, canmure = {$cantMuelle} where artrefer = '{$material}' AND cod_alma = '{$cod_alma}'";
                    //                    echo $sq3;
                    $db->query($sq3);
                    $sq7 = "update serati set serubic = '{$destino}' where artrefer = '{$material}' and artped = '{$pedido}' ";
                    $db->query($sq7);
                    $response["error"] = FALSE;
                    $response['mensaje'] = '';
                } else {

                    $response["error"] = TRUE;
                    $response['mensaje'] = "No se pudo actualizar ubicacion";
                }
            }
            $qry = "SELECT distinct a.muelle, a.pedrefer, b.artrefer, b.canti, c.artdesc FROM pedmuelle a 
                   inner join stockubi b on a.muelle = b.ubirefer 
                   inner join arti c on b.artrefer = c.artrefer
                   where muelle = '{$muelle}' and pedstatus = 0";
            //     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
            $cc = 0;
            $rx1 = $db->query($qry);
            if ($rx1->num_rows == 0) {
                $sq4 = "update pedmuelle set pedstatus = 1 where muelle = '{$muelle}' and pedrefer = '{$pedido}'";
                $db->query($sq4);
                $sq5 = "update pedrecab set pedresitu = 'UB' where pedrefer = '{$pedido}'";
                $db->query($sq5);
                $sq6 = "update ubimapa set ubisitu = 'VA' where ubirefer = '{$muelle}' AND cod_alma = '{$cod_alma}'";
                $db->query($sq6);
            }
        }
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "Ubicación destino no corresponde";
    }
} else {

    $response["error"] = TRUE;
    $response['mensaje'] = "Ubicación destino no existe";
}
echo json_encode($response);
include '/var/www/html/closeconn.php';