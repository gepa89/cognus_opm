<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . "/../../conect.php");
require_once(__DIR__ . "/../../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();

$cod_articulo = htmlspecialchars($_POST['cod_articulo'], ENT_QUOTES, 'UTF-8');
$ubicacion = htmlspecialchars($_POST['ubicacion'], ENT_QUOTES, 'UTF-8');
$mensaje = "";
try {
    $sql = "DELETE FROM artirepo WHERE artrefer='$cod_articulo' AND ubirefer='$ubicacion'";
    $res = $db->query($sql);
    $db->commit();
    $mensaje = "Eliminado";
    header('Content-type: application/json; charset=utf-8', true, 201);
} catch (\Throwable $th) {
    header('Content-type: application/json; charset=utf-8', true, 500);
    $mensaje = $th->getMessage();
}
$db->close();
echo json_encode(array("mensaje" => $mensaje));
