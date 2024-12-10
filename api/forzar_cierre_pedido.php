<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
header('Content-type: application/json; charset=utf-8');

guardar_info_log("forzar cierre pedidp", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$pedido = htmlspecialchars(trim($_POST['pedido']));
$articulo = htmlspecialchars(trim($_POST['articulo']));
$posicion = htmlspecialchars(trim($_POST['posicion']));
$respuesta = array("exito" => true, "mensaje" => "");

$sql = "UPDATE pedexdet SET expst=1 WHERE pedpos = '{$posicion}' AND pedexentre = '{$pedido}' AND artrefer = '{$articulo}'";
if ($db->query($sql)) {
    $db->commit();
} else {
    $db->rollback();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Error al guardar datos";
}

$db->close();
echo json_encode($respuesta);
exit;
?>