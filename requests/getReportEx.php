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
        $cabeceras[$row['pedexentre']]['terminal'] = '' . $row['terminal'];
        $cabeceras[$row['pedexentre']]['ubirefer'] = '' . $row['ubirefer'];
        $cabeceras[$row['pedexentre']]['multiref'] = '' . $row['multiref'];
        $cabeceras[$row['pedexentre']]['factura'] = '' . $row['factura'];
        $cabeceras[$row['pedexentre']]['usuario_anulacion'] = $row['pedexrack'];
        $cabeceras[$row['pedexentre']]['fecha_anulacion'] = $row['pedexcie'];
        $cabeceras[$row['pedexentre']]['hora_anulacion'] = $row['pedexhorcie'];
        //  $almaec = $row['almrefer'];
        $cabeceras[$row['pedexentre']]['almrefer'] = '' . $row['almrefer'];
        $cabeceras[$row['pedexentre']]['codenv'] = '' . $row['codenv'];
        $cabeceras[$row['pedexentre']]['pedresitu'] = '' . $row['siturefe'];
        //        $cabeceras[$row['pedexentre']]['pedaction'] = '<a href="javascript:void(0);" onclick="asReception('."'".$row['pedexentre']."'".')" >Asignar</a>';
        if (in_array($row['siturefe'], ['CE', 'PP']) && $row['pedsent'] == 0 && $row['pedclase'] <> 'REPO') {
            $btn = $row['send'] ? "Enviado" : "Enviar";

            $cabeceras[$row['pedexentre']]['pedsendi'] = '<a class="btn btn-success btn-fnt-size" href="javascript:void(0);"onclick="sendExpedition(' . "'" . $row['pedexentre'] . "'" . ')" >' . $btn . '</a>';
        } else if ($row['siturefe'] == 'CE' && $row['pedsent'] == 1 && $row['pedclase'] <> 'REPO') {
            $cabeceras[$row['pedexentre']]['pedsendi'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="sendExpedition(' . "'" . $row['pedexentre'] . "'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedexentre']]['pedsendi'] = '';
        }
        if (!is_array(@$detalles[$row['pedexentre']])) {
            $detalles[$row['pedexentre']] = array();
        }
        if ($row['siturefe'] == 'PD' && $row['terminal'] == '') {
            $cabeceras[$row['pedexentre']]['pedactio'] = '<a class="btn btn-danger btn-fnt-size" href="javascript:void(0);"onclick="nullDoc(' . "'" . $row['pedexentre'] . "'" . ",'Ex'" . ')" >Anular</a>';
        } else {
            $cabeceras[$row['pedexentre']]['pedactio'] = '';
        }
        if ($row['siturefe'] == 'AN') {
            $cabeceras[$row['pedexentre']]['pedaction'] = $row['pedexrack'];
            $cabeceras[$row['pedexentre']]['pedactio'] = $row['pedexcie'];
            $cabeceras[$row['pedexentre']]['pedsendi'] = $row['pedexhorcie'];
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
        $auxDat["canpedi"] = formatear_numero($row["canpedi"]);
        $auxDat["canpendi"] = formatear_numero($row["canpendi"]);
        $auxDat["canprepa"] = formatear_numero($row["canprepa"]);
        $auxDat["almrefer"] = $row["almrefer"];
        $auxDat["cencod"] = $row["cencod"];
        $auxDat["expst"] = $row["expst"];
        $auxDat["fechcie"] = $row["fechcie"];
        $auxDat["horcie"] = $row["horcie"];
        $auxDat["usuario"] = $row["usuario"];
        $auxDat["movref"] = $row["movref"];
        $auxDat["siturefe"] = $row["siturefe"];
        $auxDat["terminal"] = $row["terminal"];
        $auxDat["ubirefer"] = $row["ubirefer"];
        $auxDat["zoncodpre"] = "" . $row["zoncodpre"];
        array_push($detalles[$row['pedexentre']], $auxDat);
    }

    foreach ($cabeceras as $ped => $dat) {
        $assigns = '';
        if ($dat['pedresitu'] <> 'AN') {
            if ($dat['pedclase'] <> 'REPO') {
                if ($dat['terminal'] != '') {
                    $assigns = explode(',', $dat['terminal']);
                } else {
                    $assigns = '';
                }
                $lk = '';
                if (!is_array($assigns)) {
                    foreach ($zonas[$ped] as $k => $v) {
                        $habilitar_boton = false;
                        foreach ($zonas_pedidos[$ped] as $zona) {
                            if ($zona['zona'] == $v) {
                                $habilitar_boton = (int) $zona['cantidad'] > 0;
                                break;
                            }
                        }
                        if ($habilitar_boton) {
                            $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);"onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $dat['almrefer'] . "','EXPE'" . ')" >Asignar Z-' . $v . '</a><br/>';
                        }
                    }
                } else {

                    foreach ($zonas_pedidos[$ped] as $zona) {
                        $v = $zona['zona'];
                        if (!in_array($v, $zonas_ocupadas[$ped])) {
                            $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);" onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $auxDat["almrefer"] . "','EXPE'" . ')" >Asignar Z-' . $v . '</a><br/>';
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
                    $sql = "SELECT ubimapa.zoncodalm 
                    FROM pedexdet inner join stockubi on stockubi.artrefer = pedexdet.artrefer
                    inner join ubimapa on ubimapa.ubirefer = stockubi.ubirefer and stockubi.cod_alma=ubimapa.cod_alma
                    WHERE ubimapa.ubitipo = 'RE' limit 1";
                    $query = $db->query($sql);
                    if (!$query) {
                        print_r("error");
                    }
                    $dato = $query->fetch_object();

                    $cod_almacen = strtoupper(trim($dat['almrefer']));
                    $lk = "<a class=\"btn btn-primary btn-fnt-size\" href=\"javascript:void(0);\" onclick=\"asReceptionRe('$ped','REPO','{$dato->zoncodalm}','{$cod_almacen}')\" >Asignar REPO</a><br/>";
                }
                //            var_dump($lk);

            }
            $cabeceras[$ped]['pedaction'] = $lk;
        }
    }
}

//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();
