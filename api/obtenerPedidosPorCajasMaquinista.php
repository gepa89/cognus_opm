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
$sqr = "select lectura from config";
$rr = $db->query($sqr);
$rrx = $rr->fetch_assoc();
$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";
//     echo $sq;
$r1 = $db->query($sq);
$cc = 0;
$ser = '';
$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
//echo $sqz;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$zona = $rszz['terzonpre'];
$rutas = array();
$pedidos_pendientes_reposicion = array();

while ($fila_pedido = $r1->fetch_object()) {
    $sql = "SELECT Distinct
        a.artrefer,
        b.artlotemar, 
        a.canpedi,
        a.pedpos,
        a.artlote, 
        a.canprepa, 
        b.artdesc,
        b.artser,
        b.tipalma,
        pedexcabcajas.almrefer,
        pedexcabcajas.obse1,
        pedexcabcajas.puestoexp,
        pedexcabcajas.codenv,
        (select GROUP_CONCAT(ean) as eans from artean c where c.artrefer = a.artrefer GROUP BY c.artrefer) as ean,
        e.canpresen,
        e.preseref
        from pedexdetcajas a
        inner join pedexcabcajas ON a.pedexentre = pedexcabcajas.pedexentre
        inner join arti b on a.artrefer = b.artrefer 
        inner join stockubi on stockubi.artrefer = b.artrefer
        inner join ubimapa on ubimapa.ubirefer = stockubi.ubirefer
        left join artipresen e on a.artrefer = e.artrefer and e.cod_alma=pedexcabcajas.almrefer
        where a.pedexentre = '$fila_pedido->pedido'
        AND ubimapa.zoncodpre = '$zona' 
        and a.expst=0 and preseref = 'CJ' order by a.pedexentre ASC";
    $resultado_query = $db->query($sql);
    $datos = array();
    $cantidad = 0;
    while ($fila = $resultado_query->fetch_object()) {
        $ruta = new stdClass();
        $totalRE = 0;
        $totalPI = 0;
        $totalPS = 0;
        $fila->pedido = $fila_pedido->pedido;
        $fila->es_loteado = $fila->artlotemar === "SI";
        $fila->tiene_presentacion = $fila->preseref != 'UNI';

        obtenerDatosRePi($db, $fila, $totalRE, $totalPI, $totalPS);

        $lotes_articulos = obtenerLotesArticulos($db, $fila);
        $pendPrep = $fila->canpedi - $fila->canprepa; //lo que falta preparar
        //            echo $ax2['preseref'];
        $cantidad_pedido = $fila->canpedi;
        $unidad_medida_presentacion = $fila->preseref;
        $cantidad_por_presentacion = $fila->canpresen;

        /*$presentacion_por_pallet = obtener_presentacion_por_pallet($db, $fila->artrefer);
        if ($presentacion_por_pallet) {
            if ($presentacion_por_pallet->cantidad_presentacion <= $pendPrep) {
                $unidad_medida_presentacion = $presentacion_por_pallet->presentacion;
                $cantidad_por_presentacion = $presentacion_por_pallet->cantidad_presentacion;
            }
        }*/
        $tipoUbicacion = ["PS", "RE"];
        $totalPI = $totalRE;
        if ($fila->tiene_presentacion) {
            $pendXPresen = intval($pendPrep / $cantidad_por_presentacion);
            //                echo $pendXPresen;
            $pendXPresenXUni = $pendXPresen * $cantidad_por_presentacion;
            $pendiente_por_unidad = abs($pendPrep % $cantidad_por_presentacion);
            $pendPrep = $pendPrep - $pendiente_por_unidad;
        } else {
            print_r("no tiene presen");
            exit;
            //                echo    $pendPrep;
        }
        $presentacion_pallet = obtenerDatosPresentacionPallet($db, $fila->artrefer, $fila->almrefer);
        $cantidad_por_presentacion_pallet = 1; // toma por defecto 1 para la comparacion
        if ($presentacion_pallet) {
            $cantidad_por_presentacion_pallet = ($pendPrep >= $presentacion_pallet->canpresen) ? $presentacion_pallet->canpresen : 1;
        }
        $min = obtenerArticuloMinimoInventario($db, $fila);
        $fila->ubicacion = obtenerUbicacion($db, $zona, $fila, $tipoUbicacion, $cantidad_por_presentacion_pallet);
        if ($fila->ubicacion == null) {
            continue;
        }
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
        $ruta->tipo_almacenaje = $fila->tipalma;
        $ruta->ean = $fila->ean;
        $ruta->pedido = $fila_pedido->pedido;
        $ruta->artrefer = $fila->artrefer;
        $ruta->canpedi = floor($cantidad_pedido / $cantidad_por_presentacion);
        $ruta->canpendi = floor($pendPrep / $cantidad_por_presentacion);
        $ruta->PI = $totalPI;
        $ruta->RE = $totalRE;
        $ruta->PS = $totalPS;
        $ruta->tiene_presentacion = $fila->tiene_presentacion;
        $ruta->presenref = $unidad_medida_presentacion;
        $ruta->presenmul = 1;
        $ruta->tipo_ubicacion = $tipoUbicacion;
        $ruta->presen = obtener_presentaciones($db, $fila->artrefer);;
        $ruta->cantidad_presentacion = (int) $cantidad_por_presentacion;
        $ruta->artdesc = $fila->artdesc;
        $ruta->lotes = $lotes_articulos;
        $ruta->ordenN = obtenerOrden($db, $ruta->ubirefer, $fila->almrefer);
        $ruta->multiref = $fila_pedido->multiref;
        $ruta->codigo_envio = $fila->codenv;
        array_push($rutas, $ruta);
    }
    $pedidos_pendientes_reposicion = array_merge($pedidos_pendientes_reposicion, obtenerPedidosConReposicionPendientes($db, $fila_pedido->pedido));
}
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
    $respuesta = [
        "error" => false,
        "ruta" => $rutas,
        "mensaje" => ""
    ];
}
$respuesta['pedidos_con_reposicion'] = $pedidos_pendientes_reposicion;

