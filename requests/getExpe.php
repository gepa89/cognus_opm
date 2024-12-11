<?php

require('../conect.php');
require_once("../utils/conversores.php");

//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$codalma = $_GET['codalma'];
$limite = $_GET['length'] ? $_GET['length'] : 10;
$offset = $_GET['start'] ? $_GET['start'] : 0;

$articulo = htmlspecialchars(trim($_GET['articulo']));
if (isset($_POST['dFecCre']) && isset($_POST['hFecCre'])) {
    if (($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')) {
        if (strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
            $dtd1 = " and date(pedexfec) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 = " and date(pedexfec) = '{$dt1}' ";
        }
        $fl = 1;
    }
}

if (is_array($_POST["selCod"]) && count($_POST["selCod"]) >= 1) {
    $codenv = " and a.codenv IN ( ";
    $codenvb = " and b.codenv IN ( ";
    foreach ($_POST["selCod"] as $k => $v) {
        if ($k == 0) {
            $codenv .= "'" . trim($v) . "'";
            $codenvb .= "'" . trim($v) . "'";
        } else {
            $codenv .= ", '" . trim($v) . "'";
            $codenvb .= ", '" . trim($v) . "'";
        }
    }
    $codenv .= ")";
    $codenvb .= ")";
    $fl = 1;
} else {
    $codenv = "";
}
if (is_array($_POST["selCto"]) && count($_POST["selCto"]) >= 1) {
    $clase = " and a.pedclase IN ( ";
    $claseb = " and b.pedclase IN ( ";
    foreach ($_POST["selCto"] as $k => $v) {
        if ($k == 0) {
            $clase .= "'" . trim($v) . "'";
            $claseb .= "'" . trim($v) . "'";
        } else {
            $clase .= ", '" . trim($v) . "'";
            $claseb .= ", '" . trim($v) . "'";
        }
    }
    $clase .= ")";
    $claseb .= ")";
    $fl = 1;
} else {
    $clase = "";
}
if (is_array($_POST["codalma"]) && count($_POST["codalma"]) >= 1) {
    $codalma = " and a.almrefer IN ( ";
    $codalmab = " and b.almrefer IN ( ";
    foreach ($_POST["codalma"] as $k => $v) {
        if ($k == 0) {
            $codalma .= "'" . trim($v) . "'";
            $codalmab .= "'" . trim($v) . "'";
        } else {
            $codalma .= ", '" . trim($v) . "'";
            $codalmab .= ", '" . trim($v) . "'";
        }
    }
    $codalma .= ")";
    $codalmab .= ")";

    $fl = 1;
} else {
    $codalma = "";
    $codalmab = "";
}
if (is_array($_POST["selSit"]) && count($_POST["selSit"]) >= 1) {
    $situ = " and a.siturefe IN ( ";
    $situb = " and b.siturefe IN ( ";
    foreach ($_POST["selSit"] as $k => $v) {
        if ($k == 0) {
            $situ .= "'" . trim($v) . "'";
            $situb .= "'" . trim($v) . "'";
        } else {
            $situ .= ", '" . trim($v) . "'";
            $situb .= "'" . trim($v) . "'";
        }
    }
    $situ .= ")";
    $situb .= ")";
    $fl = 1;
} else {
    $situ = '';
}
if (isset($_POST["desPed"]) && $_POST["desPed"] != '') {
    $pd = str_pad(trim($_POST['desPed']), 10, '0', STR_PAD_LEFT);
    $desPed = " and a.pedexentre like '%{$_POST['desPed']}' ";
    $fl = 1;
} else {
    $desPed = "";
}
if (isset($_POST["desPrv"]) && $_POST["desPrv"] != '') {
    $prv = str_pad(trim($_POST['desPrv']), 10, '0', STR_PAD_LEFT);
    $desPrv = " and a.codprove like '%{$_POST['desPrv']}' ";
    $fl = 1;
} else {
    $desPrv = "";
}
$arArt = explode(',', $_POST["desArt"]);
$arArt = array_filter($arArt);
//var_dump($arArt);
if (count($arArt) >= 1 && is_array($arArt)) {
    $dsAr = " and b.artrefer IN ( ";
    $dsArb = " and j.artrefer IN ( ";
    foreach ($arArt as $k => $v) {
        //        if(is_numeric($v)){
        //            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        //        }else{
        $arti = strtoupper($v);
        //        }
        if ($k == 0) {
            $dsAr .= "'" . trim($arti) . "'";
            $dsArb .= "'" . trim($arti) . "'";
        } else {
            $dsAr .= ", '" . trim($arti) . "'";
            $dsArb .= ", '" . trim($arti) . "'";
        }
    }
    $dsAr .= " ) ";
    $dsArb .= " ) ";
    $fl = 1;
} else {
    $dsAr = "";
}

$sql = "SELECT
            distinct 
            a.send,
            a.pedexrack,
            a.pedexcie,
            a.pedexhorcie,
            a.pedexentre,
            a.almrefer,
            a.pedclase,
            a.factura,
            f.clinom,
            a.pedexfec,
            a.pedexfec,
            a.pedexhor,
            a.codenv,
            b.pedpos,
            b.artrefer,
            b.artdesc,
            b.unimed,
            b.canpedi,
            b.canpendi,
            b.canprepa,
            b.almcod,
            b.expst,
            b.fechcie,
            b.horcie,
            b.usuario,
            g.zoncodpre,
            e.ubirefer,
            a.siturefe,
            d.terminal as tercod,
            d.multiref,
            assig_ped.tercod as terminal
        FROM
            pedexcab a
        inner join pedexdet b on
            a.pedexentre = b.pedexentre
        left join assig_ped on assig_ped.pedido=a.pedexentre 
        left join arti c on
            b.artrefer = c.artrefer
        left join ped_multiref d on
            b.pedexentre = d.pedido
            and d.pedcajas = '0'
        left join clientes f on
            f.clirefer = a.clirefer
        left join mref_exp e on
            d.multiref = e.mref
            and e.pedido = a.pedexentre
        left join artubiref g1 on
            b.artrefer = g1.artrefer
        left JOIN ubimapa g on
            g1.ubirefer = g.ubirefer

        where TRUE {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codenv} {$codalma}
        GROUP BY
            a.pedexentre,
            b.pedpos";

try {
    $sql_contar = "SELECT COUNT(*) AS cantidad FROM ({$sql}) as e";
    $cantidad = $db->query($sql_contar)->fetch_assoc()['cantidad'];

    $sql = $sql . "LIMIT $limite offset $offset";
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($resultados); $i++) {
        $resultados[$i]['total'] = formatear_numero($resultados[$i]['total']);
    }
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();

$respuesta = array(
    'data' => $resultados,
    'recordsFiltered' => (int) $cantidad,
    'recordsTotal' => (int) $cantidad
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit();
