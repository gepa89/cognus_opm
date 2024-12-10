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



$sq = "SELECT *
FROM (
    SELECT
        pedexcab.pedexentre AS pedido,
        pedexdet.usuario AS usuario,
        assig_ped.fecasig AS fecasig,
        assig_ped.horasig AS horasig,
        pedexdet.fechcie AS fecierre,
        pedexdet.horcie AS horcierre,
        artmov.descsen AS descri,
        pedexcab.pedexfec AS fecrea,
        pedexcab.pedexhor AS horcrea,
        pedexcab.fecllegada AS fechallegada,
        pedexcab.horllegada AS horallegada,
        pedexcab.almrefer AS codalma,
        pedexcab.movref AS clamov 
    FROM
        pedexcab
        INNER JOIN pedexdet ON pedexcab.pedexentre = pedexdet.pedexentre
        INNER JOIN artmov ON pedexcab.movref = artmov.movref
        LEFT JOIN assig_ped ON pedexcab.pedexentre = assig_ped.pedido 
    WHERE
        pedexcab.siturefe IN ('CE', 'PP') 
        AND pedexdet.canprepa <> '0' 

    UNION ALL

    SELECT
        pedrecab.pedrefer AS pedido,
        pallet_mat.ap_usr AS usuario,
        assig_ped.fecasig AS fecasig,
        assig_ped.horasig AS horasig,
        pedrecab.pedrecie AS fecierre,
        pedrecab.pedrehorcie AS horcierre,
        artmov.descsen AS descri,
        pedrecab.pedrefec AS fecrea,
        pedrecab.pedrehor AS horcrea,
        pedrecab.pedrefeclle AS fechallegada,
        pedrecab.pedrehorlle AS horallegada,
        pedrecab.almrefer AS codalma,
        pedrecab.movref AS clamov 
    FROM
        pedrecab
        INNER JOIN pedredet ON pedrecab.pedrefer = pedredet.pedrefer
        INNER JOIN arti ON pedredet.artrefer = arti.artrefer
        INNER JOIN artmov ON pedrecab.movref = artmov.movref
        LEFT JOIN pallet_mat ON pedrecab.pedrefer = pallet_mat.ap_pedido
        LEFT JOIN assig_ped ON pedrecab.pedrefer = assig_ped.pedido 
    WHERE
        pedrecab.pedresitu IN ('CE', 'UB', 'PP') 
        AND pedredet.canprepa <> '0' 
) AS fulldata
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
