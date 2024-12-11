<?php
require_once __DIR__ . "/../database/database.php";
require_once(__DIR__ . "/../utils/respuesta.php");

$db = MysqlDB::obtenerInstancia();
$id_pedido = $db->real_escape_string($_GET['id_pedido']);
if (empty($id_pedido)) {
    retorna_resultado(200, array("estado" => "atencion", "mensaje" => "Id pedido no enviado"));
}
$sql = "SELECT fletecab.*, proveedores.nombre, fletecab.situped as estado_pedido
FROM fletecab 
left join proveedores on proveedores.codprove = fletecab.codprove 
inner join fletedet on fletecab.idpedflete = fletedet.idpedflete
WHERE fletecab.idpedflete = '$id_pedido' LIMIT 1";
$query = $db->query($sql);
if ($query->num_rows == 0) {
    $db->close();
    $respuesta = array("estado" => "atencion", "mensaje" => "No se encontro pedido");
    retorna_resultado(200, $respuesta);
}
$cabecera = $query->fetch_assoc();

$sql = "SELECT canconte, tipconte,detconte,numconte,obseconte FROM fletedet where idpedflete = '$id_pedido'";
$query = $db->query($sql);
$detalles = $query->fetch_all();
$datos = array("cabecera" => $cabecera, "detalles" => $detalles);

$db->close();
$respuesta = array("estado" => "exito", "mensaje" => "", "datos" => $datos);
retorna_resultado(200, $respuesta);

?>