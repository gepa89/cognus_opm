<?php

/*trabajo maquinista ubicacion en piso */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../utils/respuesta.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
//$db->begin_transaction();
$db->set_charset("utf8");
$terminal = strtoupper($_POST['tercod']);

$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";

$r1 = $db->query($sq);
$pedido = (object) $r1->fetch_assoc();

$cod_alma = $pedido->cod_alma;
$codigo_pedido = $pedido->pedido;

$sql_detalle_pedido = "SELECT
                    distinct
                    a.pedubicod,
                    a.artrefer,
                    a.cantiu,
                    a.canupen,
                    a.canubi,
                    b.artdesc,
                    b.artser,
                    a.etnum ,
                    e.canpresen,
                    e.preseref,
                    a.muelle,
                    a.etnum,
                    a.posubi,
                    p.fecubi as fecha_ingreso_pedido,
                    diasmezcla.candias,
                    diasmezcla.mezmisprod,
                    (
                    select
                        GROUP_CONCAT(ean) as eans
                    from
                        artean c
                    where
                        c.artrefer = a.artrefer
                    GROUP BY
                        c.artrefer) as ean,
                    d.zoncodpre,
                    d.cod_alma 
                from
                    pedubidet a
                inner  join pedubicab p on p.pedubicod = a.pedubicod  and p.cod_alma = '$cod_alma'
                inner join diasmezcla on diasmezcla.artrefer = a.artrefer
                inner join arti b on
                    a.artrefer = b.artrefer
                left join artubiref c on
                    a.artrefer = c.artrefer
                left join ubimapa d on
                    c.ubirefer = d.ubirefer and d.cod_alma = '$cod_alma'
                left join artipresen e on a.artrefer = e.artrefer and e.cod_alma = '$cod_alma' 
                where
                    a.pedubicod = '$codigo_pedido'
                    and COALESCE(a.canubi, 0) < COALESCE (a.cantiu,
                    0) 
                    and expst = 0";
$sql_builder = new MySqlQuery($db);
$detalle_pedido = $sql_builder->rawQuery($sql_detalle_pedido)->getOne();

$material = trim($detalle_pedido->artrefer);
$cod_articulo = trim($detalle_pedido->artrefer);
$cod_almacen = trim($detalle_pedido->cod_alma);
$puede_mezclar_articulo = $detalle_pedido->mezmisprod == "SI";
$fecha_ingreso_pedido = $detalle_pedido->fecha_ingreso_pedido;
$cantidad_dias_mezcla = $detalle_pedido->candias;

if ($puede_mezclar_articulo) {
    /* Si es posible mezclar lotes del mismo artículo, entonces buscamos 
    ubicaciones con stock de tipo "PS" y que cumplan con el límite de días para poder mezclar */
    $parametros = [$fecha_ingreso_pedido, $material, $fecha_ingreso_pedido, $cantidad_dias_mezcla];
    $sql = "SELECT
             datediff(?, fecingre) as diferencia, 
             stockubi.ubirefer,
             sum(stockubi.canti) as cantidad_ocupada,
             stockubi.fecingre,
             capaubi.capacidad,
             stockubi.ubirefer,
             (select sum(canti) from stockubi where ubirefer=stockubi.ubirefer) as cantidad_disponible
        from
            stockubi
        inner join ubimapa ON ubimapa.ubirefer = stockubi.ubirefer 
        inner join capaubi on capaubi.artrefer=stockubi.artrefer and capaubi.dimension=ubimapa.dimension
        where
            stockubi.artrefer = ? and ubimapa.ubitipo='PS'
        group by
            ubirefer,fecingre
        having sum(canti) > 0 and datediff(?, fecingre) <= ? and capaubi.capacidad > sum(stockubi.canti)
        order by fecingre asc limit 1";
    $sqlbuilder = new MySqlQuery($db);

    $ubicacion_encontrada = $sqlbuilder->rawQuery($sql, $parametros)->getOne();
    //todo: retornar ubicacion seleccionada
    if ($ubicacion_encontrada) {
        $db->close();
        $resultado = generarRespuesta($cod_alma, $lugar_encontrado, $detalle_pedido);
        $respuesta = ['error' => false, 'ruta' => [$resultado]];
        retorna_resultado(200, $respuesta);
    }
}

$sql = "SELECT
                artubiref.artrefer as articulo,
                artubiref.ubirefer,
                artubiref.cod_alma,
                ubimapa.ubitipo,
                ubimapa.ubisitu,
                ubimapa.tipoubi,
                (select
                        COUNT(*)
                    from
                        (
                        select
                            sum(stockubi.canti) as cantidad,
                            stockubi.artrefer
                        from
                            stockubi
                        inner join ubimapa ON
                            ubimapa.ubirefer = stockubi.ubirefer
                        where
                            ubimapa.ubitipo = 'PS'
                            and stockubi.artrefer <> articulo
                        group by
                            stockubi.ubirefer,stockubi.artrefer 
                        having
                            cantidad>0) as cantidad_stock) as cantidad_articulos_distintos,
                    (
                    SELECT
                        COALESCE(sum(canti), 0)
                    from
                        stockubi
                    where
                        ubirefer = artubiref.ubirefer and artrefer and artrefer=articulo) as cantidad_stock,
                (select COUNT(*) from (select
                    sum(stockubi.canti) as cantidad,
                    stockubi.artrefer
                from
                    stockubi
                inner join ubimapa ON
                    ubimapa.ubirefer = stockubi.ubirefer
                where
                    stockubi.ubirefer = stockubi.ubirefer
                    and ubimapa.ubitipo = 'PS'
                    and artubiref.artrefer <> articulo
                group by
                    stockubi.ubirefer
                having
                    cantidad>0) as cantidad_stock) as cantidad_otro,
                (SELECT COALESCE(sum(canti),0) from stockubi where ubirefer=artubiref.ubirefer) as cantidad_stock,
                (SELECT capacidad  from capaubi inner join ubimapa on capaubi.dimension=ubimapa.dimension And ubimapa.ubirefer=artubiref.ubirefer and capaubi.artrefer=artubiref.artrefer) as capacidad
            FROM
                ubimapa,
                artubiref
            WHERE
                ubimapa.ubirefer = artubiref.ubirefer
                AND ubimapa.cod_alma = artubiref.cod_alma
                AND artubiref.artrefer = '$cod_articulo'
                AND artubiref.cod_alma = '$cod_almacen'
                AND ubimapa.ubitipo IN ('PS') 
                ORDER BY artubiref.ubirefer ASC LIMIT 1";
