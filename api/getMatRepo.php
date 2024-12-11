<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/sql.php");
require_once("../logger/logger.php");
function retorna_resultado($respuesta, $estado = 200)
{
    header('Content-type: application/json; charset=utf-8', true, $estado);
    echo json_encode($respuesta);
    exit;
}
header('Content-type: application/json; charset=utf-8');

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
//$_POST["terminal"] = 'TR07';//
$terminal = $db->real_escape_string(strtoupper($_POST["terminal"])); // = 'TR07';
$ck_assign = "select pedido,cod_alma from assig_ped where tercod = '{$terminal}' and st = 0 limit 1";
//echo $ck_assign;
$rs = $db->query($ck_assign);
$ped_asignado = $rs->fetch_assoc();
$cod_alma = $ped_asignado['cod_alma'];

//obtener pedido para reposicion
$pedido = $ped_asignado['pedido'];
$sq = " SELECT *, 
(select sum(canti) as stock 
from stockubi c 
inner join ubimapa d on d.ubirefer = c.ubirefer and d.ubitipo =
'RE' and d.cod_alma = '$cod_alma' where c.artrefer = a.artrefer group by c.artrefer, d.ubirefer HAVING sum(canti) > 0 limit 1) as stock,
arti.artlotemar,
a.ubirefer 
from pedexdet a 
inner join arti on arti.artrefer = a.artrefer
inner join artipresen on artipresen.artrefer = arti.artrefer 
inner join pedexcab b on a.pedexentre = b.pedexentre 
where b.pedclase = 'REPO' and b.pedexentre = '$pedido' and b.almrefer='$cod_alma' and b.siturefe = 'PD' LIMIT 1";
$resultset = $db->query($sq);
if (!$resultset) {
    retorna_resultado(array("error" => true, "mensaje" => $db->errno));
}

$response = array("pedcod" => $ped_asignado['pedido'], "mensaje" => "");


