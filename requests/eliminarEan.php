<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("eliminar asignacion pedido", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$cod_material = $db->real_escape_string($_POST['cod_material']);
$ean = $db->real_escape_string($_POST['ean']);
$cod_alma = $db->real_escape_string($_POST['cod_alma']);

$sql = "DELETE FROM artean WHERE ean='$ean' AND artrefer='$cod_material' AND cod_alma='$cod_alma'";
guardar_error_log(__FILE__, $sql);
$query = $db->query($sql);
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con Exito"));
?>