<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$cod_articulo = $db->real_escape_string($_GET['cod_articulo']);

guardar_info_log("obtener articulo", json_encode($_GET));

$sql = "SELECT artrefer, artdesc, unimed, costo FROM arti WHERE artrefer='$cod_articulo' LIMIT 1";

$query = $db->query($sql);
//print $sql;
if (!$query) {
    guardar_error_log("obtener documentos", $db->error);
    $db->close();
    header('Content-type: application/json; charset=utf-8', true, 400);
    echo json_encode(array("mensaje" => "Ocurrio un error"));
    exit;
}
$respuesta = array("estado" => "exito", "mensaje" => "", "dato" => null);

if ($query->num_rows == 0) {
    $db->close();
    $respuesta['estado'] = "alerta";
    $respuesta['mensaje'] = "No se encontro articulo";
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($respuesta);
    exit;
}

$respuesta['dato'] = $query->fetch_assoc();
$db->close();
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit;
?>