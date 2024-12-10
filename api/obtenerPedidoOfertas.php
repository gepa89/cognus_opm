<?php
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../utils/respuesta.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$id_pedido = $db->real_escape_string($_GET['id_pedido']);
if (empty($id_pedido)) {
    retorna_resultado(200, array("estado" => "atencion", "mensaje" => "Id pedido no enviado"));
}
$sql = "SELECT
	ofercab.*, proveedores.nombre
FROM
	ofercab
LEFT JOIN proveedores ON ofercab.codprove = proveedores.codprove
WHERE
	idoferta = '$id_pedido'
LIMIT 1 ";

//print $sql;
$query = $db->query($sql);
if ($query->num_rows == 0) {
    $db->close();
    $respuesta = array("estado" => "atencion", "mensaje" => "No se encontro pedido");
    retorna_resultado(200, $respuesta);
}
$cabecera = $query->fetch_assoc();

$sql = "SELECT material, matdesc,canped,unimed,precuni,prectotal,cencod,codalma FROM oferdet where idoferta = '$id_pedido'";
$query = $db->query($sql);
$detalles = $query->fetch_all();
$datos = array("cabecera" => $cabecera, "detalles" => $detalles);

$db->close();
$respuesta = array("estado" => "exito", "mensaje" => "", "datos" => $datos);
retorna_resultado(200, $respuesta);

?>