while ($ax = $resultset->fetch_assoc()) {
    //    var_dump($ax);
    if ($ax['stock'] > 0) {
        $prensentacion = obtener_presentacion_cajas($db, $ax['artrefer']);
        $response['pedcod'] = $ax['pedexentre']; //[$cc]
        $response['matcod'] = $ax['artrefer']; //[$cc]
        $response['matdesc'] = $ax['artdesc']; //[$cc]
        $cantidad_reposicion = floatval($ax['canpedi']) - floatval($ax['canprepa']);
        $cantidad_reposicion_un = $cantidad_reposicion;

        if ($prensentacion) {
            $cantidad_reposicion = (int)(($ax['canpedi'] - $ax['canprepa']) / ($prensentacion->canpresen));
            $cantidad_extra = 0;
            $cantidad_reposicion_un = $cantidad_reposicion_un - $cantidad_reposicion*$prensentacion->canpresen;
            // si la cantidad de reposicion faltante con presentacion supera 75 % generar reposicion de 1 caja
            if (($cantidad_reposicion_un / $prensentacion->canpresen) >= 0.75) {
                $cantidad_extra = 1;
            }
            $cantidad_reposicion += $cantidad_extra;
        }
        $response['cantrepo'] = $cantidad_reposicion; //[$cc]
        $response['es_loteado'] = $ax['artlotemar']; //[$cc]
        $response['cantidad_presentacion'] = $ax['canpresen'];
        $response['um'] = $ax['preseref'];
        $response['cod_alma'] = $cod_alma; //[$cc]
        //    Ubicacion de reposición tipo RE Altura
        $sqRe = "SELECT a.ubirefer, a.artrefer, SUM(a.canti) as canti from stockubi a 
                    inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
                    left outer join repocerrados ON repocerrados.id_pedido = '{$ax['pedexentre']}' 
                    AND repocerrados.id_articulo = a.artrefer 
                    AND repocerrados.cod_alma = '{$cod_alma}' 
                    AND repocerrados.ubicacion = a.ubirefer 
                    where a.artrefer = '{$ax['artrefer']}' 
                    and b.ubitipo = 'RE'
                    and a.cod_alma='{$cod_alma}'
                    and repocerrados.id is null 
                    GROUP by  a.ubirefer, a.artrefer 
                    having SUM(a.canti)>={$ax['canpedi']}
                    order by a.ubirefer asc limit 1";
        $rR1 = $db->query($sqRe);

        if ($rR1->num_rows > 0) {
            $axR = $rR1->fetch_assoc();
            $response['ubiori'] = $axR['ubirefer']; //[$cc]
            //            Obtengo nros de series si existen
            $sq = "select artserie from serati where serubic = '{$axR['ubirefer']}' and artrefer = '{$ax['artrefer']}'";
            //            echo $sq;
            $xr = $db->query($sq);
            //            echo "llego";
            if ($xr->num_rows > 0) {
                while ($axx = $xr->fetch_assoc()) {
                    if (!$response['series'] && $response['series'] == '') {
                        $response['series'] = "" . $axx['artserie'] . "";
                    } else {
                        $response['series'] .= "-" . $axx['artserie'] . '';
                    }
                }
            } else {
                $response['series'] = '';
            }
        } else {
            $sqRe2 = "SELECT a.ubirefer, a.artrefer, SUM(a.canti) as canti from stockubi a 
            inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
            left outer join repocerrados ON repocerrados.id_pedido = '{$ax['pedexentre']}' 
            AND repocerrados.id_articulo = a.artrefer 
            AND repocerrados.cod_alma = '{$cod_alma}' 
            AND repocerrados.ubicacion = a.ubirefer 
            where a.artrefer = '{$ax['artrefer']}' 
            and b.ubitipo = 'RE'
            and a.cod_alma='{$cod_alma}' 
            and repocerrados.id is null
            GROUP by  a.ubirefer, a.artrefer 
            having SUM(a.canti)>0
            order by a.ubirefer asc limit 1";
            $rR2 = $db->query($sqRe2);
            if ($rR2->num_rows == 0) {
                $db->close();
                $response['error'] = true;
                $response['mensaje'] = "No existen ubicaciones disponibles";
                echo json_encode($response);
                exit;
            }
            $axR2 = $rR2->fetch_assoc();
            $response['series'] = "";
            $response['ubiori'] = $axR2['ubirefer']; //[$cc]

        }
        $codigo_articulo = $response['matcod'];
        $ubicacion = $response['ubiori'];

        // obtener lotes disponibles
        $sql = "SELECT stockubi.artlote, SUM(canti) AS cantidad 
        FROM stockubi 
        inner join loteart ON loteart.artrefer = stockubi.artrefer AND loteart.artlote = stockubi.artlote
        WHERE stockubi.artrefer = '{$codigo_articulo}' 
        AND stockubi.ubirefer='{$ubicacion}' 
        AND stockubi.cod_alma='{$cod_alma}'
        GROUP BY stockubi.artlote, stockubi.ubirefer HAVING SUM(canti) > 0 ORDER BY loteart.fecaduc ASC";
        $resultado_lotes = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        $response['lotes_disponibles'] = $resultado_lotes;
        //    Ubicación de destino tipo PI
        $ubicacion_destino = obtener_ubicacion_PI($db, $codigo_articulo);
        /*$sqPi = "SELECT
            a.artrefer,
            a.ubirefer ,
            a.artminrep,
            a.artmaxrep,
            a.cod_alma,
            arti.artdesc,
            ubimapa.ubitipo,
            arti.unimed,
            SUM(COALESCE (b.canti, 0)) as canti
        FROM
            artirepo a
        inner join arti on
            arti.artrefer = a.artrefer
        inner join ubimapa on
            ubimapa.ubirefer = a.ubirefer
            and a.cod_alma = ubimapa.cod_alma
        left join stockubi b on
            a.artrefer = b.artrefer
            and b.cod_alma = a.cod_alma
            and a.ubirefer = b.ubirefer
        WHERE
            a.cod_alma = '$cod_alma'
            AND ubimapa.ubitipo = 'PI'
            AND a.artrefer = '$codigo_articulo' 
            AND a.ubirefer = '$ubicacion_destino'
        GROUP BY
            a.artrefer,
            a.ubirefer";
        //                echo $sqRe;
        guardar_custom_log(__FILE__, $sqPi);
        $rP1 = $db->query($sqPi);
        $axP = $rP1->fetch_assoc();*/
        $response['ubidest'] = $ubicacion_destino; //[$cc]
        $cc++;
    } else {
        $response['mensaje'] = "Material sin stock.";
    }
}

if ($response['matcod']) {
    //    $response['data'] = $data;
    $response["error"] = FALSE;
} else {
    $response["error"] = TRUE;
}
echo json_encode($response);

//var_dump($response);
//
function obtener_presentacion_cajas($db, $articulo)
{
    $sql = "SELECT * from artipresen WHERE artrefer = '$articulo' AND preseref='CJ'";
    $resultado = $db->query($sql)->fetch_object();
    return $resultado;
}

function obtener_ubicacion_PI($db, $articulo)
{
    // obtiene la ubicacion de picking para la reposicion del articulo sin validar stock.
    $sql = "SELECT ubirefer FROM artirepo WHERE artrefer = '$articulo' LIMIT 1";
    $res = $db->query($sql);
    $datos = $res->fetch_object();
    return $datos->ubirefer;
}
