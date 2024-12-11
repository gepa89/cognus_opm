<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT 
        pedidosindet.pedpos,
        pedidosindet.artrefer,
        pedidosindet.artdesc,
        pedidosindet.unimed,
        pedidosindet.canti,
        pedidosindet.preuni,
        pedidosindet.pretotal,
        pedidosindet.canti,
        pedidosindet.canti,
        pedidosindet.canti,
        
    FROM 
        pedidosindet detalles_pedido
   
    WHERE 
        pedidosindet.docompra = '$pedido' 
        ORDER BY pedpos ASC";

$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['canti'] = formatear_numero($dato['canti']);
    $dato['preuni'] = formatear_numero($dato['preuni']);
    $dato['pretotal'] = formatear_numero($dato['pretotal']);

}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
