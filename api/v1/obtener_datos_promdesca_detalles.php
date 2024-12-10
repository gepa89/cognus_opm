<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT 
        tip_conte as tip_conte,
        num_conte as num_conte,
        can_bultos as can_bultos,
        num_interno as num_interno,
        num_poliza as num_poliza,
        obse1 as obse1,
        metros_cub as metros_cub,
        prioridad as prioridad
        FROM oc_finiquitada 
        WHERE oc_finiquitada.pedrefer = '$pedido' 
        ORDER BY tip_conte ASC";
$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['cantidad_pedido'] = formatear_numero($dato['cantidad_pedido']);
    $dato['cantidad_excedente'] = formatear_numero($dato['cantidad_excedente']);
    $dato['cantidad_pendiente'] = formatear_numero($dato['cantidad_pendiente']);
    $dato['cantidad_preparada'] = formatear_numero($dato['cantidad_preparada']);
    $dato['fecha'] = $dato['fecha'] ? formatear_fecha($dato['fecha'],'d/m/Y H:i:s') : "";
}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
