<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$buscar = htmlspecialchars(trim($_GET['search']));
guardar_info_log("obtener clase documento", json_encode($_GET));
$sql = "SELECT * FROM clasdoc WHERE pedclase LIKE '%$buscar%' OR descripcion LIKE '%$buscar%'";
$query = $db->query($sql);
$resultados = array();
while ($fila = $query->fetch_assoc()) {
    $resultado = [
        'id' => $fila['pedclase'],
        'text' => $fila['pedclase'] . " - " . $fila['descripcion']
    ];
    array_push($resultados, $resultado);
}
header('Content-type: application/json; charset=utf-8');
echo json_encode(array("results" => $resultados));
exit;
?>