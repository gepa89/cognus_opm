<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$proveedor = htmlspecialchars(trim($_GET['search']));
//guardar_info_log("obtener cliente", json_encode($_GET));
$sql = "SELECT * FROM proveedores WHERE codprove LIKE '%$proveedor%' OR nombre LIKE '%$proveedor%'";
$query = $db->query($sql);
//print $sql;
$resultados = array();
//print $resultados;
while ($fila = $query->fetch_assoc()) {
    $resultado = [
        'id' => $fila['codprove'],
        'text' => $fila['nombre']
    ];
    array_push($resultados, $resultado);
}
header('Content-type: application/json; charset=utf-8');
echo json_encode(array("results" => $resultados));
exit;
?>