<?php

require('../conect.php');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$usuario = $_SESSION['user'];
$fecha = date("Y-m-d");
$hora = date("H:i:s");
$response = array();
$sq = "delete from assig_ped where pedido = '{$_POST['ped']}' and tercod = '{$_POST['term']}'";
//    echo $sq;
if ($db->query($sq)) {
    $err = false;
    $msg = 'Asignación Anulada.';
} else {
    $err = true;
    $msg = 'Ocurrió un error al anular asignación.';
}
$si = "insert into anul_asignaciones set pedido = '{$_POST['ped']}', tercod = '{$_POST['term']}', fecha = '{$fecha}', hora = '{$hora}', usuario = '{$usuario}', cod_alma = 'CD11'";
if ($db->query($si)) {
    $err = false;
    $msg = 'Asignación Anulada.';
} else {
    $err = true;
    $msg = 'Ocurrió un error al anular asignación.';
}
$sqxx = "delete from ped_multiref where terminal = '{$_POST['term']}' and codst = 0";
$db->query($sqxx);
echo json_encode(array('err' => $err, 'msg' => $msg));

exit();
