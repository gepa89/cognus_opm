<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");


function es_loteado($db, $codigo_articulo)
{
    $sql = "SELECT artlotemar FROM arti WHERE artrefer = '{$codigo_articulo}'";
    return $db->query($sql)->fetch_assoc()['artlotemar'] === "SI";
}
function obtenerCantidadDisponible($db, $codigo_articulo, $ubicacion, $cod_alma, $lote)
{
    $es_loteado = es_loteado($db, $codigo_articulo);
    $sql = "SELECT SUM(canti) AS cantidad 
    FROM stockubi 
    WHERE artrefer = '{$codigo_articulo}' 
    AND ubirefer='{$ubicacion}' 
    AND cod_alma='{$cod_alma}'
    GROUP BY ubirefer LIMIT 1";
    if ($es_loteado) {
        $sql = "SELECT artlote, SUM(canti) AS cantidad 
        FROM stockubi 
        WHERE artrefer = '{$codigo_articulo}' 
        AND ubirefer='{$ubicacion}' 
        AND cod_alma='{$cod_alma}'
        AND artlote = '{$lote}'
        GROUP BY artlote, ubirefer LIMIT 1";
    }
    guardar_info_log("send mat repo", $sql);

    $resultado = $db->query($sql)->fetch_assoc();
    return floatval($resultado['cantidad']);
}

header('Content-type: application/json; charset=utf-8');

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$db->begin_transaction();
//crear hash del estante
$reposicion = strtoupper($_POST['reposicion']);
$usuario = strtoupper($_POST['usuario']);
$picking = strtoupper($_POST['picking']);
$cantidad = strtoupper($_POST['cantidad']); //cantidad a mover
$pedido = strtoupper($_POST['pedido']);
$material = ($_POST['material']);
$serie = strtoupper($_POST['serie']);
$lote = trim($_POST['lote']);
// obtener codalma
$sql = "SELECT almrefer FROM pedexcab WHERE pedexentre = '$pedido'";
$resultado = $db->query($sql)->fetch_assoc();
$cod_alma = $resultado['almrefer'];

// validad cantidad solicitada
$cantidad_disponible = obtenerCantidadDisponible($db, $material, $reposicion, $cod_alma, $lote);
if ($cantidad_disponible < $cantidad) {
    $db->rollback();
    $db->close();
    echo json_encode(array("error" => true, "mensaje" => "Cantidad supera al stock disponible"));
    exit;
}
function cerrarPedido($db, $pedido, $cod_alma, $articulo)
{
    $response = array();
    $sql = "SELECT SUM(a.canti) as canti from stockubi a 
    inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
    left outer join repocerrados ON repocerrados.id_pedido = '{$pedido}' 
    AND repocerrados.id_articulo = a.artrefer 
    AND repocerrados.cod_alma = '{$cod_alma}' 
    AND repocerrados.ubicacion = a.ubirefer 
    where a.artrefer = '{$articulo}' 
    and b.ubitipo = 'RE'
    and a.cod_alma='{$cod_alma}'
    and repocerrados.id is null 
    GROUP by  a.ubirefer, a.artrefer 
    having SUM(a.canti) > 0";
    $query = $db->query($sql);
    if (!$query) {
        $response["error"] = TRUE;
        $response['mensaje'] = "Error al cerrar pedido.";
        $db->rollback();
        $db->close();
        echo json_encode($response);
        exit;
    }
    $existen_posicion_reposicion = $query->num_rows > 0;

    if ($existen_posicion_reposicion) {

        $response["error"] = FALSE;
        $response["estado"] = "pendiente_reposicion";
        $response['mensaje'] = "Datos guardados. 5";
        $db->commit(); //commit
        $db->close();
        echo json_encode($response);
        exit;
    }
    $sq_ck2 = "update pedexcab set siturefe = 'CE' where pedexentre = '{$pedido}'";
    //                 echo $sq_ck2."<br/>";
    if (!$db->query($sq_ck2)) {
        $response["error"] = TRUE;
        $response['mensaje'] = "Error al actualizar cabecera en el pedido.";
        $db->rollback();
        $db->close();
        echo json_encode($response);
        exit;
    }
    $sq_ck0 = "update assig_ped set st = 1 where pedido = '{$pedido}'";
    $db->query($sq_ck0);
    $response["error"] = FALSE;
    $response["estado"] = "completo";
    $response['mensaje'] = "Datos guardados. 3";
    $db->commit(); //commit
    $db->close();
    echo json_encode($response);
    exit;
}

