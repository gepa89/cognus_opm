<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
header('Content-type: application/json; charset=utf-8');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$ean = htmlspecialchars($_POST['ean'], ENT_QUOTES, 'UTF-8');
$ubicacion = htmlspecialchars($_POST['ubicacion'], ENT_QUOTES, 'UTF-8');
$sql = "SELECT SUM(stockubi.canti) as cantidad, stockubi.ubirefer as ubicacion,stockubi.artrefer as articulo, arti.artdesc as descripcion
        FROM stockubi 
        INNER JOIN arti on arti.artrefer = stockubi.artrefer 
        INNER JOIN artean on artean.artrefer=arti.artrefer
        WHERE artean.ean = '$ean' and stockubi.ubirefer='$ubicacion' 
        GROUP BY stockubi.artrefer,stockubi.artrefer ";
guardar_error_log($sql,"");
$res = $db->query($sql);
if (!$res) {
    $error = $db->error;
    $db->close();
    echo json_encode(['mensaje' => "Error al ejecutar $error"], 500);
    exit;
}

$detalles = $res->fetch_object();
$detalles->cantidad = doubleval($detalles->cantidad);
$db->close();
$mensaje = $res->num_rows == 0 ? "Articulo no encontrado en la ubicacion" : "";
echo json_encode(['existe_articulo' => $res->num_rows > 0, "detalles" => $detalles, 'mensaje' => $mensaje], 200);
