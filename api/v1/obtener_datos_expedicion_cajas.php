<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");
require_once(__DIR__ . "/../../utils/sanitizador.php");
require_once(__DIR__ . "/../../logger/logger.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');
$fecha_desde = htmlspecialchars($_GET['fecha_desde'] ??  date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$fecha_hasta = htmlspecialchars($_GET['fecha_hasta'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$clases_documento = htmlspecialchars($_GET['clase_documento'] ?? "", ENT_QUOTES, 'UTF-8');
$situaciones = htmlspecialchars($_GET['situacion'] ?? "", ENT_QUOTES, 'UTF-8');
$offset = $situacion = htmlspecialchars($_GET['start'] ?? 0, ENT_QUOTES, 'UTF-8');
$limit = htmlspecialchars($_GET['length'] ?? 10, ENT_QUOTES, 'UTF-8');
$buscar = htmlspecialchars(@$_GET['search']['value'] ?? "", ENT_QUOTES, 'UTF-8');
$order_by_id = htmlspecialchars($_GET['order'][0]['column'] ?? -1, ENT_QUOTES, 'UTF-8');
$almacen = !empty($_GET['almacen']) ? limpiarCadena($_GET['almacen']) : 'CD11';
$codigos_envio = htmlspecialchars($_GET['codigos_envio'] ?? "", ENT_QUOTES, 'UTF-8');

$ordernar_por_direccion = "DESC";
$ordernar_por  = "pedexcabcajas.pedexfec";

if ((int) $order_by_id > 0) {
    $ordernar_por = htmlspecialchars($_GET['columns'][$order_by_id]['data'], ENT_QUOTES, 'UTF-8');
    $ordernar_por_direccion = htmlspecialchars($_GET['order'][0]['dir'], ENT_QUOTES, 'UTF-8');
}

$filtro_campos_numericos = "";
$filtros_campos_cadena = "";
$parametros = [];
$filtro = "";
if ($pedido) {
    $filtro .= " AND pedexentre = ?";
    $parametros[] = $pedido;
}
if ($fecha_desde) {
    $filtro .= " AND pedexfec >= ?";
    $parametros[] = $fecha_desde;
}
if ($fecha_hasta) {
    $filtro .= " AND pedexfec <= ?";
    $parametros[] = $fecha_hasta;
}
if ($clases_documento) {
    $clases_documentos = explode(",", $clases_documento);
    $filtro .= " AND pedclase IN (";
    $filtro .= str_repeat("?,", count($clases_documentos) - 1) . "?)";
    $parametros = array_merge($parametros, $clases_documentos);
}
if ($codigos_envio) {
    $codigos_envios = explode(",", $codigos_envio);
    $filtro .= " AND codenv IN (";
    $filtro .= str_repeat("?,", count($codigos_envios) - 1) . "?)";
    $parametros = array_merge($parametros, $codigos_envios);
}
if ($situaciones) {
    $situaciones_pedidos = explode(",", $situaciones);
    $filtro .= " AND siturefe IN (";
    $filtro .= str_repeat("?,", count($situaciones_pedidos) - 1) . "?)";
    $parametros = array_merge($parametros, $situaciones_pedidos);
}
$filtros_busqueda = "";
if ($buscar) {
    $filtros_busqueda = " mref_exp.ubirefer LIKE '%$buscar%' ";
    if (is_numeric($buscar)) {
        $filtros_busqueda .= " OR pedexentre LIKE '%$buscar%' OR pedexcabcajas.codenv = '%$buscar%' OR ped_multiref.multiref LIKE '%$buscar%'";
    }

    if (!is_numeric($buscar)) {
        $filtros_busqueda .= " OR clientes.clinom LIKE '%$buscar%' OR pedexcabcajas.siturefe LIKE '%$buscar%' OR pedexcabcajas.pedclase LIKE '%$buscar%' OR ped_multiref.terminal LIKE '%$buscar%'";
    }

    $filtros_busqueda = " AND ($filtros_busqueda)";
}



$sqlBuilder = new MySqlQuery($conn);
$sql_condicion = " FROM
                    pedexcabcajas 
                    left join (
                        select max(clicod),clirefer,clinom from clientes group by clirefer
                        )	as clientes on clientes.clirefer=pedexcabcajas.clirefer
                    LEFT JOIN ped_multiref on pedexcabcajas.pedexentre = ped_multiref.pedido and ped_multiref.pedcajas = '0'
                    left join mref_exp on
                        ped_multiref.multiref = mref_exp.mref
                        and mref_exp.pedido = pedexcabcajas.pedexentre
                    WHERE pedexcabcajas.almrefer='$almacen' $filtro $filtros_busqueda";

$sql = " SELECT
            pedexcabcajas.pedsent as enviado_sap,
            pedexcabcajas.pedexentre as pedido,
            pedexcabcajas.almrefer as almacen,
            pedexcabcajas.pedclase as clase_documento,
            clientes.clinom as nombre_cliente,
            pedexcabcajas.pedexfec as fecha_creacion,
            pedexcabcajas.pedexhor as hora_creacion,
            pedexcabcajas.codenv as cod_envio,
            mref_exp.ubirefer as ubicacion,
            pedexcabcajas.siturefe as situacion,
            (select GROUP_CONCAT(tercod) from assig_ped where pedido = pedexcabcajas.pedexentre and pedcajas='1' GROUP BY pedido) as terminal,
            ped_multiref.multiref as multireferencia 
            $sql_condicion
            ORDER BY $ordernar_por $ordernar_por_direccion LIMIT ? OFFSET ?";

$sql_cantidad = "SELECT count(*) AS cantidad $sql_condicion";
$cantidad = $sqlBuilder->rawQuery($sql_cantidad, $parametros)->getOne()->cantidad;
$sqlBuilder = new MySqlQuery($conn);

$datos = $sqlBuilder->rawQuery($sql, [...$parametros, $limit, $offset])->getAll();
#echo json_encode($datos); exit;
$pedidos = array_column($datos, 'pedido');

$pedidos_cadena = implode("','", $pedidos);
$materiales = obtener_materiales($pedidos_cadena);
/* obtenemos las zonas dosponibles de extraccion de articulos */
$zonas_libres = obtener_zonas($materiales);
//! zonas en porogreso de trabajo con terminal asignada
$zonas_ocupadas = obtener_zonas_ocupadas($pedidos);

#$terminales = obtener_terminales_asignadas_por_zona($pedidos);

foreach ($datos as $indice => $detalle_pedido) {
    $datos[$indice]['zonas'] = [];
    $datos[$indice]['fecha_creacion'] = formatear_fecha($detalle_pedido['fecha_creacion']) . " " . $detalle_pedido['hora_creacion'];
    $datos[$indice]['terminal_reposicion'] = "";
    if ($detalle_pedido['situacion'] == 'AN') {
        continue;
    }
    $datos[$indice]['enviar_sap'] = "ignorar";
    if ($detalle_pedido['enviado_sap'] == 1) {
        $datos[$indice]['enviar_sap'] = "enviado";
    }
    if (in_array($detalle_pedido['situacion'], ['CE', 'PP']) && $detalle_pedido['enviado_sap'] == 0 && $detalle_pedido['clase_documento'] <> 'REPO') {
        $datos[$indice]['enviar_sap'] = "enviar";
    }

    $datos[$indice]['habilitar_anulacion'] = $detalle_pedido['situacion'] == "PD" && $detalle_pedido['terminal'] == "";

    $datos[$indice]['anular_envio_sap'] = in_array($detalle_pedido['situacion'], ['CE', 'PP']) && $detalle_pedido['enviado_sap'] == 1 && $detalle_pedido['clase_documento'] <> 'REPO';


    if ($detalle_pedido['clase_documento'] <> 'REPO') {
        $zonas_pedido = $zonas_libres[$detalle_pedido['pedido']] ?? [];
        $zonas_disponibles = array_column($zonas_pedido, 'zona');

        foreach ($zonas_disponibles as $zona_disponible) {
            if (isset($zonas_ocupadas[$detalle_pedido['pedido']][$zona_disponible])) {
                $datos[$indice]['zonas'][] = [
                    'estado' => 'ocupado',
                    'zona' => $zona_disponible,
                    'terminal' => $zonas_ocupadas[$detalle_pedido['pedido']][$zona_disponible]
                ];
            } else {
                $datos[$indice]['zonas'][] = [
                    'estado' => 'libre',
                    'zona' => $zona_disponible
                ];
            }
        }
    } else {
        $pedido = $detalle_pedido['pedido'];
        $sql = "SELECT * from assig_ped where pedido = '{$pedido}'";
        $sqlBuilder = new MySqlQuery();
        $resultados = $sqlBuilder->rawQuery($sql, [])->getOne();
        if ($resultados) {
            $datos[$indice]['terminal_reposicion'] = $resultados->tercod;
        } else {
            $sql = "SELECT ubimapa.zoncodalm 
                    FROM pedexdet inner join stockubi on stockubi.artrefer = pedexdet.artrefer
                    inner join ubimapa on ubimapa.ubirefer = stockubi.ubirefer and stockubi.cod_alma=ubimapa.cod_alma
                    WHERE ubimapa.ubitipo = 'RE' limit 1";
            $sqlBuilder = new MySqlQuery();
            $resultados = $sqlBuilder->rawQuery($sql, [])->getOne();
            $datos[$indice]['zona_reposicion'] = $resultados->zoncodalm;
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

function obtener_materiales($pedidos_cadena)
{
    $sql = "SELECT * from pedexdet WHERE pedexentre IN ('$pedidos_cadena')";
    $sqlBuilder = new MySqlQuery();
    $detalles = $sqlBuilder->rawQuery($sql, [])->getAll();
    $materiales_por_pedido = [];
    foreach ($detalles as $detalle) {
        $materiales_por_pedido[$detalle['pedexentre']][] = $detalle['artrefer'];
    }
    return $materiales_por_pedido;
}

function obtener_zonas($materiales)
{
    $zonas_pedidos = [];
    foreach ($materiales as $pedido => $material) {
        $codigos_cadena = implode("','", $material);
        $sqzon = "SELECT
        b.zoncodpre,
        a.artrefer,
        sum(a.canti) as cantidad 
        from
            stockubi a
        inner join ubimapa b on
            a.ubirefer = b.ubirefer and b.ubitipo IN ('RE','PS')
            and a.cod_alma = b.cod_alma
        where
            a.artrefer in ('{$codigos_cadena}')
        GROUP BY
            b.zoncodpre
        HAVING sum(a.canti) > 0";
        $sqlBuilder = new MySqlQuery();
        $resultados = $sqlBuilder->rawQuery($sqzon, [])->getAll();
        foreach ($resultados as $resultado) {
            $zonas_pedidos[$pedido][] = array(
                "zona" => $resultado['zoncodpre'],
                "articulo" => $resultado['artrefer'],
                "cantidad" => $resultado['cantidad']
            );
        }
    }
    return $zonas_pedidos;
}

function obtener_zonas_ocupadas($pedidos)
{
    $zonas_ocupadas = [];
    $codigos_cadena = implode("','", $pedidos);
    $sql = "select zona,tercod,pedido from assig_ped where pedido IN ('$codigos_cadena') and pedcajas='1'";
    $sqlBuilder = new MySqlQuery();
    $resultados = $sqlBuilder->rawQuery($sql, [])->getAll();
    foreach ($resultados as $resultado) {
        $zonas_ocupadas[$resultado['pedido']][$resultado["zona"]] = $resultado['tercod'];
    }
    return $zonas_ocupadas;
}

function obtener_terminales_asignadas_por_zona($pedidos)
{
    $datos = [];
    $codigos_cadena = implode("','", $pedidos);
    $sql = "SELECT * FROM assig_ped WHERE pedcajas='1' AND pedido IN ('$codigos_cadena')";
    $sqlBuilder = new MySqlQuery();
    $resultados = $sqlBuilder->rawQuery($sql, [])->getAll();

    foreach ($resultados as $resultado) {
        $datos[$resultado['pedido']]['terminales'][$resultado["zona"]] = $resultado["tercod"];
    }
    return $datos;
}