//verifico cantidad en stock para posición de reposición
$filtro_lote = "";
$insertar_lote = "";
if (es_loteado($db, $material)) {
    $filtro_lote = " AND artlote = '{$lote}'";
    $insertar_lote = ",artlote='{$lote}'";
}
$sql = "INSERT INTO repocerrados SET id_articulo='{$material}', id_pedido='{$pedido}',ubicacion='{$reposicion}', cod_alma='{$cod_alma}' {$insertar_lote} ";
$query = $db->query($sql);
if (!$query) {
    $db->rollback();
    $db->close();
    echo json_encode(array("error" => true, "mensaje" => "Error insertar cerrarpedidos"));
}
$ckStockRe = "select sum(canti) as tot from stockubi where artrefer = '{$material}' and ubirefer = '{$reposicion}' and cod_alma='$cod_alma' group by artrefer, ubirefer";
//echo $ckStockRe;
$rs1 = $db->query($ckStockRe);
$rq1 = $rs1->fetch_assoc();
$totInOri = $rq1['tot']; /// cantidad de materiales dentro de stock en ubicacion de origen 464

$ckStockPi = "select sum(canti) as tot from stockubi where artrefer = '{$material}' and ubirefer = '{$picking}' and cod_alma='$cod_alma' group by artrefer, ubirefer";
$rs2 = $db->query($ckStockPi);
$rq2 = $rs2->fetch_assoc();
$totInDest = $rq2['tot']; /// cantidad de materiales dentro de stock en ubicacion de origen/ tiene que destino    1080
$ckPedi = "select canpedi, canpendi, canprepa from pedexdet where pedexentre = '{$pedido}' and artrefer = '{$material}' limit 1";
$rs3 = $db->query($ckPedi);
$rq3 = $rs3->fetch_assoc();
$canpedi = $rq3['canpedi'];
$canpendi = $rq3['canpendi'];
$canprepa = $rq3['canprepa'];
//echo "tot in dest: ".$totInDest." ----------";
if ($cantidad <= $totInOri) {
    //$db->autocommit(FALSE);
    //    saldo en origen
    $saldoOrigen = $totInOri - $cantidad;
    $saldoDestino = $cantidad + $totInDest;
    //     echo $saldoOrigen." ooooo<br/>";
    //      echo $saldoDestino." ooooo<br/>";

    $err = 0;
    if ($serie != '') {
        $rmv = array(']', '[');
        $serie = str_replace($rmv, '', $serie);
        $lsSer = explode(',', $serie);
        $lsSerStr = '';
        foreach ($lsSer as $k => $v) {
            if ($lsSerStr == '') {
                $lsSerStr = "'" . $v . "'";
            } else {
                $lsSerStr = ",'" . $v . "'";
            }
        }
        //        en caso de tener nro de serie modifico la ubicación de ese articulo
        $upd_u = "update serati set serubic = '{$picking}' where artrefer = '{$material}' and serubic = '{$reposicion}' and artserie in ({$lsSerStr})";
        //        echo $upd_u."<br/>";
        if (!$db->query($upd_u)) {
            $err = 1;
        }
    }
    if ($err == 0) {
        $valOri = $saldoOrigen;
        if ($valOri == 0) {
            //            si se vacía el stock, elimino de sotckubi
            $sq_ck = "delete from stockubi where artrefer = '{$material}' and ubirefer = '{$reposicion}' and cod_alma='$cod_alma' {$filtro_lote}";
            $sq_ubimap = "update ubimapa set ubisitu = 'VA' where ubirefer = '{$reposicion}' and cod_alma='$cod_alma'";
            $db->query($sq_ubimap);
            $flg_er = 0;
        } else if ($valOri > 0) {
            //$sq_ck  = "update stockubi set canti = {$saldoOrigen} where artrefer = '{$material}' and ubirefer = '{$reposicion}'";
            $negCant = -1 * $cantidad;
            $sq_ck = "insert into stockubi set canti = {$negCant}, artrefer = '{$material}', ubirefer = '{$reposicion}', etnum = 'REPO', cod_alma='$cod_alma' {$insertar_lote}";
            $flg_er = 0;
            $sq_ubimap = "update ubimapa set ubisitu = 'LL' where ubirefer = '{$picking}' and cod_alma='$cod_alma'";
            $db->query($sq_ubimap);
            $flg_er = 0;
        } else if ($valOri < 0) {
            $flg_er = 1;
        }
        if ($flg_er == 0) {
            if ($db->query($sq_ck)) {

                if ($canpendi == 0 && $canpedi > $canprepa) {
                    $canpendi = $canpedi - $cantidad;
                    $canprepa = $cantidad;
                } else {
                    $canpendi = $canpendi - $cantidad;
                    $canprepa = $canprepa + $cantidad;
                }

                if ($canpendi < 0) {
                    $presentacion = obtener_presentacion_cajas($db, $material);
                
                    if ($presentacion && abs($canpendi) <= $presentacion->canpresen) {
                        $cantidad = $presentacion->canpresen + $canpendi;
                        $sql = "UPDATE pedexdet SET excedente = $cantidad WHERE artrefer = '{$material}' AND pedexentre = '{$pedido}'";
                        $db->query($sql);
                        $canpendi = 0;
                    } else {
                        $response["error"] = TRUE;
                        $response['mensaje'] = "Cantidad ingresada supera lo solicitado.";
                        $db->rollback();
                        $db->close();
                        echo json_encode($response);
                        exit;
                    }
                }

                $sq_ck = "update pedexdet set fechcie = now(), horcie = now(), usuario = '{$usuario}',canpendi = {$canpendi},canprepa = {$canprepa} where artrefer = '{$material}' and pedexentre = '{$pedido}'";
                //            echo $sq_ck."<br/>";
                if (!$db->query($sq_ck)) {
                    $response["error"] = TRUE;
                    $response['mensaje'] = "Error al actualizar posición en el pedido.";
                    $db->rollback();
                    echo json_encode($response);
                    exit;
                }
                //$sq_ck0  = "update stockubi set canti = {$saldoDestino} where artrefer = '{$material}' and ubirefer = '{$picking}'";
                $sq_ck0 = "insert into stockubi set canti = {$cantidad}, artrefer = '{$material}', ubirefer = '{$picking}', etnum = 'REPO', cod_alma='$cod_alma' {$insertar_lote}";
                //                echo $sq_ck0;
                $db->query($sq_ck0);
                if ($canpendi == 0) {
                    $sq_ck2 = "update pedexcab set siturefe = 'CE' where pedexentre = '{$pedido}'";
                    //                 echo $sq_ck2."<br/>";
                    if ($db->query($sq_ck2)) {
                        $sq_ck0 = "update assig_ped set st = 1 where pedido = '{$pedido}'";
                        $db->query($sq_ck0);
                        $db->commit(); //commit
                        $db->close();
                        $response["error"] = FALSE;
                        $response["estado"] = "completo";
                        $response['mensaje'] = "Datos guardados. 1";
                        echo json_encode($response);
                        exit;
                    } else {
                        $response["error"] = TRUE;
                        $response['mensaje'] = "Error al actualizar cabecera del pedido.";
                        $db->rollback();
                        $db->close();
                        echo json_encode($response);
                        exit;
                    }
                } else {
                    /*try {
                        $presentacion = obtener_presentacion_cajas($db, $material);
                        if ($presentacion && ($canpendi / $presentacion->canpresen) < 0.75) {
                            $sql_update_pedido = "update pedexcab set siturefe = 'CE' where pedexentre = '{$pedido}'";
                            $sql_update_asignacion = "update assig_ped set st = 1 where pedido = '{$pedido}'";
                            $db->query($sql_update_pedido);
                            $db->query($sql_update_asignacion);
                            $db->commit(); //commit
                            $db->close();
                            $response = [];
                            $response["error"] = false;
                            $response["estado"] = "completo";
                            $response['mensaje'] = "Datos guardados";
                            echo json_encode($response);
                            exit;
                        }
                    } catch (\Throwable $th) {
                        guardar_error_log(__FILE__, $th->getMessage());
                    }*/
                    cerrarPedido($db, $pedido, $cod_alma, $material);
                }
                //pendiente de reposicion, hay stock en altura
                /*if (($totInOri - $canpendi) > 0) {
                    $response["error"] = FALSE;
                    $response["estado"] = "pendiente_reposicion";
                    $response['mensaje'] = "Datos guardados. 2";
                    $db->rollback(); //commit
                    $db->close();
                    echo json_encode($response);
                    exit;

                } else {
                    $sq_ck2 = "update pedexcab set siturefe = 'CE' where pedexentre = '{$pedido}'";
                    //                 echo $sq_ck2."<br/>";
                    if (!$db->query($sq_ck2)) {
                        $response["error"] = TRUE;
                        $response['mensaje'] = "Error al actualizar cabecera en el pedido.";
                        $db->rollback();
                        $db->close();
                        echo json_encode($response);
                        exit;
                    }
                    ;
                    $sq_ck0 = "update assig_ped set st = 1 where pedido = '{$pedido}'";
                    $db->query($sq_ck0);
                    $response["error"] = FALSE;
                    $response["estado"] = "completo";
                    $response['mensaje'] = "Datos guardados. 3";
                    $db->rollback(); //commit
                    $db->close();
                    echo json_encode($response);
                    exit;
                }*/
            } else {
                $response["error"] = TRUE;
                $response['mensaje'] = "Error al liberar ubicación.";
            }
        } else {
            $response["error"] = TRUE;
            $response['mensaje'] = "Cantidad en ubicación RE insuficiente.";
        }
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo reubicar por número de serie.";
    }
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "Material sin Stock.";
}


//
//if($pedido != ''){
//    $sq = "delete from assig_ped where tercod = '{$terminal}' and pedido = '{$pedido}'";
//    if($db->query($sq)){
//        $response["error"] = FALSE;
//        $response['mensaje'] = "Asignación Anulada";
//    }else{
//        $response["error"] = TRUE;
//        $response['mensaje'] = "No se pudo anular asignación.";
//    }
//}else{
//    $response["error"] = TRUE;
//    $response['mensaje'] = "No hay pedido asignado a esta terminal.";
//}
// 
$db->close();
echo json_encode($response);
exit;

function obtener_presentacion_cajas($db, $articulo)
{
    $sql = "SELECT * from artipresen WHERE artrefer = '$articulo' AND preseref='CJ'";
    $resultado = $db->query($sql)->fetch_object();
    return $resultado;
}
