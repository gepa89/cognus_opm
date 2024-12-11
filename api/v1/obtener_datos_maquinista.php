<?php
require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");
require_once(__DIR__ . "/../../utils/sanitizador.php");

$conn = MysqlDB::obtenerInstancia();
$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');
$fecha_desde = htmlspecialchars($_GET['fecha_desde'] ?? "", ENT_QUOTES, 'UTF-8');
$fecha_hasta = htmlspecialchars($_GET['fecha_hasta'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$clases_documento = htmlspecialchars($_GET['clase_documento'] ?? "", ENT_QUOTES, 'UTF-8');
$situaciones = htmlspecialchars($_GET['situacion'] ?? "", ENT_QUOTES, 'UTF-8');
$offset = htmlspecialchars($_GET['start'] ?? 0, ENT_QUOTES, 'UTF-8');
$limit = htmlspecialchars($_GET['length'] ?? 10, ENT_QUOTES, 'UTF-8');
$buscar = htmlspecialchars(@$_GET['search']['value'] ?? "", ENT_QUOTES, 'UTF-8');
$order_by_id = htmlspecialchars($_GET['order'][0]['column'] ?? -1, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['almacen']) ? limpiarCadena($_GET['almacen']) : 'CD11';
$ordenar_por = ['fecubi' => 'DESC'];

if ((int) $order_by_id > 0) {
    $parametro_ordenar = htmlspecialchars($_GET['columns'][$order_by_id]['data'], ENT_QUOTES, 'UTF-8');
    $parametro_ordenar_direccion = htmlspecialchars($_GET['order'][0]['dir'], ENT_QUOTES, 'UTF-8');
    $ordenar_por = [$parametro_ordenar => $parametro_ordenar_direccion];
    if ($parametro_ordenar == "fecha_creacion") {
        $ordenar_por['horubi'] = $parametro_ordenar_direccion;
    }
}
$order_clause = implode(", ", array_map(function ($key, $value) {
    return "$key $value";
}, array_keys($ordenar_por), array_values($ordenar_por)));

$filtros_busqueda = "";
if ($buscar) {
    $filtros_busqueda = "";
    if (is_numeric($buscar)) {
        $filtros_busqueda .= "pedclase LIKE '%$buscar%' OR pedubicod LIKE '%$buscar%' OR situped LIKE '%$buscar%' OR pedrefer LIKE '%$buscar%'";
        $filtros_busqueda = " AND ($filtros_busqueda)";
    }

}

$parametros = [$almacen];
$filtro = "";
if ($pedido) {
    $filtro .= " AND pedubicod = ?";
    $parametros[] = $pedido;
}
if ($fecha_desde) {
    $filtro .= " AND fecubi >= ?";
    $parametros[] = $fecha_desde;
}
if ($fecha_hasta) {
    $filtro .= " AND fecubi <= ?";
    $parametros[] = $fecha_hasta;
}
if ($clases_documento) {
    $clases_documentos = explode(",", $clases_documento);
    $filtro .= " AND pedclase IN (";
    $filtro .= str_repeat("?,", count($clases_documentos) - 1) . "?)";
    $parametros = array_merge($parametros, $clases_documentos);
}
if ($situaciones) {
    $situaciones_pedidos = explode(",", $situaciones);
    $filtro .= " AND situped IN (";
    $filtro .= str_repeat("?,", count($situaciones_pedidos) - 1) . "?)";
    $parametros = array_merge($parametros, $situaciones_pedidos);
}


$sql_condicion = "FROM
                        pedubicab
                  WHERE pedubicab.cod_alma = ? {$filtro} {$filtros_busqueda}";

$sql = "SELECT pedubicod as pedido, 
                pedrefer as referencia, 
                fecubi as fecha_creacion, 
                horubi as hora_creacion, 
                pedclase as clase_documento, 
                situped as situacion, 
                pedubicab.cod_alma as almacen, 
                pedsent as enviado {$sql_condicion} ORDER BY $order_clause LIMIT ? OFFSET ?";

$sqlBuilder = new MySqlQuery($conn);
$sql_cantidad = "SELECT COUNT(*) as cantidad $sql_condicion";
$cantidad = $sqlBuilder->rawQuery($sql_cantidad, $parametros)->getOne()->cantidad;
$sqlBuilder = new MySqlQuery($conn);


$datos_primarios = $sqlBuilder->rawQuery($sql, [...$parametros, $limit, $offset])->getAll();
$ids = [];
$materiales = [];
foreach ($datos_primarios as &$dato) {
    $ids[] = $dato['pedido'];
}
$ids_cadena = implode("','", $ids);
$sql = "SELECT pedido,tercod from assig_ped WHERE pedido IN ('$ids_cadena')";
$terminales_asignadas = $sqlBuilder->rawQuery($sql, [])->getAll();
foreach ($datos_primarios as &$dato_primario) {
    $dato_primario['terminal_asignada'] = "";
    foreach ($terminales_asignadas as $terminal) {
        if ($terminal['pedido'] == $dato_primario['pedido']) {
            $dato_primario['terminal_asignada'] = $terminal['tercod'];
        }
    }
    $dato_primario['enviar_sap'] = $dato_primario['situacion'] == "CE" && $dato_primario['enviado'] == 0 && $dato_primario['clase_documento'] == 'UB';
    $dato_primario['anular_wms'] = $dato_primario['situacion'] == 'PD' && $dato_primario['terminal_asignada'] == "";
    $dato_primario['fecha_creacion'] = formatear_fecha($dato_primario['fecha_creacion']) . " " . $dato_primario['hora_creacion'];
}

$respuesta = [
    'data' => $datos_primarios,
    'recordsFiltered' => $cantidad,
    'recordsTotal' => $cantidad
];
$conn->close();

retorna_resultado(200, $respuesta);