usort($rutas, 'sortByUbirefer');
echo json_encode($respuesta);
function obtenerUbicacion($db, $zona, $fila, $tipoUbicacion = ['PI'], $cantidad = 1)
{
    $ubicacion = null;
    $in_ubicaciones = implode("','", $tipoUbicacion);
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
            HAVING SUM(stockubi.canti) >= $cantidad 
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
            WHERE stockubi.artrefer = '$fila->artrefer' 
            AND ubimapa.cod_alma = '$fila->almrefer' 
            AND ubimapa.ubitipo in ('$in_ubicaciones')
            AND ubimapa.zoncodpre = '$zona' 
            AND expeartce.id is null 
            GROUP BY  stockubi.artrefer,ubimapa.ubirefer,stockubi.cod_alma 
            HAVING sum(stockubi.canti) >= $cantidad ORDER BY ubimapa.ubirefer ASC";
        $query = $db->query($sql);
        $ubicaciones_probables = $query->fetch_all(MYSQLI_ASSOC);
        if (count($ubicaciones_probables) > 0) {
            $ubicacion = $ubicaciones_probables[0];
        }
    }
    return $ubicacion;
}
function obtenerDatosRePi($db, $fila, &$totalRE, &$totalPI, &$totalPS)
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
        else if ($totalData['ubitipo'] == 'PS')
            $totalPS = (int) $totalData['canti'];
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
    $in_ubicaciones = implode("','", $tipoUbicacion);
    $sql = "SELECT * from (
        select distinct 
        a.ubirefer,a.artrefer, b.ubitipo, sum(a.canti) as stock
        from stockubi a 
        inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
        where 
        a.cod_alma = '$fila->almrefer'
        AND a.artrefer = '$fila->artrefer' and 
        b.ubitipo IN ('$in_ubicaciones') and a.ubirefer = '{$fila->ubicacion['ubirefer']}'
        GROUP BY a.ubirefer, a.artrefer, b.ubitipo 
        ) as locx where locx.stock >= {$pendXPresen} order by locx.ubirefer asc limit 1";
    return $db->query($sql)->fetch_assoc()['stock'];
}

function obtenerUbicacionesPallet($db, $fila, $cantidad_unidades)
{
    $sql = "SELECT * from (
        select distinct 
        a.ubirefer,a.artrefer, b.ubitipo, sum(a.canti) as stock
        from stockubi a 
        inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
        where 
        a.cod_alma = '$fila->almrefer'
        AND a.artrefer = '$fila->artrefer' and 
        b.ubitipo IN ('PI') and a.ubirefer = '{$fila->ubicacion['ubirefer']}'
        GROUP BY a.ubirefer, a.artrefer, b.ubitipo 
        ) as locx where locx.stock >= {$cantidad_unidades} order by locx.ubirefer asc limit 1";
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
    $sql = "SELECT * FROM pedexdetcajas 
    WHERE  pedexentre = '$pedidoActual' 
    AND repocurso = true 
    AND expst=0 ";
    $resultado = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}

function obtener_presentaciones($db, $articulo)
{
    $sql = "SELECT canpresen as cantidad_presentacion, preseref as presentacion FROM artipresen where artrefer = '$articulo' ORDER BY FIND_IN_SET(preseref, 'PAL,CJ,UNI')";
    $res = $db->query($sql);
    $registros =  $res->fetch_all(MYSQLI_ASSOC);
    $valores_presentacion = "";
    $registro_significativo = $registros[0];
    $valores_presentacion = "1" . $registro_significativo['presentacion'] . "=";
    foreach ($registros as $registro) {
        if (!in_array($registro['presentacion'], [$registro_significativo["presentacion"], "UNI"])) {
            $valor = $registro_significativo["cantidad_presentacion"] / $registro['cantidad_presentacion'];
            $valores_presentacion .= $valor . $registro['presentacion'] . "=";
        }
    }
    $valores_presentacion .= $registro_significativo["cantidad_presentacion"] . "UNI";
    return $valores_presentacion;
}

function obtenerDatosPresentacionPallet($db, $articulo, $almacen)
{
    $sql = "SELECT * FROM artipresen WHERE artrefer = '$articulo' AND cod_alma = '$almacen' AND preseref = 'PAL'";
    $res = $db->query($sql);
    return $res->fetch_object();
}
