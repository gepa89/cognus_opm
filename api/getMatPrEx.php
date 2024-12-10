<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
header('Content-type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$terminal = strtoupper($_POST['terminal']);
$db->set_charset("utf8");
$response = array();
$query = $db->query($sql);
$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";
$cc = 0;
$r1 = $db->query($sq);
$ser = '';


$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
//echo $sqz;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$zona = $rszz['terzonpre'];
$rutas = array();
$pedidos_pendientes_reposicion = array();
$materiales_procesados = array();
while ($fila_pedido = $r1->fetch_object()) {
    $pedido_id = $fila_pedido->pedido;
    $sql = "SELECT DISTINCT 
        a.artrefer,
        b.artlotemar, 
        a.canpedi,
        a.pedpos,
        a.artlote, 
        a.canprepa, 
        b.artdesc,
        b.artser,
        pedexcab.almrefer,
        pedexcab.obse1,
        pedexcab.puestoexp,
        pedexcab.codenv,
        b.unimed AS unidad_medida_articulo,
        (select GROUP_CONCAT(ean) as eans from artean c where c.artrefer = a.artrefer GROUP BY c.artrefer) as ean,
        e.canpresen,
        e.preseref
        from pedexdet a
        inner join pedexcab ON a.pedexentre = pedexcab.pedexentre
        inner join arti b on a.artrefer = b.artrefer 
        left join artubiref on artubiref.artrefer = b.artrefer
        left join ubimapa on artubiref.ubirefer = ubimapa.ubirefer and artubiref.cod_alma = ubimapa.cod_alma 
        left join artipresen e on a.artrefer = e.artrefer and e.cod_alma=pedexcab.almrefer
        where a.pedexentre = '$fila_pedido->pedido' AND ubimapa.zoncodpre = '$zona' and a.tienekit <> 'SI'
        and a.expst=0 order by a.pedexentre ASC";
    $resultado_query = $db->query($sql);
    $datos = array();
    $cantidad = 0;
    while ($fila = $resultado_query->fetch_object()) {
        verificar_reposicion($db, $pedido_id, $fila->pedpos, $fila->artrefer);
        $ruta = new stdClass();
        $totalRE = 0;
        $totalPI = 0;
        $fila->pedido = $fila_pedido->pedido;
        $fila->es_loteado = $fila->artlotemar === "SI";
        $fila->tiene_presentacion = $fila->preseref != 'UNI';
        $cantidad_por_presentacion = $fila->canpresen ? $fila->canpresen : 1;
        $unidad_medida_presentacion = $fila->preseref;
        $cantidad_procesada_previa = 0;
        $cantidad_unitaria_procesada = 0;
        if (isset($materiales_procesados[$fila->artrefer][$fila->pedpos])) {
            $cantidad_procesada_previa = $materiales_procesados[$fila->artrefer][$fila->pedpos];
        }
        $pendiente_preparacion = $fila->canpedi - $fila->canprepa - $cantidad_procesada_previa; //lo que falta preparar
        if ($pendiente_preparacion == 0) {
            continue;
        }
        $cantidad_pedido = $fila->canpedi;
        $pendXPresen = 0;
        $tipoUbicacion = "PI";
        obtenerDatosRePi($db, $fila, $totalRE, $totalPI);

        $presentacion_articulo = obtener_presentacion_cajas($db, $fila->artrefer);
        if ($presentacion_articulo) {
            # verifica las presentaciones UNI
            $res = $pendiente_preparacion / $presentacion_articulo->canpresen;
            if ($res < 1) {
                $tipoUbicacion = "PI";
                $fila->tiene_presentacion = false;
                $fila->preseref = 'UNI';
                $fila->canpresen = 1;
                $cantidad_pedido = $pendiente_preparacion;
                $cantidad_por_presentacion = 1;
                $unidad_medida_presentacion = "UNI";
                $cantidad_unitaria_procesada = $pendiente_preparacion;
                $fila->ubicacion = obtenerUbicacion($db, $fila, $zona, $tipoUbicacion);
                if ($fila->ubicacion == null) {
                    continue;
                }

                goto salir;
            }
            if (!$fila->tiene_presentacion) {

                $pendiente_por_unidad = ($res - (int) $res) * $presentacion_articulo->canpresen;
                if ($pendiente_por_unidad == 0) {
                    # ya no existen cantidades unitarias a preparar
                    continue;
                }
                $pendXPresen = 0;
                $cantidad_por_presentacion = 1;
                $cantidad_pedido = $pendiente_por_unidad;
                $cantidad_unitaria_procesada = $cantidad_pedido;
                $unidad_medida_presentacion = "UNI";
                $pendiente_preparacion = $pendiente_por_unidad;
                $fila->ubicacion = obtenerUbicacion($db, $fila, $zona, $tipoUbicacion);
                if ($fila->ubicacion == null) {
                    continue;
                }
            } else {
                // si hay cantidad menor a preparar que la presentacion
                $tipoUbicacion = "RE";
                $fila->ubicacion = obtenerUbicacion($db, $fila, $zona, $tipoUbicacion);
                if ($fila->ubicacion == null) {
                    $tipoUbicacion = "PI";
                    $fila->tiene_presentacion = false;
                    $fila->preseref = 'UNI';
                    $fila->canpresen = 1;
                    $cantidad_por_presentacion = 1;
                    $cantidad_unitaria_procesada = $pendiente_preparacion;
                    $unidad_medida_presentacion = "UNI";
                    $fila->ubicacion = obtenerUbicacion($db, $fila, $zona, $tipoUbicacion);
                    if ($fila->ubicacion == null) {
                        continue;
                    }
                } else {
                    $totalPI = $totalRE;
                    $pendXPresen = intval($pendiente_preparacion / $cantidad_por_presentacion);
                    $cantidad_unitaria_procesada = $pendXPresen * $cantidad_por_presentacion;
                    $cantidad_pedido = $pendXPresen;
                    $pendiente_preparacion = $cantidad_pedido;
                }
            }
        } else {
            $fila->ubicacion = obtenerUbicacion($db, $fila, $zona, $tipoUbicacion);
            if ($fila->ubicacion == null) {
                continue;
            }
        }
        salir:
        if (isset($fila->artrefer, $materiales_procesados)) {
            $materiales_procesados[$fila->artrefer][$fila->pedpos] += $cantidad_unitaria_procesada;
        } else {
            $materiales_procesados[$fila->artrefer][$fila->pedpos] = $cantidad_unitaria_procesada;
        }

        $lotes_articulos = obtenerLotesArticulos($db, $fila);

        $min = obtenerArticuloMinimoInventario($db, $fila);

        $stock = obtenerArticuloStock($db, $fila, $pendXPresen, $tipoUbicacion);

        $ruta->ubirefer = separateLocation($fila->ubicacion['ubirefer']);
        $ruta->minimo = $min;
        $ruta->pedido = $fila_pedido->pedido;
        $ruta->stock = $stock;
        $ruta->posicion = $fila->pedpos;
        $ruta->observacion = trim($fila->obse1);
        $ruta->puesto_expedicion = $fila->puestoexp;
        $ruta->es_loteado = $fila->es_loteado;
        $ruta->lote = $fila->artlote;
        $ruta->tipo = $fila->arser;
        $ruta->serie = "";
        $ruta->ean = $fila->ean;
        $ruta->pedido = $fila_pedido->pedido;
        $ruta->artrefer = $fila->artrefer;
        $ruta->canpedi = $cantidad_pedido;
        $ruta->canpendi = $pendiente_preparacion;
        $ruta->PI = $totalPI;
        $ruta->RE = $totalRE;
        $ruta->tipo_ubicacion = $tipoUbicacion;
        $ruta->pedido_divido = $dividir_pedido;
        $ruta->tiene_presentacion = $fila->tiene_presentacion;
        $ruta->presenref = $unidad_medida_presentacion;
        $ruta->presenmul = 1;
        $ruta->presen =  $fila->unidad_medida_articulo;
        $ruta->cantidad_presentacion = (int) $cantidad_por_presentacion;
        $ruta->artdesc = $fila->artdesc;
        $ruta->lotes = $lotes_articulos;
        $ruta->codigo_envio = $fila->codenv;
        $ruta->ordenN = obtenerOrden($db, $ruta->ubirefer, $fila->almrefer);
        $ruta->multiref = $fila_pedido->multiref;
        $ruta->tipo_almacenaje = "";

        array_push($rutas, $ruta);
    }
    $pedidos_pendientes_reposicion = array_merge($pedidos_pendientes_reposicion, obtenerPedidosConReposicionPendientes($db, $fila_pedido->pedido));
}
$db->close();
$pedido = "";
$auxiliar = array();
$respuesta = array(
    "mensaje" => "Sin pedidos procesables",
    "error" => true,
);
if ($rutas) {
    foreach ($rutas as $ruta) {
        if ($pedido != $ruta->pedido) {
            $pedido = $ruta->pedido;
            $auxiliar[$pedido] = 1;
        } else {
            $auxiliar[$pedido]++;
        }
    }
    foreach ($rutas as $ruta) {
        $ruta->cantsku = $auxiliar[$ruta->pedido];
    }
    usort($rutas, 'sortByUbirefer');
    $respuesta = [
        "error" => false,
        "ruta" => $rutas,
        "mensaje" => ""
    ];
}
$respuesta['pedidos_con_reposicion'] = $pedidos_pendientes_reposicion;

/*$sq = "update serati set sersitu = 0 where artserie in ()";
$db->query($sq);
$sq = "select * from ruta_ubic";
$rubi = $db->query($sq);
$ubica = array();
while ($asx = $rubi->fetch_assoc()) {
    $ubica[$asx['orden']] = $asx['ubirefer'];
}
//var_dump($fstDataMat);
foreach ($fila as $k => $dat) {
    $key = array_search($fila->ubirefer, $ubica);
    $data[$key][] = $dat;
}
ksort($data);
$orden = OrdenarArray($data2, 'ordenN');
array_multisort($orden, SORT_ASC, $data2);
echo json_encode($data);*/

//guardar_info_log(__FILE__,json_encode($respuesta));
echo json_encode($respuesta);
exit;
function obtenerUbicacion($db, $fila, $zona, $tipoUbicacion = 'PI')
{
    $ubicacion = null;
    if ($fila->artlotemar === "SI") {
        $sql = "SELECT stockubi.*, SUM(stockubi.canti) AS cantidad from loteart 
            inner join stockubi ON stockubi.artlote = loteart.artlote AND stockubi.cod_alma = loteart.cod_alma 
            left outer join expeartce ON expeartce.id_articulo = stockubi.artrefer
            AND expeartce.cod_alma = stockubi.cod_alma
            AND expeartce.ubicacion = stockubi.ubirefer 
            AND expeartce.id_pedido = '$fila->pedido' 
            WHERE stockubi.artrefer = '$fila->artrefer' 
            AND stockubi.cod_alma='$fila->almrefer' 
            AND expeartce.id is null 
            GROUP BY stockubi.artlote 
            HAVING SUM(stockubi.canti) > 0 
            order by fecaduc ASC LIMIT 1";
        $query = $db->query($sql);
        $dato = $query->fetch_object();
        $ubicacion = [
            "ubirefer" => $dato->ubirefer,
            "cantidad" => $dato->canti,
            "lote" => $dato->artlote
        ];
    } else {
        $sql = "SELECT ubimapa.ubirefer, sum(stockubi.canti) as cantidad 
            FROM stockubi inner join ubimapa 
            ON ubimapa.ubirefer = stockubi.ubirefer AND ubimapa.cod_alma=stockubi.cod_alma 
            left outer join expeartce ON expeartce.id_articulo = stockubi.artrefer
            AND expeartce.cod_alma = stockubi.cod_alma
            AND expeartce.ubicacion = stockubi.ubirefer 
            AND expeartce.id_pedido = '$fila->pedido'
            AND expeartce.pedpos = '$fila->pedpos'
            WHERE stockubi.artrefer = '$fila->artrefer' 
            AND ubimapa.cod_alma = '$fila->almrefer' 
            AND ubimapa.ubitipo='$tipoUbicacion' 
            AND ubimapa.zoncodpre = '$zona'
            AND expeartce.id is null 
            GROUP BY  stockubi.artrefer,ubimapa.ubirefer,stockubi.cod_alma 
            HAVING sum(stockubi.canti) > 0 ORDER BY stockubi.fecingre ASC";
        $query = $db->query($sql);
        $ubicaciones_probables = $query->fetch_all(MYSQLI_ASSOC);
        if (count($ubicaciones_probables) > 0) {
            $ubicacion = $ubicaciones_probables[0];
        }
    }
    return $ubicacion;
}
function obtenerDatosRePi($db, $fila, &$totalRE, &$totalPI)
{
    $sql = "SELECT
                stockubi.artrefer,
                ubimapa.ubitipo,
                SUM(canti) as canti
            FROM
                stockubi
            inner join ubimapa ON
                stockubi.ubirefer = ubimapa.ubirefer
                and stockubi.cod_alma = ubimapa.cod_alma
            WHERE
                stockubi.cod_alma = '$fila->almrefer'
                and ubimapa.ubitipo <> 'MR'
                and artrefer = '$fila->artrefer'
            GROUP BY
                ubimapa.ubitipo";
    $query = $db->query($sql);
    while ($totalData = $query->fetch_assoc()) {
        if ($totalData['ubitipo'] == 'RE')
            $totalRE = (int) $totalData['canti'];
        else if ($totalData['ubitipo'] == 'PI')
            $totalPI = (int) $totalData['canti'];
    }
}
function obtenerLotesArticulos($db, $fila)
{
    if (!$fila->es_loteado) {
        return [];
    }
    $sql_lotes = "SELECT artrefer, artlote, SUM(canti) as cantidad 
                FROM stockubi 
                WHERE artrefer = '$fila->artrefer' 
                AND cod_alma = '$fila->almrefer'
                AND ubirefer = '{$fila->ubicacion['ubirefer']}'
                GROUP BY artlote";
    $lotes_articulo = $db->query($sql_lotes)->fetch_all(MYSQLI_ASSOC);
    return $lotes_articulo;
}

function obtenerArticuloMinimoInventario($db, $fila)
{
    $sql = "SELECT artminve 
    from artinve 
    where artrefer = '$fila->artrefer' and almcod='$fila->almrefer' limit 1";
    return $db->query($sql)->fetch_assoc()['artminve'];
}

function obtenerArticuloStock($db, $fila, $pendXPresen, $tipoUbicacion)
{
    $sql = "SELECT * from (
        select distinct 
        a.ubirefer,a.artrefer, b.ubitipo, sum(a.canti) as stock
        from stockubi a 
        inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
        where 
        a.cod_alma = '$fila->almrefer'
        AND a.artrefer = '$fila->artrefer' and 
        b.ubitipo = '$tipoUbicacion' and a.ubirefer = '{$fila->ubicacion['ubirefer']}'
        GROUP BY a.ubirefer, a.artrefer, b.ubitipo 
        ) as locx where locx.stock >= {$pendXPresen} order by locx.ubirefer asc limit 1";
    return $db->query($sql)->fetch_assoc()['stock'];
}
function SepararUbicacion($ubi)
{
    //$ubi = filter_var($ubi, FILTER_SANITIZE_NUMBER_INT); 
    $separador = "-";
    $separada = explode($separador, $ubi);
    $separada[2] = substr($separada[2], 0, 2);
    return $separada;
}

