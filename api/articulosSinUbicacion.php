<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

guardar_info_log("obtener-Articulos", json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$pedrefer = htmlspecialchars(trim($_POST['pedrefer']));
$cod_alma = htmlspecialchars(trim($_POST['cod_alma']));

$sql = "SELECT
	pedrecab.pedrefer,
    pedredet.pedpos,
	pedredet.artrefer,
	arti.artdesc,
	Coalesce(artubiref.ubirefer,'-----') AS ubirefer
FROM
	pedrecab
	INNER JOIN pedredet ON pedrecab.pedrefer = pedredet.pedrefer
	INNER JOIN arti ON pedredet.artrefer = arti.artrefer
	LEFT JOIN artubiref ON pedredet.artrefer = artubiref.artrefer 
	AND artubiref.cod_alma = '$cod_alma' 
	AND artubiref.ubitipo in ('PI','PS') 
WHERE
	pedrecab.pedrefer = '$pedrefer' 
	AND artubiref.ubirefer IS NULL 
GROUP BY
	artrefer";
$query = $db->query($sql);
$resultados = array();
while ($fila = $query->fetch_assoc()) {
	array_push($resultados, $fila);
}
$respuesta = array("datos" => $resultados);
retorna_resultado(200, $respuesta);
?>