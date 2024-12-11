<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("eliminar asignacion pedido", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$id = htmlspecialchars(trim($_POST['id']));

$sql = "DELETE FROM assig_ped WHERE id=$id";
$query = $db->query($sql);
$db->close();
retorna_resultado(200, array("estado" => "success", "mensaje" => "Eliminado con Exito"));
?>