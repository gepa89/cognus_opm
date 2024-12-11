<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);



$arti = $_POST['arti']; //ss
$cli = $_POST['cli'];
$prov = $_POST['prov'];
$dFecCre = $_POST['dFecCre'];
$hFecCre = $_POST['hFecCre'];
$oper = $_POST['oper'];
$clamov = $_POST['clamov'];
$pedclase  = $_POST['pedclase'];
$codalma  = $_POST['codalma'];

/*
$dFecCre = "2022-11-01";
$hFecCre = "2022-11-04";
*/
//+

//var_dump($alma);
//almacen
//cliente
//fecierre
//horcierre
//cladoc
//nrodoc
//canprep
//posi
//userprep
//movref

$dFecCreCond = $operCond = $clamovCond = '';
if ($arti != "") {
    $artiCond = " and fulldata.arti like '%{$arti}%'";
}
if ($cli != "") {
    $cliCond = " and fulldata.cliente like '%{$cli}%'";
}
if ($prov != "") {
    $provCond = " and fulldata.coprov like '%{$prov}%'";
}
$dsAr = '';
if (count($oper) > 0 && is_array($oper)) {
    //    echo "entro";
    $dsAr = " and fulldata.usuario IN ( ";
    foreach ($oper as $k => $v) {
        if (is_numeric($v)) {
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        } else {
            $arti = $v;
        }
        if ($k == 0) {
            $dsAr .= "'" . trim($arti) . "'";
        } else {
            $dsAr .= ", '" . trim($arti) . "'";
        }
    }
    $dsAr .= " ) ";

    $operCond = $dsAr;
    $fl = 1;
} else {
    $dsAr = "";

    $operCond = $dsAr;
}
if (is_array($_POST["codalma"]) && count($_POST["codalma"]) >= 1) {
    $codalma = " and a.almrefer IN ( ";
    foreach ($_POST["codalma"] as $k => $v) {
        if ($k == 0) {
            $codalma .= "'" . trim($v) . "'";
        } else {
            $codalma .= ", '" . trim($v) . "'";
        }
    }
    $codalma .= ")";
    $fl = 1;
} else {
    $codalma = "";
}
//echo $almaCond;
//IF($clamov != ""){
//   $clamovCond = " and fulldata.movref like '%{$clamov}%'";
//}

$dsAr = '';

if (count($clamov) > 0 && is_array($clamov)) {
    $dsAr = " and fulldata.clamov IN ( ";
    foreach ($clamov as $k => $v) {
        if (is_numeric($v)) {
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        } else {
            $arti = $v;
        }
        if ($k == 0) {
            $dsAr .= "'" . trim($arti) . "'";
        } else {
            $dsAr .= ", '" . trim($arti) . "'";
        }
    }
    $dsAr .= " ) ";
    $clamovCond = $dsAr;
    $fl = 1;
} else {
    $dsAr = "";
    $clamovCond = $dsAr;
}

$dsAr = '';
if (count($pedclase) > 0 && is_array($pedclase)) {
    $dsAr = " and fulldata.cladoc IN ( ";
    foreach ($pedclase as $k => $v) {
        if (is_numeric($v)) {
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        } else {
            $arti = $v;
        }
        if ($k == 0) {
            $dsAr .= "'" . trim($arti) . "'";
        } else {
            $dsAr .= ", '" . trim($arti) . "'";
        }
    }
    $dsAr .= " ) ";
    $claseCond = $dsAr;
} else {
    $dsAr = "";
    $claseCond = $dsAr;
}

