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
$fecha_descard = htmlspecialchars($_GET['fecha_descard'] ?? "", ENT_QUOTES, 'UTF-8');
$fecha_descarh = htmlspecialchars($_GET['fecha_descarh'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$clases_documento = htmlspecialchars($_GET['clase_documento'] ?? "", ENT_QUOTES, 'UTF-8');
$situaciones = htmlspecialchars($_GET['situacion'] ?? "", ENT_QUOTES, 'UTF-8');
$offset = $situacion = htmlspecialchars($_GET['start'] ?? 0, ENT_QUOTES, 'UTF-8');
$limit = htmlspecialchars($_GET['length'] ?? 10, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['almacen']) ? limpiarCadena($_GET['almacen']) : 'CD11';
$buscar = htmlspecialchars(@$_GET['search']['value'] ?? "", ENT_QUOTES, 'UTF-8');
$order_by_id = htmlspecialchars($_GET['order'][0]['column'] ?? -1, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['almacen']) ? limpiarCadena($_GET['almacen']) : 'CD11';
$ordenar_por = ['idoferta' => 'DESC'];

if ((int) $order_by_id > 0) {
    $parametro_ordenar = htmlspecialchars($_GET['columns'][$order_by_id]['data'], ENT_QUOTES, 'UTF-8');
    $parametro_ordenar_direccion = htmlspecialchars($_GET['order'][0]['dir'], ENT_QUOTES, 'UTF-8');
    $ordenar_por = [$parametro_ordenar => $parametro_ordenar_direccion];
    if ($parametro_ordenar == "fecped") {
        $ordenar_por['horped'] = $parametro_ordenar_direccion;
    }
}

$sql_terminal = "SELECT ap_termi AS terminal
                FROM pedredet 
                INNER JOIN pallet_mat 
                on pedredet.pedrefer=pallet_mat.ap_pedido and pedredet.pedpos=pallet_mat.pedpos and pedredet.artrefer=pallet_mat.ap_mat 
                WHERE pedredet.pedrefer = pedrecab.pedrefer limit 1";

$filtros_busqueda = "";
if ($buscar) {
    $filtros_busqueda = " codprove LIKE '%$buscar%' ";
    if (is_numeric($buscar)) {
        $filtros_busqueda .= " OR idcontra LIKE '%$buscar%' ";
    }

    if (!is_numeric($buscar)) {
        $filtros_busqueda .= " OR pedclase LIKE '%$buscar%' OR ($sql_terminal) LIKE '%$buscar%' OR pedresitu LIKE '%$buscar%'";
    }

    $filtros_busqueda = " AND ($filtros_busqueda)";
}

$parametros = [];
$filtro = "";
if ($pedido) {
    $filtro .= " AND idcontra = ?";
    $parametros[] = $pedido;
}
if ($fecha_desde) {
    $filtro .= " AND fecped >= ?";
    $parametros[] = $fecha_desde;
}
if ($fecha_hasta) {
    $filtro .= " AND fecped <= ?";
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


$sql_condicion = " FROM
                    contcab 
                    left join proveedores on contcab.codprove = proveedores.codprove
                    where TRUE 
                    $filtro ";

$order_clause = implode(", ", array_map(function ($key, $value) {
    return "$key $value";
}, array_keys($ordenar_por), array_values($ordenar_por)));

$sql_datos = "
    SELECT 
    contcab.idcontra,
    contcab.pedclase,
    contcab.sociedad,
    contcab.codmone,
    proveedores.nombre,
    contcab.fecped,
    contcab.orgcompra,
    contcab.grupcompra,
    contcab.situped,
    contcab.codliber


        $sql_condicion
        ORDER BY contcab.idcontra asc
        LIMIT ? OFFSET ?
";
//print $sql_datos;
$sqlBuilder = new MySqlQuery($conn);
$sql_cantidad = "SELECT COUNT(*) as cantidad $sql_condicion";
$cantidad = $sqlBuilder->rawQuery($sql_cantidad, $parametros)->getOne()->cantidad;
$sqlBuilder = new MySqlQuery($conn);

$datos = $sqlBuilder->rawQuery($sql_datos, [...$parametros, $limit, $offset])->getAll();

foreach ($datos as &$dato) {
   // $dato['fecped'] = formatear_fecha($dato['fecped'], 'd-m-Y');
    //$dato['fec_finiquito'] = formatear_fecha($dato['fec_finiquito'], 'd-m-Y');
    //$dato['fecvenci_dias'] = formatear_fecha($dato['fecvenci_dias'], 'd-m-Y');
    //$dato['fecha_descarga'] = formatear_fecha($dato['fecha_descarga'], 'd-m-Y');
    //$dato['terminal'] = null;
   // $dato['almacen'] = $almacen;

}

$respuesta = [
    'data' => $datos,
    'recordsFiltered' => $cantidad,
    'recordsTotal' => $cantidad
];
$conn->close();

retorna_resultado(200, $respuesta);
