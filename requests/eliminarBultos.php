<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("eliminar asignacion pedido", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$bul = $db->real_escape_string($_POST['bul']);


$sql = "DELETE FROM bultos WHERE tipbul='$bul'";
print $sql;
guardar_error_log(__FILE__, $sql);
$query = $db->query($sql);
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con Exito"));
?>