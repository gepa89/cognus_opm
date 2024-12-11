<?php

require('../conect.php');

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
            $dtd1 = " and date(pedexfec) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if (strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])) {
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dtd1 = " and date(pedexfec) = '{$dt1}' ";
        }
        $fl = 1;
    }
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

//var_dum$flp($fl);
//echo $dsAr;
$cabeceras = $detalles = array();
if ($fl == 1) {
    $sq = "SELECT distinct 
            a.pedexentre,
            a.almrefer,
            pedclase,
            clinom,
            b.pedpos,
            b.artrefer,
            c.artdesc,
            b.unimed,
            b.canpedi,
            b.canpendi,
            b.canprepa,
            b.fechcie,
            b.horcie,
            b.usuario,
            siturefe, (SELECT fecmov from movimientos where movimientos.artrefer = b.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=a.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) as fecingre,
                    (SELECT DATEDIFF(b.fechcie ,fecmov )*m.costodia*b.canprepa from movimientos where movimientos.artrefer = b.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=a.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) AS costoarticulo ,
                    (SELECT DATEDIFF(b.fechcie ,fecmov )from movimientos where movimientos.artrefer = b.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=a.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) AS diasalma,
            g.zoncodpre,
            g.ubirefer,
	    m.costodia
						
            FROM 
                pedexcab a 
            inner join pedexdet b on a.pedexentre = b.pedexentre 
            left join arti c on b.artrefer = c.artrefer 
            left join ped_multiref d on b.pedexentre = d.pedido
            left join clientes f on f.clirefer = a.clirefer
            left join mref_exp e on d.multiref = e.mref and e.pedido = a.pedexentre
            left join artubiref g1 on b.artrefer = g1.artrefer
            left JOIN ubimapa g on g1.ubirefer = g.ubirefer 
            left JOIN costoalma m on g.dimension=m.cod_pal 
            INNER JOIN movimientos l on g.ubirefer=l.ubirefer and g.cod_alma=l.cod_alma
            where 1=1  {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$codalma} 
						
	    AND a.siturefe='CE'
            GROUP BY b.pedexentre,b.pedpos          
            
 union ALL

SELECT DISTINCT
	b.pedexentre,
        b.almrefer,
	pedclase,
	clinom,
	j.poskit,
	j.artrefkit,
	j.deskit,
	a.unimed,
	a.canpedi,
	a.canpendi,
	a.canprepa,
	a.fechcie,
	a.horcie,
	a.usuario,
	siturefe, (SELECT fecmov from movimientos where movimientos.artrefer = a.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=b.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) as fecingre,
                (SELECT DATEDIFF(a.fechcie ,fecmov )*m.costodia*a.canprepa from movimientos where movimientos.artrefer = a.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=b.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) AS costoarticulo ,
                (SELECT DATEDIFF(a.fechcie ,fecmov )from movimientos where movimientos.artrefer = a.artrefer and movimientos.ubirefer=g.ubirefer and movimientos.cod_alma=b.almrefer and clamov='UBAL'	ORDER BY fecmov ASC LIMIT 1) AS diasalma,
        g.zoncodpre,
        g.ubirefer,
	m.costodia
FROM
	
  pedexcab b
	INNER JOIN pedexdet a on b.pedexentre=a.pedexentre
	LEFT JOIN arti c on a.artrefer=c.artrefer
	INNER JOIN artkit j on c.artrefer=j.artrefer
	left join ped_multiref d on a.pedexentre = d.pedido
	left join clientes f on f.clirefer = b.clirefer
	left join mref_exp e on d.multiref = e.mref and e.pedido = b.pedexentre
	left join artubiref g1 on a.artrefer = g1.artrefer
	left JOIN ubimapa g on g1.ubirefer = g.ubirefer 
	left JOIN costoalma m on g.dimension=m.cod_pal 
        INNER JOIN movimientos l on g.ubirefer=l.ubirefer and g.cod_alma=l.cod_alma
        where 1=1 {$dsArb} {$desPrv} {$desPed} {$dtd1} {$claseb} {$codalmab}
        AND b.siturefe='CE'
        GROUP BY a.pedexentre,a.pedpos";


    // echo $sq;

    $rs = $db->query($sq);

    $materiales = array();
    $zonas_pedidos = array();
    while ($row = $rs->fetch_assoc()) {
        if ($materiales[$row['pedexentre']] == '') {
            $materiales[$row['pedexentre']] = "'" . $row['artrefer'] . "'";
        } else {
            $materiales[$row['pedexentre']] .= ",'" . $row['artrefer'] . "'";
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
        $cabeceras[$row['pedexentre']]['send'] = $row['send'] == '1';
        $cabeceras[$row['pedexentre']]['pedrefer'] = '' . $row['pedexentre'];
        $cabeceras[$row['pedexentre']]['clinom'] = '' . $row['clinom'];
        $cabeceras[$row['pedexentre']]['codprove'] = '' . $row['codprove'];
        $cabeceras[$row['pedexentre']]['pedclase'] = '' . $row['pedclase'];
        $cabeceras[$row['pedexentre']]['pedrefec'] = '' . $row['pedexfec'];
        $cabeceras[$row['pedexentre']]['pedrefec'] = '' . $row['pedexfec'];
        $cabeceras[$row['pedexentre']]['pedrehor'] = '' . $row['pedexhor'];
        $cabeceras[$row['pedexentre']]['tercod'] = '' . $row['tercod'];
        $cabeceras[$row['pedexentre']]['ubirefer'] = '' . $row['ubirefer'];
        //  $almaec = $row['almrefer'];
        $cabeceras[$row['pedexentre']]['almrefer'] = '' . $row['almrefer'];
        $cabeceras[$row['pedexentre']]['codenv'] = '' . $row['codenv'];
        $cabeceras[$row['pedexentre']]['pedresitu'] = '' . $row['siturefe'];
        //        $cabeceras[$row['pedexentre']]['pedaction'] = '<a href="javascript:void(0);" onclick="asReception('."'".$row['pedexentre']."'".')" >Asignar</a>';


        if (!is_array($detalles[$row['pedexentre']])) {
            $detalles[$row['pedexentre']] = array();
        }
        if ($row['siturefe'] == 'PD' && $row['tercod'] == '') {
            $cabeceras[$row['pedexentre']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="nullDoc(' . "'" . $row['pedexentre'] . "'" . ",'Ex'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedexentre']]['pedactio'] = '';
        }

        //HASTA ACA ES LO QUE ESTABA//

        $auxDat = array();
        $auxDat["pedexentre"] = $row["pedexentre"];
        $auxDat["pedclase"] = $row["pedclase"];
        $auxDat["pedexfec"] = $row["pedexfec"];
        $auxDat["pedexhor"] = $row["pedexhor"];
        $auxDat["codenv"] = $row["codenv"];
        $auxDat["pedpos"] = $row["pedpos"];
        $auxDat["artcodi"] = $row["artcodi"];
        $auxDat["artrefer"] = $row["artrefer"];
        $auxDat["artdesc"] = htmlspecialchars(utf8_decode($row["artdesc"]));
        $auxDat["unimed"] = $row["unimed"];
        $auxDat["canpedi"] = $row["canpedi"];
        $auxDat["canpendi"] = $row["canpendi"];
        $auxDat["fecingre"] = $row["fecingre"];
        $auxDat["diasalma"] = $row["diasalma"];
        $auxDat["costoarticulo"] = $row["costoarticulo"];
        $auxDat["canprepa"] = $row["canprepa"];
        $auxDat["almrefer"] = $row["almrefer"];
        $auxDat["cencod"] = $row["cencod"];
        $auxDat["expst"] = $row["expst"];
        $auxDat["fechcie"] = $row["fechcie"];
        $auxDat["horcie"] = $row["horcie"];
        $auxDat["usuario"] = $row["usuario"];
        $auxDat["movref"] = $row["movref"];
        $auxDat["siturefe"] = $row["siturefe"];
        $auxDat["tercod"] = $row["tercod"];
        $auxDat["ubirefer"] = $row["ubirefer"];
        $auxDat["zoncodpre"] = "" . $row["zoncodpre"];
        array_push($detalles[$row['pedexentre']], $auxDat);
    }
    foreach ($cabeceras as $key => $value) {
        $suma = 0;
        $detalles_pedido = $detalles[$key];
        for ($j=0; $j < count($detalles_pedido) ; $j++) { 
            $suma += $detalles_pedido[$j]['costoarticulo'];
        }
        $cabeceras[$key]['suma'] = $suma;
    }
    
}



//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();
