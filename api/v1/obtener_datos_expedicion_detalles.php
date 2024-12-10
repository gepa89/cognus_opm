<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");
require_once(__DIR__ . "/../../utils/sanitizador.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT DISTINCT
        detalles_pedido.pedpos AS posicion,
        articulo.artrefer AS material,
        articulo.artdesc AS descripcion_material,
        detalles_pedido.canpedi AS cantidad_pedido,
        detalles_pedido.canpendi AS cantidad_pendiente,
        detalles_pedido.canprepa AS cantidad_preparada,
        detalles_pedido.fechcie AS fecha,
        detalles_pedido.horcie AS hora,
        detalles_pedido.usuario AS usuario
        FROM pedexdet detalles_pedido
        INNER JOIN arti articulo on detalles_pedido.artrefer=articulo.artrefer 
        WHERE detalles_pedido.pedexentre = '$pedido'";
$datos = $sqlBuilder->rawQuery($sql)->getAll();
$lista_materiales = [];
foreach ($datos as &$dato) {
    $lista_materiales[] = $dato['material'];
    $dato['zonas'] = "";
    $dato['fecha_y_hora'] = $dato['fecha'] ? formatear_fecha($dato['fecha']) . " " . $dato['hora'] : "";
    $dato['descripcion_material'] = limpiarCadena($dato['descripcion_material']);
    $dato['material'] = limpiarCadena($dato['material']);
    $dato['cantidad_pedido'] = formatear_numero($dato['cantidad_pedido']);
    $dato['cantidad_pendiente'] = formatear_numero($dato['cantidad_pendiente']);
    $dato['cantidad_preparada'] = formatear_numero($dato['cantidad_preparada']);
    $dato['fecha'] = formatear_fecha($dato['fecha']);
}
setear_zonas($datos, $lista_materiales);
$respuesta = ['detalles' => $datos];
$conn->close();



retorna_resultado(200, $respuesta);


function setear_zonas(&$datos, $lista_materiales)
{
    $materiales_cadena = implode("','", $lista_materiales);
    $conn = MysqlDB::obtenerInstancia();
    $sqlBuilder = new MySqlQuery($conn);
    $sql = "SELECT
            articulo.artrefer AS material,
            GROUP_CONCAT(DISTINCT mapa_ubicacion.zoncodpre SEPARATOR ' ') AS zonas
            FROM arti articulo
            left join artubiref referencia_ubicacion on
                articulo.artrefer = referencia_ubicacion.artrefer
            left JOIN ubimapa mapa_ubicacion on
                referencia_ubicacion.ubirefer = mapa_ubicacion.ubirefer
            WHERE articulo.artrefer IN ('$materiales_cadena') GROUP BY articulo.artrefer;";
    $resultados = $sqlBuilder->rawQuery($sql)->getAll();
    foreach ($resultados as $resultado) {
        array_walk($datos, function (&$dato) use ($resultado) {
            if ($dato['material'] === $resultado['material']) {
                $dato['zonas'] = $resultado['zonas'];
            }
        });
    }
}