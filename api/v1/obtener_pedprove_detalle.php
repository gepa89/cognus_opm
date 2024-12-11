<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT 
        pedidosindet.posnr,
        pedidosindet.artrefer,
        pedidosindet.artdesc,
        pedidosindet.unimed,
        pedidosindet.canti,
        pedidosindet.preuni,
        (pedidosindet.canti * pedidosindet.preuni) AS pretotal,
        pedidosindet.cencod,
        pedidosindet.cod_alma,
        pedidosindet.volumen

        FROM pedidosindet  
        WHERE pedidosindet.docompra = '$pedido' 
        ORDER BY posnr ASC";

//print $sql;
$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['canti'] = formatear_numero($dato['canti']);
    $dato['preuni'] = formatear_numero($dato['preuni']);
    $dato['pretotal'] = formatear_numero($dato['pretotal']);
 
}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
