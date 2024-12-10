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
            "artrefer = '" . $_POST['Art'] . "'," .
            "capacidad = '" . $_POST['Cap'] . "'," .    
            "fecaut = '" . $fecha ."',".
            "codalma = '" . $_POST['Alm'] . "' where 
            dimension = '" . $_POST['Dim'] . "' AND artrefer = '$artrefer'";
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
        
        $articulo = $_POST['Art'];
        $sql = "SELECT artrefer FROM arti WHERE artrefer = '{$articulo}'";
        $query = $db->query($sql);

        if ($query->num_rows == 0) {
            retorna_resultado(400, array('error' => "Articulo no encontrado"));
        }
        $resultado = $query->fetch_assoc()['artrefer'];
        
        $sq = "insert into " . $table . " ({$fields},fecaut) values ";
        $sq .= "('" . $_POST['Dim'] . "'," .
            "'" . $_POST['Art'] . "'," .
            "'" . $_POST['Cap'] . "'," .    
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