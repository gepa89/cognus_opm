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
        $Lot = $_POST['id'];
        $artrefer = $_POST['Art'];
        $cod_alma = $_POST['Alm'];
        $sq = "update " . $table . " set " .
            "artrefer = '" . $_POST['Art'] . "'," .
            "fecaduc = '" . $_POST['Fca'] . "'," .
            "fecinspe = '" . $_POST['Fin'] . "'," .
            "cod_alma = '" . $_POST['Alm'] . "' 
                where 
            artlote = '" . $_POST['Lot'] . "' AND artrefer = '$artrefer' AND cod_alma = '$cod_alma'";
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
        $sql = "SELECT artlotemar FROM arti WHERE artrefer = '{$articulo}'";
        $query = $db->query($sql);
        if ($query->num_rows == 0) {
            retorna_resultado(400, array('error' => "Articulo no encontrado"));
        }
        $resultado = $query->fetch_assoc()['artlotemar'];
        if (!($resultado === 'SI')) {
            retorna_resultado(400, array('error' => "Articulo sin Lote."));
        }
        $sq = "insert into " . $table . " ({$fields},fecre) values ";
        $sq .= "('" . $_POST['Lot'] . "'," .
            "'" . $_POST['Art'] . "'," .
            "'" . $_POST['Fca'] . "'," .
            "'" . $_POST['Fin'] . "'," .
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