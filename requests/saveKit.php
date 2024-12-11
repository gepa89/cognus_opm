<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
$fecha = date('Y-m-d-h:m:s');
switch ($_POST['action']) {
    case 'upd':
        $Art = $_POST['id'];
        $artrefer = $_POST['Art'];
        $cod_alma = $_POST['Alm'];
        $sq = "update " . $table . " set " .
            "artrefer = '" . $_POST['Art'] . "'," .
            
            "artrefkit = '" . $_POST['Com'] . "'," .
            "deskit = '" . $_POST['Dec'] . "'," .    
            "cod_alma = '" . $_POST['Alm'] . "' where 
            artrefer = '" . $_POST['Art'] . "' AND artrefer = '$artrefer' AND cod_alma = '$cod_alma'";
        //        echo $sq;
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
        $articulocom = $_POST['Com'];
        
        $sql = "SELECT artmarkit FROM arti WHERE artrefer = '{$articulocom}'";
        $query = $db->query($sql);
        if ($query->num_rows == 0) {
            retorna_resultado(400, array('error' => "Articulo no encontrado"));
        }
        $resultado = $query->fetch_assoc()['artmarkit'];
        if (!($resultado === 'SI')) {
            retorna_resultado(400, array('error' => "Componente no corresponde a Kits."));
        }
        $sq = "insert into " . $table . " ({$fields},fecre) values ";
        $sq .= "('" . $_POST['Art'] . "'," .
            
            
            "'" . $_POST['Com'] . "'," .
            "'" . $_POST['Dec'] . "'," .
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