$sqlbuilder = new MySqlQuery($db);
$lugar_ubicacion = $sqlbuilder->rawQuery($sql)->getOne();

if (!$lugar_ubicacion) {
    $db->close();
    $respuesta = ['error' => true, 'mensaje' => "Ubicacion de referencia no encontrada"];
    retorna_resultado(200, $respuesta);
}

$lugar_encontrado = null;
if ($lugar_ubicacion->cantidad_stock == 0 && $lugar_ubicacion->cantidad_articulos_distintos == 0) {
    $lugar_encontrado = $lugar_ubicacion;
}
if (!$lugar_encontrado) {
    /* Buscamos ubicacion mas proxima a la ubicacion de refernecia */
    $sql = "SELECT
                        t.ubirefer,
                        t.ubisitu,
                        t.ubitipo,
                        t.cod_alma,
                        @rownum := @rownum + 1 AS posicion
                    FROM
                        (SELECT * from ubimapa WHERE cod_alma='$cod_almacen'
                         AND tipoubi='$lugar_ubicacion->tipoubi') t,
                        (
                        SELECT
                            @rownum := 0) r
                    order by
                        ubirefer ASC";
    $ubicacion = $lugar_ubicacion->ubirefer;
    $sql_ubicar_posicion = "SELECT * FROM ({$sql}) AS resultados WHERE ubirefer=? LIMIT 1";
    $sqlbuilder = new MySqlQuery($db);
    $resultado = $sqlbuilder->rawQuery($sql_ubicar_posicion, [$ubicacion])->getOne();
    if (!$resultado) {
        $db->close();
        $respuesta = ['error' => true, 'mensaje' => "Ninguna ubicacion encontrada"];
        retorna_resultado(200, $respuesta);
    }
    $posicion_ubicacion = $resultado->posicion;

    $sql_menor = "SELECT * 
                          FROM ({$sql}) AS resultados 
                          WHERE ubirefer < '$ubicacion' 
                            AND ubitipo='PS' 
                            AND ubisitu='VA' LIMIT 1";

    $sqlbuilder = new MySqlQuery($db);
    $ubicacion_menor_disponible = $sqlbuilder->rawQuery($sql_menor)->getOne();

    $sql_mayor = "SELECT * 
                          FROM ({$sql}) AS resultados 
                          WHERE ubirefer > '$ubicacion' 
                            AND ubitipo='PS' 
                            AND ubisitu='VA' LIMIT 1";
    $sqlbuilder = new MySqlQuery($db);
    $ubicacion_mayor_disponible = $sqlbuilder->rawQuery($sql_mayor)->getOne();

    $diferencia_espacio_menor = null;
    $diferencia_espacio_mayor = null;

    if ($ubicacion_menor_disponible) {
        $diferencia_espacio_menor = (int) $posicion_ubicacion - (int) $ubicacion_menor_disponible->posicion;
    }
    if ($ubicacion_mayor_disponible) {
        $diferencia_espacio_mayor = (int) $ubicacion_mayor_disponible->posicion - (int) $posicion_ubicacion;
    }

    if (is_null($diferencia_espacio_menor) && !is_null($diferencia_espacio_mayor)) {
        $lugar_encontrado = $ubicacion_mayor_disponible;
    } else if (!is_null($diferencia_espacio_menor) && is_null($diferencia_espacio_mayor)) {
        $lugar_encontrado = $ubicacion_menor_disponible;
    } else {
        $db->close();
        $respuesta = ['error' => true, 'mensaje' => "No se encontro ninguna ruta disponible"];
        retorna_resultado(200, $respuesta);
    }
}


$resultado = generarRespuesta($cod_alma, $lugar_encontrado, $detalle_pedido);
$db->close();
$respuesta = ['error' => false, 'ruta' => [$resultado]];
retorna_resultado(200, $respuesta);


function generarRespuesta($cod_alma, $lugar, $detalle_pedido)
{
    $resultado = new stdClass();
    $resultado->ubirefer = separateLocation($lugar->ubirefer);
    $resultado->ean = $detalle_pedido->ean;
    $resultado->pedido = $detalle_pedido->pedubicod;
    $resultado->artrefer = $detalle_pedido->artrefer;
    $resultado->presenref = $detalle_pedido->preseref;
    $resultado->multiref = $detalle_pedido->etnum;
    $resultado->posicion = $detalle_pedido->posubi;
    $resultado->presenmul = $detalle_pedido->canpresen;
    $resultado->presen = $detalle_pedido->canpresen . "x" . $detalle_pedido->preseref;
    $resultado->artdesc = $detalle_pedido->artdesc;
    $resultado->cantiu = $detalle_pedido->cantiu;
    $resultado->canupen = $detalle_pedido->canupen;
    $resultado->muelle = $detalle_pedido->muelle;
    $resultado->cod_almacen = $cod_alma;
    $resultado->tipo = $detalle_pedido->artser;
    return $resultado;
}