if (isset($_POST['dFecCre']) && isset($_POST['hFecCre'])) {
    if (($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')) {
        if (strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
            $dtd1 =  " and date(fulldata.fecierre) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 =  " and date(fulldata.fecierre) = '{$dt1}' ";
        }
        $fl = 1;
    }
    $dFecCreCond = $dtd1;
}



$sq = "SELECT
*
from
(
SELECT
    *
FROM
    (
    SELECT
        pedexcab.pedexentre as pedido,
        pedexdet.usuario as usuario,
        assig_ped.fecasig as fecasig,
        assig_ped.horasig as horasig,
        pedexdet.fechcie as fecierre,
        pedexdet.horcie as horcierre,
        artmov.descsen as descri,
        pedexcab.pedexfec as fecrea,
	pedexcab.pedexhor as horcrea,
        pedexcab.fecllegada as fechallegada,
	pedexcab.horllegada as horallegada,
        pedexcab.almrefer as codalma,
        pedexcab.movref as clamov
    FROM
        pedexcab
    INNER JOIN pedexdet on
        pedexcab.pedexentre = pedexdet.pedexentre
    INNER JOIN artmov on
        pedexcab.movref = artmov.movref
    LEFT JOIN assig_ped on
        pedexcab.pedexentre = assig_ped.pedido
    WHERE
        pedexcab.siturefe IN ('CE', 'PP')
        and pedexdet.canprepa <> '0'
    GROUP BY
        pedexcab.pedexentre
    ORDER BY
        pedexcab.pedexhorcie DESC) as d1
UNION ALL
SELECT
    *
FROM
    (
    select
        pedrecab.pedrefer as pedido,
        pallet_mat.ap_usr as usuario,
        assig_ped.fecasig as fecasig,
        assig_ped.horasig as horasig,
        pedrecab.pedrecie as fecierre,
        pedrecab.pedrehorcie as horcierre,
        artmov.descsen as descri,
        pedrecab.pedrefec as fecrea,
	pedrecab.pedrehor as horcrea,
        pedrecab.pedrefeclle as fechallegada,
	pedrecab.pedrehorlle as horallegada,
        pedrecab.almrefer as codalma,
        pedrecab.movref as clamov
    FROM
        pedrecab
    INNER JOIN pedredet on
        pedrecab.pedrefer = pedredet.pedrefer
    INNER JOIN arti on
        pedredet.artrefer = arti.artrefer
    INNER JOIN artmov on
        pedrecab.movref = artmov.movref
    LEFT JOIN pallet_mat on
        pedrecab.pedrefer = pallet_mat.ap_pedido
    LEFT JOIN assig_ped on
        pedrecab.pedrefer = assig_ped.pedido
    WHERE
        pedrecab.pedresitu in ('CE', 'UB', 'PP')
            and pedredet.canprepa <> '0'
        GROUP BY
            pedrecab.pedrefer
        ORDER BY
            pedrecab.pedrehorcie DESC) as d2
UNION ALL
SELECT
    *
FROM
    (
    select
        pedubicab.pedubicod as pedido,
        pedubidet.usuario as usuario,
        assig_ped.fecasig as fecasig,
        assig_ped.horasig as horasig,
        pedubidet.fecha as fecierre,
        pedubidet.hora as horcierre,
        artmov.descsen as descri,
        pedubicab.fecubi as fecrea,
	    pedubicab.horubi as horcrea,
        assig_ped.fecasig as fechallegada,
        assig_ped.horasig as horallegada,
        pedubicab.cod_alma as codalma,
        pedubicab.pedclase as clamov
    from
        pedubicab
    INNER JOIN pedubidet on
        pedubicab.pedubicod = pedubidet.pedubicod
    INNER JOIN artmov on
        pedubicab.pedclase = artmov.pedclase
    LEFT JOIN assig_ped on
        pedubicab.pedubicod = assig_ped.pedido
    WHERE
        pedubidet.expst = '1'
    GROUP BY
        pedubicab.pedubicod
    ORDER BY
        pedubidet.hora DESC) as d3

) as fulldata
where 
1=1 " . $dFecCreCond . $operCond . $clamovCond . $claseCond . $codalma;
    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while ($row = $rs->fetch_assoc()) {
    $cabeceras[$cc]['pedido'] = $row['pedido'];
    $cabeceras[$cc]['arti'] = $row['arti'];
    $cabeceras[$cc]['artides'] = $row['artides'];
    $cabeceras[$cc]['canpedida'] = $row['canpedida'];
    $cabeceras[$cc]['canprepara'] = $row['canprepara'];
    $cabeceras[$cc]['usuario'] = $row['usuario'];
    $cabeceras[$cc]['fecasig'] = $row['fecasig'];
    $cabeceras[$cc]['horasig'] = $row['horasig'];
    $cabeceras[$cc]['fecierre'] = $row['fecierre'];
    $cabeceras[$cc]['fecrea'] = $row['fecrea'];
    $cabeceras[$cc]['horcrea'] = $row['horcrea'];
    $cabeceras[$cc]['fechallegada'] = $row['fechallegada'];
    $cabeceras[$cc]['horallegada'] = $row['horallegada'];
    $cabeceras[$cc]['horcierre'] = $row['horcierre'];
    $cabeceras[$cc]['descri'] = $row['descri'];
    $cabeceras[$cc]['codalma'] = $row['codalma'];
    $hora_inicio = new DateTime($row['horasig']); // Replace with your start time
    $hora_fin = new DateTime($row['horcierre']);   // Replace with your end time
    $hora_creasap = new DateTime($row['horcrea']); // Replace with your start time
    $hora_llega = new DateTime($row['horallegada']);   // Replace with your end time

    // Calculate the difference
    $interval = $hora_inicio->diff($hora_fin);
    $interval2 = $hora_creasap->diff($hora_llega);

    // Get the difference in minutes and seconds
    $minutos = $interval->format('%i');
    $segundos = $interval->format('%s');
    $segundos = strlen($segundos) > 1 ? $segundos : "0$segundos";
    $cabeceras[$cc]['tiempo_respuesta'] = $minutos . ":" . $segundos;
    
    $minutos = $interval2->format('%i');
    $segundos = $interval2->format('%s');
    $segundos = strlen($segundos) > 1 ? $segundos : "0$segundos";
    $cabeceras[$cc]['tiempo_llegada'] = $minutos . ":" . $segundos;
    if (!$row['horasig']) {
        $cabeceras[$cc]['tiempo_respuesta'] = "--:--";
        
    }
    if (!$row['horallegada']) {
        
        $cabeceras[$cc]['tiempo_llegada'] = "--:--";
    }
    $cc++;
}
#header('Content-type: application/json; charset=utf-8');
echo json_encode(array('cab' => $cabeceras));

exit();
