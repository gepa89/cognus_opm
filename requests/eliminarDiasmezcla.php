<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("eliminar dias para mesclar el material", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$cod_material = $db->real_escape_string($_POST['cod_material']);
$Art = $db->real_escape_string($_POST['Art']);
$Dia= $db->real_escape_string($_POST['$Dia']);
$cod_alma = $db->real_escape_string($_POST['cod_alma']);

$sql = "DELETE FROM diasmezcla WHERE artrefer='$Art' AND codalma='$cod_alma'";
guardar_error_log(__FILE__, $sql);
$query = $db->query($sql);
print $sql;
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con Exito"));
?>