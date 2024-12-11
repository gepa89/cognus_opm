<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT
            posubi AS posicion,
            artrefer AS material,
            artdesc AS descripcion_material,
            cantiu AS cantidad_pedido,
            canubi AS cantidad_ubicada,
            canupen AS cantidad_pendiente,
            etnum AS multireferencia,
            usuario,
            fecha,
            hora,
            muelle
        FROM
            pedubidet
        Where pedubidet.pedubicod = '$pedido' ";
$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['cantidad_pedido'] = formatear_numero($dato['cantidad_pedido']);
    $dato['cantidad_ubicada'] = formatear_numero($dato['cantidad_ubicada']);
    $dato['cantidad_pendiente'] = formatear_numero($dato['cantidad_pendiente']);
    $dato['fecha_y_hora'] = $dato['fecha'] ? formatear_fecha($dato['fecha'], 'd/m/Y') . " " . $dato['hora'] : "";
}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
