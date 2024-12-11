<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("eliminar asignacion pedido", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$cod_material = $db->real_escape_string($_POST['cod_material']);
$dim = $db->real_escape_string($_POST['dim']);

$cod_alma = $db->real_escape_string($_POST['Alm']);

$sql = "DELETE FROM dimen WHERE dimension='$dim'";
guardar_error_log(__FILE__, $sql);
$query = $db->query($sql);
print $sql;
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con Exito"));
?>