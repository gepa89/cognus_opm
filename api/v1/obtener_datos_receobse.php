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
$offset = $situacion = htmlspecialchars($_GET['start'] ?? 0, ENT_QUOTES, 'UTF-8');
$limit = htmlspecialchars($_GET['length'] ?? 10, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['codalma']) ? limpiarCadena($_GET['codalma']) : 'CD11';
$buscar = htmlspecialchars(@$_GET['search']['value'] ?? "", ENT_QUOTES, 'UTF-8');
$order_by_id = htmlspecialchars($_GET['order'][0]['column'] ?? -1, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['almacen']) ? limpiarCadena($_GET['almacen']) : 'CD11';
$ordenar_por = ['pedrefec' => 'DESC'];

if ((int) $order_by_id > 0) {
    $parametro_ordenar = htmlspecialchars($_GET['columns'][$order_by_id]['data'], ENT_QUOTES, 'UTF-8');
    $parametro_ordenar_direccion = htmlspecialchars($_GET['order'][0]['dir'], ENT_QUOTES, 'UTF-8');
    $ordenar_por = [$parametro_ordenar => $parametro_ordenar_direccion];
    if ($parametro_ordenar == "fecha_creacion") {
        $ordenar_por['pedrehor'] = $parametro_ordenar_direccion;
    }
}



$filtros_busqueda = "";
if ($buscar) {
    $filtros_busqueda = " codprove LIKE '%$buscar%' ";
    if (is_numeric($buscar)) {
        $filtros_busqueda .= " OR pedrefer LIKE '%$buscar%' ";
    }

    if (!is_numeric($buscar)) {
        $filtros_busqueda .= " OR pedclase LIKE '%$buscar%' OR ($sql_terminal) LIKE '%$buscar%' OR pedresitu LIKE '%$buscar%'";
    }

    $filtros_busqueda = " AND ($filtros_busqueda)";
}

$parametros = [$almacen];
$filtro = "";
if ($pedido) {
    $filtro .= " AND pedrecab.pedrefer = ?";
    $parametros[] = $pedido;
}
if ($fecha_desde) {
    $filtro .= " AND pedrefec >= ?";
    $parametros[] = $fecha_desde;
}
if ($fecha_hasta) {
    $filtro .= " AND pedrefec <= ?";
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
    $filtro .= " AND pedresitu IN (";
    $filtro .= str_repeat("?,", count($situaciones_pedidos) - 1) . "?)";
    $parametros = array_merge($parametros, $situaciones_pedidos);
}


$sql_condicion = " FROM
                    pedrecab 
                    INNER JOIN observaciones_recepcion on observaciones_recepcion.codigo_pedido=pedrecab.pedrefer
                    WHERE pedrecab.almrefer=? $filtro $filtros_busqueda";

$order_clause = implode(", ", array_map(function ($key, $value) {
    return "$key $value";
}, array_keys($ordenar_por), array_values($ordenar_por)));

$sql_datos = "
    SELECT 
        pedrecab.pedrefer AS pedido,
        pedrecab.pedclase AS clase_documento,
        pedrecab.pedrefec AS fecha_creacion,
        observaciones_recepcion.fecha_creacion AS fecha_control,
        pedrecab.codprove AS proveedor,
        pedrecab.nomprove AS nomprove

        $sql_condicion
        ORDER BY $order_clause 
        LIMIT ? OFFSET ?
";
$sqlBuilder = new MySqlQuery($conn);
$sql_cantidad = "SELECT COUNT(*) as cantidad $sql_condicion";
$cantidad = $sqlBuilder->rawQuery($sql_cantidad, $parametros)->getOne()->cantidad;
$sqlBuilder = new MySqlQuery($conn);

$datos = $sqlBuilder->rawQuery($sql_datos, [...$parametros, $limit, $offset])->getAll();

foreach ($datos as &$dato) {
    $dato['fecha_creacion'] = $dato['fecha_creacion'] ? formatear_fecha($dato['fecha_creacion'], 'd-m-Y') . " " . $dato['hora_creacion'] : "";
    $dato['fecha_recepcion'] = formatear_fecha($dato['fecha_recepcion'], 'd-m-Y');
    //$dato['terminal'] = null;
    $dato['enviado_sap'] = "";
    $dato['puede_anular'] = false;
    $dato['cantidad_pendiente'] = 0;
    $dato['almacen'] = $almacen;
    /*if (in_array($dato['estado'], ['AN', 'CE'])) {
        $sqlBuilder = new MySqlQuery($conn);
        $sql = "SELECT ap_termi AS terminal,
                        pedredet.pedrefer
                FROM pedredet 
                INNER JOIN pallet_mat 
                on pedredet.pedrefer=pallet_mat.ap_pedido and pedredet.pedpos=pallet_mat.pedpos and pedredet.artrefer=pallet_mat.ap_mat 
                WHERE pedredet.pedrefer = ? limit 1";
        $params = [$dato['pedido']];
        $terminal = $sqlBuilder->rawQuery($sql, $params)->getOne();
        $dato['terminal'] = $terminal ? $terminal->terminal : "No encontrado";
    }*/
    if ($dato['enviado_sap'] == 'CE' && $dato['enviado_sap'] == 1) {
        $dato['enviado_sap'] = "Enviado";
    }
    if ($dato['estado'] == 'PD') {
        $dato['puede_anular'] = !$dato['terminal'];
        $sql = "SELECT
                    COUNT(*) AS cantidad
                from
                    pedrecab
                INNER JOIN pedredet on
                    pedrecab.pedrefer = pedredet.pedrefer
                INNER JOIN arti on
                    pedredet.artrefer = arti.artrefer
                LEFT JOIN artubiref on
                    pedredet.artrefer = artubiref.artrefer
                    and artubiref.cod_alma = ?
                    AND artubiref.ubitipo = 'PI'
                WHERE
                    pedrecab.pedrefer = ?
                    AND artubiref.ubirefer is NULL";
        $parametros = [$dato['almacen'], $dato['pedido']];
        $pendientes = $sqlBuilder->rawQuery($sql, $parametros)->getOne();
        if ($pendientes) {
            $cantidad_pendiente = $pendientes->cantidad;
            if ($cantidad_pendiente > 0) {
                $dato['cantidad_pendiente'] = (int) $cantidad_pendiente;
            }
        }
    }
}

$respuesta = [
    'data' => $datos,
    'recordsFiltered' => $cantidad,
    'recordsTotal' => $cantidad
];
$conn->close();

retorna_resultado(200, $respuesta);
