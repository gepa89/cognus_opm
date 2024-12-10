<?php
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../utils/respuesta.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$id_pedido = $db->real_escape_string($_GET['id_pedido']);
if (empty($id_pedido)) {
    retorna_resultado(200, array("estado" => "atencion", "mensaje" => "Id pedido no enviado"));
}
$sql = "SELECT
	contcab.*, proveedores.nombre
FROM
	contcab
LEFT JOIN proveedores ON contcab.codprove = proveedores.codprove
WHERE
	idcontra = '$id_pedido'
LIMIT 1 ";

//print $sql;
$query = $db->query($sql);
if ($query->num_rows == 0) {
    $db->close();
    $respuesta = array("estado" => "atencion", "mensaje" => "No se encontro pedido");
    retorna_resultado(200, $respuesta);
}
$cabecera = $query->fetch_assoc();

$sql = "SELECT
	material,
	matdesc,
	canped,
	unimed,
	precuni,
	prectotal,
	contdet.cencod,
	contdet.codalma,
	contdet.pedcontra,
	contdet.pedoferta
FROM
	contdet
INNER JOIN contcab on contdet.idcontra=contcab.idcontra 
WHERE
	contdet.idcontra = '$id_pedido'";
$query = $db->query($sql);
$detalles = $query->fetch_all();
$datos = array("cabecera" => $cabecera, "detalles" => $detalles);

$db->close();
$respuesta = array("estado" => "exito", "mensaje" => "", "datos" => $datos);
retorna_resultado(200, $respuesta);

?>