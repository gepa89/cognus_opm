<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
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
            $dtd1 = " and date(a.pedrefec) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 = " and date(a.pedrefec) = '{$dt1}' ";
        }
        $fl = 1;
    }
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
//var_dump($_POST["selSit"]);
if (is_array($_POST["selSit"]) && count($_POST["selSit"]) >= 1) {
    $situ = " and a.pedresitu IN ( ";
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
    $desPed = " and a.pedrefer like '%{$_POST['desPed']}' ";
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

$cabeceras = $detalles = array();
if ($fl == 1) {
    /*$sq = "SELECT  a.*, b.*, COALESCE(h.ap_cant,0)as ap_cant, c.artdesc, d.tercod, l.mic, a.almrefer as almcod, IFNULL(e.ap_usr,'') as ap_usr, IFNULL(e.MaxDate,'') as ap_ts, IFNULL(f.muelle,'') as muelle, IFNULL(h.etnum,'') as etnum, g.pedclase
        FROM pedrecab a 
        inner join pedredet b on a.pedrefer = b.pedrefer 
        left join arti c on b.artrefer = c.artrefer 
        left join assig_ped d on a.pedrefer = d.pedido
        left join (select ap_pedido, ap_mat, ap_usr, MAX(ap_ts) MaxDate from pallet_mat group by ap_pedido, ap_mat) e on a.pedrefer = e.ap_pedido and b.artrefer = e.ap_mat
        left join pedmuelle f on a.pedrefer = f.pedrefer
        left join clasdoc g on g.pedclase = a.pedclase
        left join pallet_mat h on h.ap_pedido = a.pedrefer and b.artrefer = h.ap_mat
        left JOIN pedidosincab l on a.pedrefer=l.docompra
        where 1=1  {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codalma}";*/
    //echo $sq;
    $sq = "SELECT
        b.pedpos,
        a.pedrefer,
        a.almrefer as almcod,
        a.pedrefec,
        a.pedrehor,
        a.pedrefeclle,
        a.nomprove as codprove,
        a.obse1 as obse,
        a.pedclase,
        a.pedresitu,
        b.artrefer,
        d.ap_termi as tercod,
        e.artdesc,
        b.canexp,
        b.canpedi,
        b.canpendi,
        d.etnum,
        d.ap_usr,
        d.ap_ts,
        (SELECT ubirefer
        from movimientos
        WHERE clamov='UBMUS'
        AND pedido=a.pedrefer and etnum=d.etnum limit 1) as muelle,	
        Coalesce((SELECT SUM(ap_cant) FROM pallet_mat 
        WHERE ap_mat=b.artrefer 
        and ap_pedido=a.pedrefer 
        and etnum=d.etnum and pedpos = b.pedpos GROUP BY ap_mat,etnum ),0) as ap_cant
        FROM pedrecab a
        INNER JOIN pedredet b on a.pedrefer=b.pedrefer
        INNER JOIN arti e on b.artrefer=e.artrefer
        left JOIN movimientos c on a.pedrefer=c.pedido
        left JOIN pallet_mat d on b.pedrefer=d.ap_pedido and b.pedpos=d.pedpos and b.artrefer=d.ap_mat 
        WHERE 1=1 {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codalma}
        group by pedpos,pedrefer,etnum";
    $rs = $db->query($sq);
    $array_cabeceras = array();
    while ($row = $rs->fetch_assoc()) {
        //        <th></th>
//        <th>Pedido</th>
//        <th>Fecha Creacion</th>
//        <th>Hora Creacion</th>
//        <th>fecha recepcion</th>
//        <th>Proveedor</th>
//        <th>Clase de Documento</th>
//        <th>Situacion del Pedido</th>
//        <th>Planificar</th>
        $cabeceras[$row['pedrefer']]['pedrefer'] = $row['pedrefer'];
        $cabeceras[$row['pedrefer']]['codprove'] = $row['nomprove'];
        $cabeceras[$row['pedrefer']]['obse'] = $row['mic'];
        $cabeceras[$row['pedrefer']]['pedclase'] = $row['pedclase'];
        $cabeceras[$row['pedrefer']]['pedrefec'] = $row['pedrefec'];
        $cabeceras[$row['pedrefer']]['pedrefec'] = $row['pedrefec'];
        $cabeceras[$row['pedrefer']]['pedrehor'] = $row['pedrehor'];
        $cabeceras[$row['pedrefer']]['pedresitu'] = $row['pedresitu'];
        $cabeceras[$row['pedrefer']]['muelle'] = $row['muelle'];
        $cabeceras[$row['pedrefer']]['codalma'] = $row['almcod'];

        if (!in_array($row['pedresitu'], ['AN', 'CE'])) {
            $cabeceras[$row['pedrefer']]['pedaction'] = sprintf("<button class=\"btn btn-primary btn-fnt-size\"  onclick=\"asReception('%s','%s')\" >Asignar</button>", $row['pedrefer'], $row['almcod']);
        } else {
            $cabeceras[$row['pedrefer']]['pedaction'] = $row['tercod'];
        }
        if ($row['pedresitu'] == 'CE' && $row['pedsent'] == 0) {
            $cabeceras[$row['pedrefer']]['pedsendi'] = '';
        } else if ($row['pedresitu'] == 'CE' && $row['pedsent'] == 1) {
            $cabeceras[$row['pedrefer']]['pedsendi'] = '<a class="btn btn-success btn-fnt-size" href="javascript:void(0);" onclick="sendReception(' . "'" . $row['pedrefer'] . "'" . ')" >Enviado</a>';
        } else {
            $cabeceras[$row['pedrefer']]['pedsendi'] = '';
        }
        if (!is_array(@$detalles[$row['pedrefer']])) {
            $detalles[$row['pedrefer']] = array();
        }

        $cabeceras[$row['pedrefer']]['pedactio'] = '';
        if ($row['pedresitu'] == 'PD' && $row['tercod'] == '') {
            $cabeceras[$row['pedrefer']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);" onclick="nullDoc(' . "'" . $row['pedrefer'] . "'" . ",'Re'" . ')" >Anular</a>';
        }
        $cabeceras[$row['pedrefer']]['pend'] = '';
        $cantidad = 0;
        if ($row['pedresitu'] == 'PD') {
            if (!in_array($row['pedrefer'], $array_cabeceras)) {
                array_push($array_cabeceras, $row['pedrefer']);
                $almacen = $row['almcod'];
                $pedido = $row['pedrefer'];
                $sql = "SELECT
                            COUNT(*) AS cantidad
                        from
                            pedrecab
                        INNER JOIN pedredet on
                            pedrecab.pedrefer = pedredet.pedrefer
                        INNER JOIN arti on
                            pedredet.artrefer = arti.artrefer
                        LEFT JOIN artubiref on
                            pedredet.artrefer = artubiref.artrefer
                            and artubiref.cod_alma = '$almacen'
                            AND artubiref.ubitipo IN ('PI','PS')
                        WHERE
                            pedrecab.pedrefer = '$pedido'
                            AND artubiref.ubirefer is NULL";
                $query = $db->query($sql);
                
                $cantidad = $query->fetch_object()->cantidad;
            }
            if ($cantidad > 0) {
                $pedido = $row['pedrefer'];

                $cabeceras[$row['pedrefer']]['pend'] = "<a class=\"btn btn-success btn-fnt-size \"
                href=\"javascript:void(0);\" onclick=\"pend('$pedido','$almacen')\" > Ver $cantidad</a>";
            }
        }
        $row['canpedi'] = formatear_numero($row['canpedi']);
        $row['canprepa'] = formatear_numero($row['canprepa']);
        $row['canpendi'] = formatear_numero($row['canpendi']);
        $row['canexp'] = formatear_numero($row['canexp']);
        $row['ap_cant'] = formatear_numero($row['ap_cant']);

        array_push($detalles[$row['pedrefer']], $row);
    }
}


echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();