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
            "docompra = '" . $_POST['Doc'] . "'," .     
            "tipconte = '" . $_POST['Tco'] . "'," .  
            "numconte = '" . $_POST['Nco'] . "'," .  
            "observacion = '" . $_POST['Obs'] . "'," .      
            "canti = '" . $_POST['Can'] . "'," .       
            "usermod = '" . $usuario . "'," .     
            "hormod = '" . $hora . "'," .     
            "fecmod = '{$fecha}' where 
            docompra = '" . $_POST['Doc'] . "' ";
                echo $sq;
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
       

        
        $sq = "insert into " . $table . " ({$fields},usuario,fecre,horcre) values ";
        $sq .= "('" . $_POST['Doc'] . "'," .
            "'" . $_POST['Tco'] . "'," .
            "'" . $_POST['Nco'] . "'," .
            "'" . $_POST['Can'] . "'," .    
            "'" . $_POST['Obs'] . "'," .       
            "'" .$usuario. "',".
            "'" .$fecha. "',".
            "'" . $hora . "' )";


                echo $sq;
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