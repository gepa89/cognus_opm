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
if (isset($_POST['dFecCre']) && isset($_POST['hFecCre'])) {
    if (($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')) {
        if (strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
            $dtd1 = " and date(fecinve) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 = " and date(fecinve) = '{$dt1}' ";
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
//var_dump($_POST["selCto"]);
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
//var_dump($_POST["selSit"]);
if (is_array($_POST["selSit"]) && count($_POST["selSit"]) >= 1) {
    $situ = " and a.situinve IN ( ";
    $situb = " and b.situinve IN ( ";
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
//var_dum$flp($fl);
//echo $dsAr;
$cabeceras = $detalles = array();
if ($fl == 1) {
    /*$sq = "SELECT distinct 
            a.send,
            a.pedexrack,
            a.pedexcie,
            a.pedexhorcie,
            a.pedexentre,
            a.almrefer,
            pedclase,
            clinom,
            pedexfec,
            pedexfec,
            pedexhor,
            codenv,
            b.pedpos,
            b.artcodi,
            b.artrefer,
            b.artdesc,
            b.unimed,
            b.canpedi,
            b.canpendi,
            b.canprepa,
            b.almcod,
            b.cencod,
            b.expst,
            b.fechcie,
            b.horcie,
            b.usuario,
            b.movref,
            siturefe, (select GROUP_CONCAT(tercod) from assig_ped where pedido = a.pedexentre and pedcajas is NULL GROUP BY pedido) as tercod, g.zoncodpre,
            e.ubirefer
            FROM 
                pedexcab a 
            inner join pedexdet b on a.pedexentre = b.pedexentre 
            left join arti c on b.artrefer = c.artrefer 
            left join ped_multiref d on b.pedexentre = d.pedido
            left join clientes f on f.clirefer = a.clirefer
            left join mref_exp e on d.multiref = e.mref and e.pedido = a.pedexentre
            left join artubiref g1 on b.artrefer = g1.artrefer
            left JOIN ubimapa g on g1.ubirefer = g.ubirefer and g.ubitipo <> 'RE'
            
            where 1=1  {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codenv} {$codalma}
            GROUP BY a.pedexentre,b.pedpos";*/


    $sq = "SELECT            
            a.pedinve,
            a.situinve,
            a.clasdoc,
            a.fecinve,
            a.horinve,
            a.userinve,
            a.control1,
            a.fecontrol1,
            a.horcontrol1,
	    a.dife1,
            a.codalma,
            a.control2,
            a.fecontrol2,
            a.horcontrol2,
	    a.dife2,
            a.control3,
            a.fecontrol3,
            a.horcontrol3,
            a.dife3,
            b.posdoc,
            b.artrefer,
            b.artdesc,
            b.canubi1,
            b.canfisi1,
            b.diferencia1,
	    b.fecontrol1,
            b.canubi2,
            b.canfisi2,
            b.diferencia2,
	    b.fecontrol2,
            b.canubi3,
            b.canfisi3,
            b.usuario1,
            b.usuario2,
            b.usuario3,
            b.diferencia3,
            b.fecontrol3,
            b.ubirefer,
            c.tercod as terminal
            FROM
            pedinvecab a
            inner join pedinvedet b on a.pedinve = b.pedinve
            left join assig_ped c on c.pedido=a.pedinve
        where TRUE {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codenv} {$codalma}
        GROUP BY
            a.pedinve,
            b.posdoc";

    $rs = $db->query($sq);

    $materiales = array();
    $zonas_pedidos = array();
    while ($row = $rs->fetch_assoc()) {
        if ($materiales[$row['pedinve']] == '') {
            $materiales[$row['pedinve']] = "'" . $row['artrefer'] . "'";
        } else {
            $materiales[$row['pedinve']] .= ",'" . $row['artrefer'] . "'";
        }
    }
    $zonas_ocupadas = array();
    $zonas_ocupadas_terminal = array();
    foreach ($materiales as $ped => $arr) {
        $sqzon = "select
                        b.zoncodpre,
                        a.artrefer,
                        sum(a.canti) as cantidad 
                    from
                        stockubi a
                    inner join ubimapa b on
                        a.ubirefer = b.ubirefer and b.ubitipo='PI'
                        and a.cod_alma = b.cod_alma
                    where
                        a.artrefer in ({$arr})
                    GROUP BY
                        b.zoncodpre";
        $rzon = $db->query($sqzon);
        while ($azon = $rzon->fetch_assoc()) {
            $zonas[$ped][] = $azon['zoncodpre'];
            $zonas_pedidos[$ped][] = array(
                "zona" => $azon['zoncodpre'],
                "articulo" => $azon['artrefer'],
                "cantidad" => $azon['cantidad']
            );
        }
        $sql = "select zona,tercod from assig_ped where pedido = '$ped' and pedcajas is null";
        $query = $db->query($sql);

        while ($fila = $query->fetch_assoc()) {
            $zonas_ocupadas[$ped][] = $fila["zona"];
            $zonas_ocupadas_terminal[$ped] = $fila["tercod"];
        }
    }

    /*print_r($materiales);
    print_r($zonas);
    print_r($zonas_pedidos);*/


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

        $cabeceras[$row['pedinve']]['pedinve'] = '' . $row['pedinve'];
        $cabeceras[$row['pedinve']]['situinve'] = '' . $row['situinve'];
        $cabeceras[$row['pedinve']]['clasdoc'] = '' . $row['clasdoc'];
        $cabeceras[$row['pedinve']]['fecinve'] = '' . $row['fecinve'];
        $cabeceras[$row['pedinve']]['userinve'] = '' . $row['userinve'];
        $cabeceras[$row['pedinve']]['control1'] = '' . $row['control1'];
        $cabeceras[$row['pedinve']]['fecontrol1'] = '' . $row['fecontrol1'];
        $cabeceras[$row['pedinve']]['dife1'] = '' . $row['dife1'];
        $cabeceras[$row['pedinve']]['control2'] = '' . $row['control2'];
        $cabeceras[$row['pedinve']]['fecontrol2'] = '' . $row['fecontrol2'];
        $cabeceras[$row['pedinve']]['dife2'] = '' . $row['dife2'];
        $cabeceras[$row['pedinve']]['control3'] = '' . $row['control3'];
        $cabeceras[$row['pedinve']]['fecontrol3'] = '' . $row['fecontrol3'];
        $cabeceras[$row['pedinve']]['dife3'] = '' . $row['dife3'];
        $cabeceras[$row['pedinve']]['terminal'] = '' . $row['terminal'];
        //  $almaec = $row['almrefer'];
        $cabeceras[$row['pedinve']]['codalma'] = '' . $row['codalma'];
        $row['send'] = "";
        $cabeceras[$row['pedinve']]['pedsendi'] = '';
        //        $cabeceras[$row['pedexentre']]['pedaction'] = '<a href="javascript:void(0);" onclick="asReception('."'".$row['pedexentre']."'".')" >Asignar</a>';
        if (!is_array(@$detalles[$row['pedinve']])) {
            $detalles[$row['pedinve']] = array();
        }
        if ($row['situinve'] == 'PD' && $row['terminal'] == "") {
            $cabeceras[$row['pedinve']]['pedaction'] = sprintf("<a class=\"btn btn-primary btn-fnt-size\" href=\"javascript:void(0);\" onclick=\"asReception('%s','%s','%s','%s')\">Asignar</a><br/>", $row['pedinve'], "todos", $row['codalma'], "INVE");
            $cabeceras[$row['pedinve']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="nullDoc(' . "'" . $row['pedinve'] . "'" . ",'Ex'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedinve']]['pedactio'] = '';
            $cabeceras[$row['pedinve']]['pedaction'] = $row['terminal'] ;
        }


        //HASTA ACA ES LO QUE ESTABA//

        $auxDat = array();
        $auxDat["pedinve"] = $row["pedinve"];
        $auxDat["posdoc"] = $row["posdoc"];
        $auxDat["artrefer"] = $row["artrefer"];
        $auxDat["artdesc"] = htmlspecialchars(utf8_decode($row["artdesc"]));
        $auxDat["canubi1"] = formatear_numero($row["canubi1"]);
        $auxDat["canfisi1"] = formatear_numero($row["canfisi1"]);
        $auxDat["diferencia1"] = formatear_numero($row["diferencia1"]);
        $auxDat["canubi2"] = formatear_numero($row["canubi2"]);
        $auxDat["canfisi2"] = formatear_numero($row["canfisi2"]);
        $auxDat["diferencia2"] = formatear_numero($row["diferencia2"]);
        $auxDat["canubi3"] = formatear_numero($row["canubi3"]);
        $auxDat["canfisi3"] = formatear_numero($row["canfisi3"]);
        $auxDat["diferencia3"] = formatear_numero($row["diferencia3"]);
        $auxDat["fecontrol1"] = $row["fecontrol1"];
        $auxDat["usuario1"] = $row["usuario1"];
        $auxDat["ubirefer"] = $row["ubirefer"];
        $auxDat["fecontrol1"] = $row["fecontrol1"];
        $auxDat["fecontrol2"] = $row["fecontrol2"];
        $auxDat["fecontrol3"] = $row["fecontrol3"];

        $auxDat["usuario2"] = $row["usuario2"];
        $auxDat["usuario3"] = $row["usuario3"];

        array_push($detalles[$row['pedinve']], $auxDat);
    }
}

//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();
