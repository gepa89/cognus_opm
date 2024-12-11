<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$material = $db->real_escape_string($_POST['material']);
$presentacion = $db->real_escape_string($_POST['presentacion']);
$cod_alma = $db->real_escape_string($_POST['cod_alma']);

$sql = "DELETE FROM artipresen WHERE artrefer='$material' AND preseref='$presentacion' AND cod_alma='$cod_alma'";
$query = $db->query($sql);
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con exito"));
?>