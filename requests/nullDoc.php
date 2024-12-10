<?php
require('../conect.php');
session_start();

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$doc = $_POST['doc'];
$tip = $_POST['tip'];
$usuario = $_SESSION['user'];


$sq = "select * from assig_ped where pedido = '{$doc}' and st = 0";
$rs = $db->query($sq);
if ($rs->num_rows > 0) {
    $err = 1;
    $msg = 'Pedido asignado a terminal.';
} else {
    switch ($tip) {
        case 'Re':
            $sqz = "update pedrecab set pedresitu = 'AN' where pedrefer = '{$doc}' ";
            //            echo $sq
            break;
        case 'Ex':
            $sqa = "update pedexcab set siturefe = 'AN', pedexcie = now(),pedexhorcie = now(),pedexrack = '{$usuario}' where pedexentre = '{$doc}' ";
            $sqz = "update pedexcabcajas set siturefe = 'AN' where pedexentre = '{$doc}' ";
            break;
    }
    //    echo $sqz;
    if ($db->query($sqz)) {
        $err = 0;
        $msg = 'Datos guardados.';
    } else {
        $err = 1;
        $msg = 'No se pudo guardar registro.';
    }
    if ($db->query($sqa)) {
        $err = 0;
        $msg = 'Datos guardados.';
    } else {
        $err = 1;
        $msg = 'No se pudo guardar registro.';
    }
}
echo json_encode(array('err' => $err, 'msg' => $msg));