function obtenerOrden($db, $ubicacion, $cod_alma)
{
    $ubicacion = SepararUbicacion($ubicacion);
    $pasillo = $ubicacion[0];
    $hueco = $ubicacion[1];
    $nivel = $ubicacion[2];
    $sqlParaOrdenar = "SELECT rutid FROM rutadet  
                    INNER JOIN  ruta ON rutadet.rutcod=ruta.rutcod AND ruta.rutesta = 1 AND ruta.cod_alma = rutadet.cod_alma
                    WHERE 
                    (ubiestan <= '$pasillo' AND ubiestanh >= '$pasillo')
                    AND ruta.cod_alma='$cod_alma' 
                    AND ((ubihuec <= '$hueco' AND ubihuech >= '$hueco') OR (ubihuec >= '$hueco' AND ubihuech <= '$hueco'))
                       AND (ubiniv <= '$nivel' AND ubinivh >= '$nivel') 
                       AND rutadet.rutcod = ruta.rutcod LIMIT 1;";
    $qxd = $db->query($sqlParaOrdenar);
    $datN = $qxd->fetch_array();
    return $datN['rutid'];
}

function sortByUbirefer($a, $b)
{
    return strcmp($a->ubirefer, $b->ubirefer);
}
function obtenerPedidosConReposicionPendientes($db, $pedidoActual)
{
    $sql = "SELECT * FROM pedexdet 
    WHERE  pedexentre = '$pedidoActual' 
    AND repocurso = true 
    AND expst=0 ";
    $resultado = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}

function obtener_presentacion_cajas($db, $articulo)
{
    $sql = "SELECT * from artipresen WHERE artrefer = '$articulo' AND preseref='CJ'";
    $resultado = $db->query($sql)->fetch_object();
    return $resultado;
}

function verificar_reposicion($db, $pedido, $posicion, $material)
{
    $sql = "SELECT * FROM pedexdet INNER JOIN pedexcab ON pedexcab.pedexentre = pedexdet.pedexentre 
        WHERE pedclase = 'REPO' AND pedexdet.artrefer = '$material' AND pedexcab.siturefe='PD'";
    guardar_sql_log($sql);
    $res = $db->query($sql);
    if (!$res) {
        guardar_error_log(__FILE__,"error $sql");
        return;
    }
    if ($res->num_rows > 0) {
        $sql_update = "UPDATE pedexdet SET repocurso=True WHERE pedexentre='$pedido' AND artrefer='$material'";
        guardar_info_log(__FILE__,$sql_update);
        guardar_sql_log($sql_update);
        $res = $db->query($sql_update);
    }
}
