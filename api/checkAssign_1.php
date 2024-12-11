<?php
require('../conect.php');
require_once(__DIR__ . "/../hanaDB.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$user = $_POST['user'];
$terminal = strtoupper($_POST['terminal']);


if (isset($terminal)) {

    //    Obtengo todos los pedidos de recepcion asignados a la terminal
    $sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
    $rsz = $db->query($sqz);
    $rszz = $rsz->fetch_assoc();


    $response = array();
    $sq = "select DISTINCT
            a.pedido, 
            a.st,
            a.ex_st, 
            d.zoncodpre,
            (select count(artrefer) as tsku from pedexdet b where pedexentre = pedido) as tsku,
            (select pedclase as pclase from pedexcab c where pedexentre = pedido) as clase
            from assig_ped a 
            inner join pedexdet b on pedexentre = pedido
            inner join stockubi c on b.artrefer = c.artrefer
            inner join ubimapa d on c.ubirefer = d.ubirefer
            inner join termi e on a.tercod = e.tercod
            where 
            a.tercod = '{$terminal}' and 
            e.terzonpre = '{$rszz['terzonpre']}' and 
            st = 0 and 
            ex_st = 0 ";
    $rs = $db->query($sq);
    $pedlist = '';
    while ($ax = $rs->fetch_assoc()) {
        if ($pedlist != '') {
            $pedlist .= ",'" . $ax['pedido'] . "'";
        } else {
            $pedlist = "'" . $ax['pedido'] . "'";
        }
        $pedido = $ax['pedido'];
        $response['materiales'][] = $ax;
    }


    $sq = "SELECT a.artrefer, b.artean, b.artdesc, a.canpedi, a.canpendi, a.canprepa, b.artser , b.artser 
            FROM pedredet a 
            left join arti b on a.artrefer = b.artrefer  
            where pedrefer = '{$pedido}' ";
    //            echo $sq;
    $rs = $db->query($sq);
    $flg = 0;
    while ($ax = $rs->fetch_assoc()) {
        $sqhan = "SELECT DISTINCT EKPO.MATNR, MSEG.MBLNR, SER03.OBKNR, OBJK.SERNR, OBJK.EQUNR, EQUI.BAUJJ, EQUI.BAUMM
                    FROM SAPABAP1.MSEG, SAPABAP1.SER03, SAPABAP1.OBJK, SAPABAP1.EQUI, SAPABAP1.EKPO

                    WHERE MSEG.EBELN='{$pedido}'
                    and EKPO.MATNR='{$ax['artrefer']}'
                    and MSEG.SHKZG='S'
                    AND MSEG.MBLNR=SER03.MBLNR
                    AND SER03.SHKZG = 'S'
                    AND SER03.OBKNR=OBJK.OBKNR
                    AND OBJK.SERNR=EQUI.SERNR
                    AND EKPO.MATNR=EQUI.MATNR";
        //                echo $sqhan;
        $rst = odbc_exec($prd, $sqhan);
        $sernro = '';
        while ($rw = odbc_fetch_object($rst)) {
            $sernro = $rw->SERNR;
            $mes = $rw->BAUJJ;
            $anio = $rw->BAUMM;
            $inSer = "insert into serati set 
                    artrefer = '{$ax['artrefer']}',
                    artserie = '{$sernro}',
                    artanio = '{$anio}',
                    artmes = '{$mes}',
                        artped = '{$pedido}'
                ";

            $db->query($inSer);
        }

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
//        echo (int)$lastOpen['pal_desc']." < ".(int)$last['pal_desc'];
        if ((int) $lastOpen['pal_desc'] == 0) {
            $response["pallet"] = "" . (int) $last['pal_desc'] + 1;
            $inPal = "insert into pallets set 
                pal_status = 0,
                pal_desc = {$response["pallet"]},
                pal_pedido = '{$pedido}',
                pal_usr = '{$user}',
                pat_ts = now()
                ";
            $db->query($inPal);
        } else if ((int) $lastOpen['pal_desc'] < (int) $last['pal_desc']) {
            $response["pallet"] = "" . (int) $lastOpen['pal_desc'];

        } else if ((int) $lastOpen['pal_desc'] == (int) $last['pal_desc']) {
            $response["pallet"] = "" . (int) $last['pal_desc'];

        }
        $response["error"] = FALSE;
        $response['mensaje'] = "Tiene " . count($response['materiales']) . " pedidos asignados.";
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "Sin pedidos asignados.";
    }
    echo json_encode($response);
}