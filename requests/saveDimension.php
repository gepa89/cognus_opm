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
        $Ean = $_POST['id'];
        $artrefer = $_POST['Art'];
        $cod_alma = $_POST['Alm'];
        $sq = "update " . $table . " set " .
            "dimension = '" . $_POST['Dim'] . "'," .
            "dimdesc = '" . $_POST['Des'] . "'," .    
            "fecaut = '" . $fecha ."',".
            "codalma = '" . $_POST['Alm'] . "' where 
            dimension = '" . $_POST['Dim'] . "' ";
//                echo $sq;
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
        
        $dimension = $_POST['Dim'];
        $sql = "SELECT dimension FROM dimen WHERE dimension = '{$dimension}'";
        $query = $db->query($sql);

        
        
        $sq = "insert into " . $table . " ({$fields},fecaut) values ";
        $sq .= "('" . $_POST['Dim'] . "'," .
            "'" . $_POST['Des'] . "'," . 
            "'" . $_POST['Alm'] . "'," .
            "'" . $fecha . "')";


//        echo $sq;
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