<?php

require('../conect.php');
require("../utils/conversores.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
if (isset($_POST['dFecCre']) && isset($_POST['hFecCre'])) {
    if (($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')) {
        if (strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
            $dtd1 = " and date(fecubi) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 = " and date(fecubi) = '{$dt1}' ";
        }
        $fl = 1;
    }
}

if (is_array($_POST["selCod"]) && count($_POST["selCod"]) >= 1) {
    $codenv = " and a.codenv IN ( ";
    foreach ($_POST["selCod"] as $k => $v) {
        if ($k == 0) {
            $codenv .= "'" . trim($v) . "'";
        } else {
            $codenv .= ", '" . trim($v) . "'";
        }
    }
    $codenv .= ")";
    $fl = 1;
} else {
    $codenv = "";
}
//var_dump($_POST["selCto"]);
if (is_array($_POST["selCto"]) && count($_POST["selCto"]) >= 1) {
    $clase = " and a.pedclase IN ( ";
    foreach ($_POST["selCto"] as $k => $v) {
        if ($k == 0) {
            $clase .= "'" . trim($v) . "'";
        } else {
            $clase .= ", '" . trim($v) . "'";
        }
    }
    $clase .= ")";
    $fl = 1;
} else {
    $clase = "";
}
if (is_array($_POST["codalma"]) && count($_POST["codalma"]) >= 1) {
    $codalma = " and a.cod_alma IN ( ";
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
//var_dump($_POST["selSit"]);
if (is_array($_POST["selSit"]) && count($_POST["selSit"]) >= 1) {
    $situ = " and a.situped IN ( ";
    foreach ($_POST["selSit"] as $k => $v) {
        if ($k == 0) {
            $situ .= "'" . trim($v) . "'";
        } else {
            $situ .= ", '" . trim($v) . "'";
        }
    }
    $situ .= ")";
    $fl = 1;
} else {
    $situ = '';
}
if (isset($_POST["desPed"]) && $_POST["desPed"] != '') {
    $pd = str_pad(trim($_POST['desPed']), 10, '0', STR_PAD_LEFT);
    $desPed = " and a.pedubicod like '%{$_POST['desPed']}' ";
    $fl = 1;
} else {
    $desPed = "";
}

$arArt = explode(',', $_POST["desArt"]);
$arArt = array_filter($arArt);
//var_dump($arArt);
if (count($arArt) >= 1 && is_array($arArt)) {
    $dsAr = " and b.artrefer IN ( ";
    foreach ($arArt as $k => $v) {
        //        if(is_numeric($v)){
        //            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        //        }else{
        $arti = strtoupper($v);
        //        }
        if ($k == 0) {
            $dsAr .= "'" . trim($arti) . "'";
        } else {
            $dsAr .= ", '" . trim($arti) . "'";
        }
    }
    $dsAr .= " ) ";
    $fl = 1;
} else {
    $dsAr = "";
}
//var_dum$flp($fl);
//echo $dsAr;
$cabeceras = $detalles = array();
if ($fl == 1) {
    $sq = "SELECT DISTINCT
	a.pedubicod,
	a.pedrefer,
	a.fecubi,
	a.horubi,
	a.pedclase,
	a.situped,
	a.cod_alma,
	b.posubi,
	b.artrefer,
	b.artdesc,
	b.cantiu,
	b.canubi,
	b.canupen,
	b.etnum,
	b.etnum,
	b.muelle,
	b.usuario,
	b.fecha,
    assig_ped.tercod,
    a.pedsent 
FROM
	pedubicab a
	LEFT JOIN pedubidet b ON a.pedubicod = b.pedubicod 
    LEFT JOIN assig_ped ON a.pedubicod = assig_ped.pedido     
 where a.situped <> 'AN' {$dsAr} {$desPed} {$dtd1} {$clase} {$situ} {$codenv} {$codalma}";
    $rs = $db->query($sq);

    $materiales = array();
    /*print_r("<pre>");
    print_r($rs->fetch_all());
    print_r("</pre>");
    exit;*/
    while ($row = $rs->fetch_assoc()) {
        if ($materiales[$row['pedrefer']] == '') {
            $materiales[$row['pedrefer']] = "'" . $row['artrefer'] . "'";
        } else {
            $materiales[$row['pedrefer']] .= ",'" . $row['artrefer'] . "'";
        }
    }

    //    echo $materiales;
    foreach ($materiales as $ped => $arr) {
        $sqzon = "select  b.zoncodpre from stockubi a 
        inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
        where a.artrefer in ({$arr}) GROUP BY  b.zoncodpre";
        //    echo $sqzon;
        $rzon = $db->query($sqzon);
        while ($azon = $rzon->fetch_assoc()) {
            $zonas[$ped][] = $azon['zoncodpre'];
        }
    }



    //    var_dump($zonas);
    $rs = $db->query($sq);
    $lk = '';


    while ($row = $rs->fetch_assoc()) {
        //        var_dump($row);
        //        <th></th>
        //        <th>Pedido</th>
        //        <th>Fecha Creacion</th>
        //        <th>Hora Creacion</th>
        //        <th>fecha recepcion</th>
        //        <th>Proveedor</th>
        //        <th>Clase de Documento</th>
        //        <th>Situacion del Pedido</th>
        //        <th>Planificar</th>

        $cabeceras[$row['pedubicod']]['pedubicod'] = '' . $row['pedubicod'];
        $cabeceras[$row['pedubicod']]['pedrefer'] = '' . $row['pedrefer'];
        $cabeceras[$row['pedubicod']]['fecubi'] = '' . $row['fecubi'];
        $cabeceras[$row['pedubicod']]['horubi'] = '' . $row['horubi'];
        $cabeceras[$row['pedubicod']]['pedclase'] = '' . $row['pedclase'];
        $cabeceras[$row['pedubicod']]['situped'] = '' . $row['situped'];
        $cabeceras[$row['pedubicod']]['cod_alma'] = '' . $row['cod_alma'];
        $cabeceras[$row['pedubicod']]['terminal'] = $row['tercod'] ? $row['tercod'] : null;
        //        $cabeceras[$row['pedexentre']]['pedaction'] = '<a href="javascript:void(0);" onclick="asReception('."'".$row['pedexentre']."'".')" >Asignar</a>';
        if ($row['situped'] == 'CE' && $row['pedsent'] == 0 && $row['pedclase'] == 'UB') {
            $pedido = $row['pedrefer'];
            $pedubicod = $row['pedubicod'];
            $cabeceras[$row['pedubicod']]['pedsendi'] = "<a class=\"btn btn-success btn-fnt-size\" href=\"javascript:void(0);\"onclick=\"sendExpedition('{$pedido}','{$pedubicod}')\" >Enviar</a>";
        } else if ($row['situped'] == 'PD' && $row['pedsent'] == 1 && $row['pedclase'] == 'UB') {
            $cabeceras[$row['pedubicod']]['pedsendi'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="sendExpedition(' . "'" . $row['pedubicod'] . "'" . ')" >Anular</a>';
        } else if ($row['pedsent'] == 1) {
            $cabeceras[$row['pedubicod']]['pedsendi'] = "Enviado";
        } else {
            $cabeceras[$row['pedubicod']]['pedsendi'] = "";
        }
        if (!is_array($detalles[$row['pedubicod']])) {
            $detalles[$row['pedubicod']] = array();
        }
        if ($row['situped'] == 'PD' && $row['tercod'] == '') {
            $cabeceras[$row['pedubicod']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="nullDoc(' . "'" . $row['pedubicod'] . "'" . ",'Ex'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedubicod']]['pedactio'] = '';
        }

        //HASTA ACA ES LO QUE ESTABA//

        $auxDat = array();
        $auxDat["pedubicod"] = $row["pedubicod"];
        $auxDat["posubi"] = $row["posubi"];
        $auxDat["artrefer"] = $row["artrefer"];
        $auxDat["artdesc"] = htmlspecialchars(utf8_decode($row["artdesc"]));
        $auxDat["cantiu"] = formatear_numero($row["cantiu"]);
        $auxDat["canubi"] = formatear_numero($row["canubi"]);
        $auxDat["canupen"] = formatear_numero($row["canupen"]);
        $auxDat["etnum"] = $row["etnum"];
        $auxDat["muelle"] = $row["muelle"];
        $auxDat["usuario"] = $row["usuario"];
        $auxDat["fecha"] = $row["fecha"];
        array_push($detalles[$row['pedubicod']], $auxDat);
    }
    foreach ($cabeceras as $ped => $dat) {
        $codalma = $dat['cod_alma'];
        $cabeceras[$ped]['pedaction'] = "";
        if ($dat['terminal'] == null) {
            $lk = "<a class=\"btn btn-primary btn-fnt-size\" href=\"javascript:void(0);\" onclick=\"asReceptionRe('$ped','REPO','{$codalma}')\" >Asignar Terminal</a><br/>";
            $cabeceras[$ped]['pedaction'] = $lk;
        } else {
            $cabeceras[$ped]['pedaction'] = $dat['terminal'];
        }
    }
}



//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();
