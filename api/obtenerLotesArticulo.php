<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("obtener-lotes-articulo", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$articulo = htmlspecialchars(trim($_POST['articulo']));
$cod_alma = htmlspecialchars(trim($_POST['cod_alma']));

$sql = "SELECT artlote, fecaduc FROM loteart WHERE artrefer = '$articulo' AND cod_alma = '$cod_alma'";
$query = $db->query($sql);
$resultados = array();
while ($fila = $query->fetch_assoc()) {
    array_push($resultados, $fila);
}
$respuesta = array("datos" => $resultados);
retorna_resultado(200, $respuesta);
?>