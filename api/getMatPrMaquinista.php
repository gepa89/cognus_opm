<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../modelos/UbicacionArticulo.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
header('Content-type: application/json; charset=utf-8');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
$db->begin_transaction();

$db->set_charset("utf8");
$terminal = strtoupper($_POST['tercod']);

$sqr = "select lectura from config";
$rr = $db->query($sqr);
$rrx = $rr->fetch_assoc();
$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";
$cc = 0;
$r1 = $db->query($sq);
$ser = '';

$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
//echo $sqz;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$fstDataMat = array();
$response = array();
$multireferencia = "";
try {
    while ($ax = $r1->fetch_assoc()) {

        //    por cada pedido para preparar, traigo sus materiales
        ///$cod_alma = $ax['']
        $cod_alma = $ax['cod_alma'];
        $pedido = $ax['pedido'];
        $multireferencia = trim($ax['multiref']);
        $codigo_pedido = trim($ax['pedido']);
        $fstData[$ax['pedido']]['pedido'] = $ax['pedido'];
        $fstData[$ax['pedido']]['multiref'] = $ax['multiref'];
        $sq2 = "SELECT
                    distinct
                    a.artrefer,
                    a.cantiu,
                    a.canupen,
                    a.canubi,
                    b.artdesc,
                    b.artser,
                    a.etnum ,
                    a.muelle,
                    a.etnum,
                    a.posubi,
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
                inner join arti b on
                    a.artrefer = b.artrefer
                inner join artubiref c on
                    a.artrefer = c.artrefer
                inner join ubimapa d on
                    c.ubirefer = d.ubirefer and d.cod_alma = '$cod_alma'
                left join artipresen e on a.artrefer = e.artrefer and e.cod_alma = '$cod_alma' 
                where
                    a.pedubicod = '$codigo_pedido'
                    and COALESCE(a.canubi, 0) < COALESCE (a.cantiu,
                    0) 
                    and expst = 0";
        $rs = $db->query($sq2);

        // cambiar
        while ($ax2 = $rs->fetch_assoc()) {

            $sql_presentacion = "SELECT * FROM artipresen 
                WHERE artrefer = '{$ax2['artrefer']}' 
                AND cod_alma = '$cod_alma' 
                AND preseref IN ('CJ','UNI') 
                ORDER BY FIND_IN_SET(preseref, 'CJ,UNI')";
            $rs_presentacion = $db->query($sql_presentacion);
            $presentacion = $rs_presentacion->fetch_assoc();
            $fstData[$ax['pedido']]['cantsku']++;
            $ax2['artrefer'] = $db->real_escape_string($ax2['artrefer']);
            $material = trim($ax2['artrefer']);
            //nuevos
            $cod_articulo = trim($ax2['artrefer']);
            $cod_almacen = trim($ax2['cod_alma']);

            $sql_eliminar = "DELETE FROM ubitempo WHERE 
                cod_alma='$cod_almacen'
                AND multiref='$multireferencia'
                AND tercod='$terminal'
                AND pedido='$pedido'
                AND artrefer='$material'
                AND estado_ubicacion=False";
            $db->query($sql_eliminar);
            $sqlRX = "SELECT
                        artubiref.ubirefer,
                        artubiref.cod_alma,
                        ubimapa.ubitipo,
                        ubimapa.ubisitu,
                        ubimapa.tipoubi 
                    FROM
                        ubimapa,
                        artubiref
                    LEFT JOIN ubitempo ON ubitempo.ubirefer=artubiref.ubirefer AND ubitempo.artrefer=artubiref.artrefer
                    WHERE
                        ubimapa.ubirefer = artubiref.ubirefer
                        AND ubimapa.cod_alma = artubiref.cod_alma
                        AND artubiref.artrefer = '$cod_articulo'
                        AND artubiref.cod_alma = '$cod_almacen'
                        AND COALESCE (ubitempo.estado_ubicacion,False) = False
                        AND ubimapa.ubitipo IN ('RE','PI','PS') 
                        ORDER BY FIND_IN_SET(ubimapa.ubitipo, 'PI,RE,PS')";
            $rxd = $db->query($sqlRX);

            $primer_resultado = $rxd->fetch_assoc();

            $lugar_ubicacion = null;

            if ($primer_resultado) {
                if ($primer_resultado['ubitipo'] == 'RE' && $primer_resultado['ubisitu'] == 'VA') {
                    $lugar_ubicacion = $primer_resultado;
                }
            }
            if (!$lugar_ubicacion) {

                if (!$primer_resultado) {
                    $sql_picking = "SELECT
                            artubiref.ubirefer,
                            artubiref.cod_alma,
                            ubimapa.ubitipo,
                            ubimapa.ubisitu,
                            ubimapa.tipoubi 
                        FROM
                            ubimapa,
                            artubiref
                        WHERE
                            ubimapa.ubirefer = artubiref.ubirefer
                            AND ubimapa.cod_alma = artubiref.cod_alma
                            AND artubiref.artrefer = '$cod_articulo'
                            AND artubiref.cod_alma = '$cod_almacen'
                            AND ubimapa.ubitipo='PI'";
                    $res_picking = $db->query($sql_picking);

                    $primer_resultado = $res_picking->fetch_assoc();
                } else {

                    if ($primer_resultado['ubitipo'] == 'PS') {
                        continue;
                    }
                }

                //$lugar_ubicacion = $primer_resultado;
                // si esta lleno buscar ubicacion libre mas proxima independiente 
                //del producto con el codigo del primer resultado
                // obtiene todos los huecos del almacen con posiciones
                $lugar_ubicacion = "";
                $codigo = $primer_resultado['ubirefer'];
                $tipoubi = $primer_resultado['tipoubi'];
                $sql = "SELECT
                            t.ubirefer,
                            t.ubisitu,
                            t.ubitipo,
                            t.cod_alma,
                            @rownum := @rownum + 1 AS posicion
                        FROM
                            (SELECT * from ubimapa WHERE cod_alma='$cod_almacen'
                             AND tipoubi='$tipoubi'
                             AND ubirefer NOT IN (
                                SELECT ubirefer 
                                FROM ubitempo 
                                WHERE cod_alma='$cod_almacen')
                            ) t ,
                            (
                            SELECT
                                @rownum := 0) r
                        order by
                            ubirefer ASC";
                $sql_ubicar_posicion = "SELECT * FROM ({$sql}) AS resultados WHERE ubirefer='$codigo' LIMIT 1";
                $res = $db->query($sql_ubicar_posicion);
                $posicion_ubicacion = $res->fetch_assoc()['posicion'];

                $sql_menor = "SELECT * 
                              FROM ({$sql}) AS resultados 
                              WHERE ubirefer < '$codigo' 
                                AND ubitipo='RE' 
                                AND ubisitu='VA' LIMIT 1";
                $res = $db->query($sql_menor);
                $ubicacion_menor_disponible = $res->fetch_assoc();
                $sql_mayor = "SELECT * 
                              FROM ({$sql}) AS resultados 
                              WHERE ubirefer > '$codigo' 
                                AND ubitipo='RE' 
                                AND ubisitu='VA' LIMIT 1";
                $res = $db->query($sql_mayor);
                $ubicacion_mayor_disponible = $res->fetch_assoc();

                $diferencia_espacio_menor = null;
                $diferencia_espacio_mayor = null;

                if ($ubicacion_menor_disponible) {
                    $diferencia_espacio_menor = (int) $posicion_ubicacion - (int) $ubicacion_menor_disponible['posicion'];
                }
                if ($ubicacion_mayor_disponible) {
                    $diferencia_espacio_mayor = (int) $ubicacion_mayor_disponible['posicion'] - (int) $posicion_ubicacion;
                }

                if (is_null($diferencia_espacio_menor) && !is_null($diferencia_espacio_mayor)) {
                    $lugar_ubicacion = $ubicacion_mayor_disponible;
                } else if (!is_null($diferencia_espacio_menor) && is_null($diferencia_espacio_mayor)) {
                    $lugar_ubicacion = $ubicacion_menor_disponible;
                } else {
                    $lugar_ubicacion = $primer_resultado;
                }
            }
            $fecha = date("Y-m-d H:i:s");
            if ($lugar_ubicacion) {
                $ubirefer = trim($lugar_ubicacion['ubirefer']);
                $existe_ubitempo = "SELECT * FROM ubitempo WHERE ubirefer = ? AND cod_alma = ? AND multiref = ? AND tercod = ? AND pedido = ? AND artrefer = ?";

                $sqlbuilder = new MySqlQuery($db);
                $ubitempo = $sqlbuilder->rawQuery($existe_ubitempo, [$ubirefer, $cod_almacen, $multireferencia, $terminal, $pedido, $material])->getOne();
                if (!$ubitempo) {
                    $sql_insertar = "INSERT INTO ubitempo SET 
                                    ubirefer = '$ubirefer', 
                                    cod_alma='$cod_alma', 
                                    multiref='$multireferencia',
                                    tercod='$terminal', 
                                    pedido='$pedido',
                                    artrefer='$material',
                                    fecha='$fecha'";
                    try {
                        $res = $db->query($sql_insertar);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
            $tipo_presentacion = $presentacion['preseref'];
            $cantidad_presentacion = $presentacion['canpresen'];
            $cantidad_restante = floor($ax2['canupen'] / $presentacion['canpresen']);
            if ($cantidad_restante == 0) {
                $cantidad_restante = $ax2['canupen'];
                $tipo_presentacion = "UNI";
                $cantidad_presentacion = 1;
            }
            $fstDataMat[$cc]['ubirefer'] = separateLocation($lugar_ubicacion['ubirefer']);
            $fstDataMat[$cc]['tipo'] = '' . $ax2['artser'];
            $fstDataMat[$cc]['ean'] = $ax2['ean'];
            $fstDataMat[$cc]['pedido'] = $ax['pedido'];
            $fstDataMat[$cc]['artrefer'] = $ax2['artrefer'];
            $fstDataMat[$cc]['presenref'] = $tipo_presentacion;
            $fstDataMat[$cc]['multiref'] = $ax2['etnum'];
            $fstDataMat[$cc]['posicion'] = $ax2['posubi'];
            $fstDataMat[$cc]['presenmul'] = $cantidad_presentacion;
            $fstDataMat[$cc]['presen'] = $cantidad_presentacion . "x" . $tipo_presentacion;
            $fstDataMat[$cc]['artdesc'] = $ax2['artdesc'];
            $fstDataMat[$cc]['cantiu'] = $ax2['cantiu'] / $presentacion['canpresen'];
            $fstDataMat[$cc]['canupen'] = $cantidad_restante;
            $fstDataMat[$cc]['muelle'] = $ax2['muelle'];
            $fstDataMat[$cc]['cod_almacen'] = $cod_alma;
            $fstDataMat[$cc]['capacidad_ubicacion'] = obtenerCapacidadUbicacion($lugar_ubicacion['ubirefer'], $material);
            //
            $value = SepararUbicacion($fstDataMat[$cc]['ubirefer']);
            $pasillo = $value[0];
            $hueco = $value[1];
            $nivel = $value[2];
            $sqlParaOrdenar = "SELECT rutid
                                FROM rutadet
                                    INNER JOIN ruta ON rutadet.rutcod = ruta.rutcod
                                    AND ruta.rutesta = 1
                                WHERE (
                                        ubiestan <= '$pasillo'
                                        AND ubiestanh >= '$pasillo'
                                    )
                                    AND (
                                        (
                                            ubihuec <= '$hueco'
                                            AND ubihuech >= '$hueco'
                                        )
                                        OR (
                                            ubihuec >= '$hueco'
                                            AND ubihuech <= '$hueco'
                                        )
                                    )
                                    AND (
                                        ubiniv <= '$nivel'
                                        AND ubinivh >= '$nivel'
                                    )
                                    AND rutadet.rutcod = ruta.rutcod 
                                    AND rutadet.cod_alma = '$cod_alma'
                                LIMIT 1;";
            $qxd = $db->query($sqlParaOrdenar);
            $datN = $qxd->fetch_array(MYSQLI_ASSOC);
            $fstDataMat[$cc]['ordenN'] = $datN['rutid'];
            $cc++;
        }
        $sq = "update serati set sersitu = 0 where artserie in ({$ser})";
        $db->query($sq);
        $sq = "select * from ruta_ubic";
        $rubi = $db->query($sq);
        while ($asx = $rubi->fetch_assoc()) {
            $ubica[$asx['orden']] = $asx['ubirefer'];
        }
        //var_dump($fstDataMat);
        foreach ($fstDataMat as $k => $dat) {
            $key = array_search($dat['ubirefer'], $ubica);
            $data[$key][] = $dat;
        }
        ksort($data);
        $ak = 0;
        $data2 = array();
        foreach ($data as $k => $dat) {
            foreach ($dat as $v => $dat2) {
                $dat2['cantsku'] = $fstData[$dat2['pedido']]['cantsku'];
                //$dat2['multiref'] = $fstData[$dat2['pedido']]['multiref'];
                $data2[] = $dat2;
            }
        }
        if (count($data) > 0) {
            $orden = OrdenarArray($data2, 'ordenN');
            array_multisort($orden, SORT_ASC, $data2);
            $response['ruta'] = $data2;
            $response["error"] = FALSE;
        } else {
            $response["error"] = TRUE;
            $response['mensaje'] = "Sin pedidos procesables.";
        }
    }
    guardar_info_log(__FILE__, json_encode($response));
} catch (\Throwable $th) {
    throw $th;
    guardar_error_log(__FILE__, json_encode($th->getMessage()));
}
//$db->rollback();
$db->commit();
$db->close();
//echo "<pre>";var_dump($fstDataMat);echo "</pre>";
echo json_encode($response);
exit();
function SepararUbicacion($ubi)
{
    //$ubi = filter_var($ubi, FILTER_SANITIZE_NUMBER_INT); 
    $separador = "-";
    $separada = explode($separador, $ubi);
    $separada[2] = substr($separada[2], 0, 2);
    return $separada;
}

function OrdenarArray($data, $columna)
{
    $orden1 = array();
    foreach ($data as $clave => $fila) {
        $orden1[$clave] = $fila[$columna];
    }
    return $orden1;
}

function obtenerUbicacionPI($db, $sql, $codigo)
{
    $sql_menor = "SELECT * 
    FROM ({$sql}) AS resultados 
    WHERE ubirefer < '$codigo' 
      AND ubitipo='PI' 
      AND ubisitu='VA' LIMIT 1";
    $res = $db->query($sql_menor);
    $ubicacion_menor_disponible = $res->fetch_assoc();
    $sql_mayor = "SELECT * 
    FROM ({$sql}) AS resultados 
    WHERE ubirefer > '$codigo' 
      AND ubitipo='PI' 
      AND ubisitu='VA' LIMIT 1";
    $res = $db->query($sql_mayor);
    $ubicacion_mayor_disponible = $res->fetch_assoc();
    return [$ubicacion_menor_disponible, $ubicacion_mayor_disponible];
}

function obtenerCapacidadUbicacion($ubicacion, $articulo)
{
    $respuesta = "";
    $capacidad = UbicacionArticulo::obtenerCapacidadUbicacionTipoPS($articulo, $ubicacion);
    if ($capacidad) {
        $capacidad_libre = $capacidad->capacidad - $capacidad->total_ocupado;
        $respuesta = $capacidad_libre . " / " . $capacidad->capacidad;
    }
    return $respuesta;
}
