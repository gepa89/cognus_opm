<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$usuario = $_SESSION['user'];
if (!$usuario) {
    exit;
}

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
$fecha = date('Y-m-d');
$hora = date("H:i:s");
switch ($_POST['action']) {
    case 'upd':
        $Bul = $_POST['id'];
        $artrefer = $_POST['Art'];
        $cod_alma = $_POST['Alm'];
        $sq = "update " . $table . " set " .
            "nombre = '" . $_POST['Des'] . "'," .        
            "usermod = '" . $usuario . "'," .     
            "hormod = '" . $hora . "'," .     
            "fecmod = '{$fecha}' where 
            tipbul = '" . $_POST['Bul'] . "' ";
  //              echo $sq;
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            $err = 1;
  //          print_r($sq);
            $msg = 'No se pudo guardar registro.';
        }
        break;
    case 'add':
       

        
        $sq = "insert into " . $table . " ({$fields},coduser,fecha,hora) values ";
        $sq .= "('" . $_POST['Ped'] . "'," .
            "'" . $_POST['Obs'] . "'," .
            "'" .$usuario. "',".
            "'" .$fecha. "',".
            "'" . $hora . "' )";


//                echo $sq;
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            //            echo $db->error;
            $err = 1;
            $msg = 'No se pudo guardar registro.';
        }
        break;
}
$db->close();
echo json_encode(array('err' => $err, 'msg' => $msg));