<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
$fecha = date('Y-m-d');
switch ($_POST['action']) {
    case 'upd':
        $Id = $_POST['Id'];
        $Sec = $_POST['Sec'];
        $Sed = $_POST['Sed'];
        $Sus = $_POST['Sus'];
       
        $sq = "update " . $table . " set " .
            "seccion = '" . $_POST['Sec']. "'," .
            "secciondes = '" . $_POST['Sed']. "'," .    
            "subseccion = '" . $_POST['Sus'] . "' 
                where 
            id = '$Id'";
  //              echo $sq;
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            $err = 1;
            print_r($sq);
            $msg = 'No se pudo guardar registro.';
        }
        break;
    case 'add':
        
        $sq = "insert into " . $table . " ({$fields},fecre) values ";
        $sq .= "('" . $_POST['Sec'] . "'," .
            "'" . $_POST['Sus'] . "'," .
            "'" . $_POST['Sed'] . "'," .    
            "'" . $fecha . "')";


    //            echo $sq;
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