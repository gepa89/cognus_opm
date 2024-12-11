<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$almacen = $_POST['codalma'] ?? null;
if ($almacen == null) {
    guardar_error_log("obtener terminales", json_encode($_POST));
    echo json_encode(array('almacenes' => []));
    exit;
}

$sql = "SELECT * FROM termi WHERE tipac = 'RECE' AND almrefer = '$almacen'";
$resultado = $db->query($sql);
$datos = $resultado->fetch_all(MYSQLI_ASSOC);
echo json_encode(array('almacenes' => $datos));
exit();