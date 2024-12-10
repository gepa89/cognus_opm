<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT 
        
        articulo.artrefer AS material,
        articulo.artdesc AS descripcion_material,
        detalles_pedido.canexp AS cantidad_excedente,
        detalles_pedido.canpedi AS cantidad_pedido,
        detalles_pedido.canfaltante AS cantidad_faltante,
        observaciones_recepcion.usuario AS usuario,
        observaciones_recepcion.fecha_creacion AS fecha,
        observaciones_recepcion.codigo_comentario AS codobse,
        observaciones_recepcion.comentario AS obse1
        FROM pedredet detalles_pedido
        INNER JOIN arti articulo on detalles_pedido.artrefer=articulo.artrefer 
        INNER JOIN observaciones_recepcion on detalles_pedido.pedrefer=observaciones_recepcion.codigo_pedido and detalles_pedido.artrefer=observaciones_recepcion.codigo_articulo
        WHERE detalles_pedido.pedrefer = '$pedido' 
        ORDER BY detalles_pedido.pedpos ASC";
$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['cantidad_pedido'] = formatear_numero($dato['cantidad_pedido']);
    $dato['cantidad_excedente'] = formatear_numero($dato['cantidad_excedente']);
    $dato['cantidad_faltante'] = formatear_numero($dato['cantidad_faltante']);
    $dato['fecha'] = $dato['fecha'] ? formatear_fecha($dato['fecha'],'d/m/Y H:i:s') : "";
}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
