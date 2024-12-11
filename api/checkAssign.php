<?php
require('../conect.php');
require_once('../logger/logger.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
//crear hash del estante
$user = $_POST['user'];
$terminal = strtoupper($_POST['terminal']);

$response = [
    'multireferencia' => null
];
if (isset($terminal)) {

    //    Obtengo todos los pedidos de recepcion asignados a la terminal
    $sq = "SELECT pedido, st,tsku.cod_alma, 
                (select count(artrefer) as tsku from pedredet b where pedrefer = pedido AND b.tienekit <> 'SI') as tsku,
                (select pedclase as pclase from pedrecab c where pedrefer = pedido) as clase 
            FROM assig_ped as tsku where tercod = '{$terminal}' and st = 0 limit 1";
    $rs = $db->query($sq);
    $pedlist = '';
    while ($ax = $rs->fetch_assoc()) {
        $pedido = $ax['pedido'];
        if ($pedlist != '') {
            $pedlist .= ",'" . $ax['pedido'] . "'";
        } else {
            $pedlist = "'" . $ax['pedido'] . "'";
        }
        $pedido = $ax['pedido'];
        $response['materiales'][] = $ax;
        $sql = "SELECT pedredet.artrefer,
                SUM(pedredet.canpedi) as cantidad_pedida,
                (SELECT COUNT(*) from capaubi where artrefer=pedredet.artrefer) as ubicacion_piso
                FROM pedredet
                WHERE pedrefer ='$pedido' group by
                pedredet.artrefer";
        
        //$sql = "SELECT * FROM pedredet WHERE pedrefer = '$pedido'";
        //$datos_pedredet = $db->query($sql);

    }

    if (count($response['materiales']) > 0) {
        //        reviso si existen pallets en el pedido
        $sqp = "select distinct pal_desc, pal_status from pallets where pal_pedido in ({$pedlist})";
        $rp1 = $db->query($sqp);
        if ($rp1->num_rows == 0) {
            //            no existe pallet ningun pallet para el pedido
            $inPal = "insert into pallets set 
                pal_status = 0,
                pal_desc = 1,
                pal_pedido = '{$pedido}',
                pal_usr = '{$user}',
                pat_ts = now()
            ";
            $db->query($inPal);
            $response["pallet"] = 1;
        }

        $sqp = "select distinct pal_desc, pal_status from pallets where pal_pedido in ({$pedlist}) order by pal_desc desc limit 1";
        $rp2 = $db->query($sqp);
        $last = $rp2->fetch_assoc(); //ultimo pallet creado
        $sqp = "select distinct pal_desc, pal_status from pallets where pal_pedido in ({$pedlist}) and pal_status = 0 order by pal_desc desc limit 1";
        $rp3 = $db->query($sqp);
        $lastOpen = $rp3->fetch_assoc(); //ultimo pallet abierto

        //si el ultimo pallet esta abierto obtener multireferencia
        if ($lastOpen['pal_status'] == '0') {
            $resultado = verificar_estado_pallet($db, $pedlist, $lastOpen['pal_desc']);
            $response['datos_adicionales'] = $resultado;
        }

        //        echo (int)$lastOpen['pal_desc']." < ".(int)$last['pal_desc'];
        if ((int) $lastOpen['pal_desc'] == 0) {

            $sqpz = "select distinct pal_desc, pal_status from pallets where pal_pedido in ({$pedlist}) and pal_auto = 1 order by pal_desc desc limit 1";
            $rp6 = $db->query($sqpz);
            if ($rp6->num_rows == 0) {
                $response["pallet"] = "" . (int) $last['pal_desc'] + 1;
                $inPal = "insert into pallets set 
                    pal_status = 0,
                    pal_desc = {$response["pallet"]},
                    pal_pedido = '{$pedido}',
                    pal_usr = '{$user}',
                    pat_ts = now()
                    ";
                $db->query($inPal);
            } else {
                $lastAuto = $rp6->fetch_assoc(); //ultimo pallet creado
                $response["pallet"] = "" . (int) $lastAuto['pal_desc'];
                $sqx = "update pallets set 
                    pal_auto = 0,
                    pal_status = 0
                    where 
                    pal_pedido = '{$pedido}' and pal_desc = {$lastAuto['pal_desc']}
                ";
                $db->query($sqx);
            }
        } else if ((int) $lastOpen['pal_desc'] < (int) $last['pal_desc']) {
            $response["pallet"] = "" . (int) $lastOpen['pal_desc'];
        } else if ((int) $lastOpen['pal_desc'] == (int) $last['pal_desc']) {
            $response["pallet"] = "" . (int) $last['pal_desc'];
        }
        $db->commit();

        $response["error"] = FALSE;
        $response['mensaje'] = "Tiene " . count($response['materiales']) . " pedidos asignados.";
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "Sin pedidos asignados.";
    }
    echo json_encode($response);
}


function verificar_estado_pallet($db, $pedidos, $paldesc)
{
    $sql = "SELECT pallets.pal_desc, pallet_mat.etnum as multireferencia 
            FROM pallets 
            INNER JOIN pallet_mat ON pallet_mat.ap_pedido=pallets.pal_pedido AND pallet_mat.ap_pall=pallets.pal_desc 
            WHERE pal_pedido in ({$pedidos}) AND pal_desc='$paldesc'";

    return $db->query($sql)->fetch_assoc();
}
