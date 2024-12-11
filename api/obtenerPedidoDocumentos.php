<?php

require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../utils/respuesta.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$id_pedido = $db->real_escape_string($_GET['id_pedido']);
if (empty($id_pedido)) {
    retorna_resultado(200, array("estado" => "atencion", "mensaje" => "Id pedido no enviado"));
}
$sql = "SELECT pedidosincab.*, proveedores.nombre,SUM(pedidosindet.pretotal) as totalp, pedidosincab.situped as estado_pedido
FROM pedidosincab 
inner join pedidosindet on pedidosincab.docompra=pedidosindet.docompra
inner join proveedores on proveedores.codprove = pedidosincab.codprove 

WHERE pedidosincab.docompra = '$id_pedido' LIMIT 1";
//print $sql;
$query = $db->query($sql);  
if ($query->num_rows == 0) {
    $db->close();
    $respuesta = array("estado" => "atencion", "mensaje" => "No se encontro pedido");
    retorna_resultado(200, $respuesta);
}
$cabecera = $query->fetch_assoc();

$sql = "SELECT artrefer, artdesc,canti,unimed,caj_pallet,preuni,pretotal,volumen,pesbruto,cencod,cod_alma FROM pedidosindet where docompra = '$id_pedido'";
$query = $db->query($sql);
$detalles = $query->fetch_all();
$datos = array("cabecera" => $cabecera, "detalles" => $detalles);

$db->close();
$respuesta = array("estado" => "exito", "mensaje" => "", "datos" => $datos);
retorna_resultado(200, $respuesta);

?>