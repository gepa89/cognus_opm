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
//var_dum$flp($fl);
//echo $dsAr;
$cabeceras = $detalles = array();
if ($fl == 1) {
    $sq = "SELECT distinct 
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
        artipresen.canpresen,
        artipresen.preseref,
        siturefe, 
        (select GROUP_CONCAT(tercod) from assig_ped 
        where pedido = a.pedexentre and pedcajas='1' 
        GROUP BY pedido) as tercod, g.zoncodalm,
        e.ubirefer
        FROM 
            pedexcabcajas a 
        inner join pedexdetcajas b on a.pedexentre = b.pedexentre 
        left join arti c on b.artrefer = c.artrefer 
        left join artipresen on c.artrefer = artipresen.artrefer AND artipresen.cod_alma =  a.almrefer
        left join ped_multiref d on b.pedexentre = d.pedido
        left join clientes f on f.clirefer = a.clirefer
        left join mref_exp e on d.multiref = e.mref and e.pedido = a.pedexentre
        left join stockubi on stockubi.artrefer = b.artrefer 
        left JOIN ubimapa g on stockubi.ubirefer = g.ubirefer and g.ubitipo IN ('RE','PS')
        where 1=1  {$dsAr} {$desPrv} {$desPed} {$dtd1} {$clase} {$situ} {$codenv} {$codalma} and preseref = 'CJ'  
        union ALL
        SELECT DISTINCT
            b.pedexentre,
            b.almrefer,
            pedclase,
            clinom,
            pedexfec,
            pedexfec,
            pedexhor,
            codenv,
            j.poskit,
            a.artcodi,
            j.artrefkit,
            j.deskit,
            a.unimed,
            a.canpedi,
            a.canpendi,
            a.canprepa,
            a.almcod,
            a.cencod,
            a.expst,
            a.fechcie,
            a.horcie,
            a.usuario,
            a.movref,
            artipresen.canpresen,
            artipresen.preseref,
            siturefe, (select GROUP_CONCAT(tercod) from assig_ped where pedido = b.pedexentre and pedcajas='1' GROUP BY pedido) as tercod, g.zoncodalm,
            e.ubirefer 
            FROM 
            pedexcabcajas b 
            INNER JOIN pedexdetcajas a on b.pedexentre=a.pedexentre 
            LEFT JOIN arti c on a.artrefer=c.artrefer 
            left join artipresen on c.artrefer = artipresen.artrefer AND artipresen.cod_alma =  b.almrefer 
            left JOIN artkit j on c.artrefer=j.artrefer 
            left join ped_multiref d on a.pedexentre = d.pedido 
            left join clientes f on f.clirefer = b.clirefer 
            left join mref_exp e on d.multiref = e.mref and e.pedido = b.pedexentre 
            left join artubiref g1 on a.artrefer = g1.artrefer 
            left JOIN ubimapa g on g1.ubirefer = g.ubirefer and g.ubitipo IN ('RE','PS')       
            where 1=1  {$dsArb} {$desPrv} {$desPed} {$dtd1} {$claseb} {$situb} {$codenvb} {$codalmab} and preseref = 'CJ' and a.artcodi is not null";
    $rs = $db->query($sq);
    $zonas_pedidos = array();
    $materiales = array();
    while ($row = $rs->fetch_assoc()) {
        if ($materiales[$row['pedexentre']] == '') {
            $materiales[$row['pedexentre']] = "'" . $row['artrefer'] . "'";
        } else {
            $materiales[$row['pedexentre']] .= ",'" . $row['artrefer'] . "'";
        }
    }
    $zonas_ocupadas = array();
    $zonas_ocupadas_terminal = array();
    //    echo $materiales;
    foreach ($materiales as $ped => $arr) {
        $sqzon = "select  b.zoncodalm from stockubi a 
        inner join ubimapa b on a.ubirefer = b.ubirefer and a.cod_alma = b.cod_alma
        where a.artrefer in ({$arr}) GROUP BY  b.zoncodalm";
        //    echo $sqzon;
        $rzon = $db->query($sqzon);
        while ($azon = $rzon->fetch_assoc()) {
            $zonas[$ped][] = $azon['zoncodalm'];
            $zonas_pedidos[$ped][] = array("zona" => $azon['zoncodalm'], "articulo" => $azon['artrefer']);
        }
        $sql = "select zona,tercod from assig_ped where pedido = '$ped' and pedcajas=1";
        $query = $db->query($sql);

        while ($fila = $query->fetch_assoc()) {
            $zonas_ocupadas[$ped][] = $fila["zona"];
            $zonas_ocupadas_terminal[$ped] = $fila["tercod"];
        }
    }



    //    var_dump($zonas);
    $rs = $db->query($sq);
    $lk = '';


    while ($row = $rs->fetch_assoc()) {
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
        if ($row['siturefe'] == 'CE' && $row['pedsent'] == 0 && $row['pedclase'] <> 'REPO') {
            $cabeceras[$row['pedexentre']]['pedsendi'] = '<a class="btn btn-success btn-fnt-size" href="javascript:void(0);"onclick="sendExpedition(' . "'" . $row['pedexentre'] . "'" . ')" >Enviar</a>';
        } else if ($row['siturefe'] == 'CE' && $row['pedsent'] == 1 && $row['pedclase'] <> 'REPO') {
            $cabeceras[$row['pedexentre']]['pedsendi'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="sendExpedition(' . "'" . $row['pedexentre'] . "'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedexentre']]['pedsendi'] = '';
        }
        if (!is_array(@$detalles[$row['pedexentre']])) {
            $detalles[$row['pedexentre']] = array();
        }
        if ($row['siturefe'] == 'PD' && $row['tercod'] == '') {
            $cabeceras[$row['pedexentre']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="nullDoc(' . "'" . $row['pedexentre'] . "'" . ",'Ex'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedexentre']]['pedactio'] = '';
        }

        //HASTA ACA ES LO QUE ESTABA//
        $cantidad_presentacion = (int) $row['canpresen'];
        $auxDat = array();
        $auxDat["pedexentre"] = $row["pedexentre"];
        $auxDat["canpresen"] = $row["canpresen"];
        $auxDat["pedclase"] = $row["pedclase"];
        $auxDat["pedexfec"] = $row["pedexfec"];
        $auxDat["pedexhor"] = $row["pedexhor"];
        $auxDat["codenv"] = $row["codenv"];
        $auxDat["pedpos"] = $row["pedpos"];
        $auxDat["artcodi"] = $row["artcodi"];
        $auxDat["artrefer"] = $row["artrefer"];
        $auxDat["artdesc"] = htmlspecialchars(utf8_decode($row["artdesc"]));
        $auxDat["unimed"] = $row["unimed"];
        $auxDat["canpedi"] = calcularCantidadPresentacion((int) $row["canpedi"], (int) $cantidad_presentacion) . " " . $row['preseref'];
        $auxDat["canpendi"] = calcularCantidadPresentacion((int) $row["canpendi"], (int) $cantidad_presentacion) . " " . $row['preseref'];
        $auxDat["canprepa"] = calcularCantidadPresentacion((int) $row["canprepa"], (int) $cantidad_presentacion) . " " . $row['preseref'];
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
        $auxDat["zoncodalm"] = "" . $row["zoncodalm"];
        array_push($detalles[$row['pedexentre']], $auxDat);
    }
    foreach ($cabeceras as $ped => $dat) {
        $assigns = '';
        //        asignados
        if ($dat['pedresitu'] <> 'AN') {
            if ($dat['pedclase'] <> 'REPO') {
                if ($dat['tercod'] != '') {
                    $assigns = explode(',', $dat['tercod']);
                } else {
                    $assigns = '';
                }
                $lk = '';
                if (!is_array($assigns)) {
                    // var_dump($zonas[$ped]);

                    // exit();

                    foreach ($zonas[$ped] as $k => $v) {
                        $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);"onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $dat['almrefer'] . "','REPO'" . ')" >Asignar Z-' . $v . '</a><br/>';
                    }
                } else {
                    foreach ($zonas_pedidos[$ped] as $zona) {
                        $v = $zona['zona'];
                        if (!in_array($v, $zonas_ocupadas[$ped])) {
                            $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);" onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $auxDat["almrefer"] . "','REPO'" . ')" >Asignar Z-' . $v . '</a><br/>';
                        } else {
                            $lk .= $zonas_ocupadas_terminal[$ped] . "<br>";
                        }
                    }
                }
            } else {
                $SQ = "select * from assig_ped where pedido = '{$ped}'";
                //            echo $SQ;
                $rk = $db->query($SQ);
                $rxs = $rk->fetch_assoc();
                //            var_dump($rxs);
                if ($rk->num_rows > 0) {
                    //                $dt = $rxs->fetch_assoc();
                    $lk = "" . $rxs['tercod'];
                } else {
                    $ped = trim($ped);
                    $cod_almacen = strtoupper(trim($dat['almrefer']));
                    $lk = "<a class=\"btn btn-primary btn-fnt-size\" href=\"javascript:void(0);\" onclick=\"asReceptionRe('$ped','REPO','{$cod_almacen}')\" >Asignar REPO</a><br/>";
                }
                //            var_dump($lk);

            }
        } else {
            $lk = "";
        }

        $cabeceras[$ped]['pedaction'] = $lk;
    }
}



//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();

function calcularCantidadPresentacion($cantidad, $cantidad_presentacion)
{
    $cantidad_final = 0;
    if ($cantidad_presentacion <= $cantidad) {
        $offset = $cantidad % $cantidad_presentacion;
        $cantidad_final = ($cantidad - $offset) / $cantidad_presentacion;
    }
    return $cantidad_final;
}
