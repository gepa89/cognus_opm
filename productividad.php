<?php // $shell = true;
//phpinfo();
require('conect.php');
//require ('/var/www/html/conect.php');
ini_set('memory_limit', '1024M');
$almacen = 'CD11';
if (!empty($_GET['almacen'])) {
    $almacen = $_GET['almacen'];
}

$db = new mysqli($SERVER, $USER, $PASS, $DB);

if (isset($_POST['usuario'])) {
    $usr = " and usuario = '{$_POST['usuario']}' ";
} else {
    $usr = "";
}
$hoy = date("Y-m-d");
$qry_main = "/*Expedicion*/
    SELECT
        DISTINCT
        pd.pedexentre as pedido,
        pd.artrefer as artrefer,
        pd.pedpos as pedpos,
        pd.canpedi as canpedi,
        pd.canprepa as canprepa,
        pd.usuario as ap_usr,
        pd.fechcie as fechcie
    FROM
        pedexdet pd
    INNER JOIN
        pedexcab pc ON
        pd.pedexentre = pc.pedexentre
    INNER JOIN
        artmov am ON
        pc.movref = am.movref
    WHERE
        pc.siturefe IN ('CE', 'PP')
        AND pd.canprepa <> '0'
        AND pd.fechcie = '$hoy'
        AND pc.almrefer = '$almacen'
    UNION 
    SELECT 
            DISTINCT 
            pedrecab.pedrefer as pedido,
        pedredet.artrefer as artrefer,
        pedredet.pedpos as pedpos,
        pedredet.canpedi as canpedi,
        pedredet.canprepa as canprepa,
        pallet_mat.ap_usr as ap_usr,
        pedrecab.pedrecie as pedrecie
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
    WHERE
        pedrecab.pedresitu in ('CE', 'UB', 'PP')
        and pedredet.canprepa <> '0'
        AND pedrecab.pedrecie = '$hoy'
        AND pedrecab.almrefer = '$almacen'
    UNION 
    SELECT 
        DISTINCT 
        pedubicab.pedubicod as pedido,
        pedubidet.artrefer as artrefer,
        pedubidet.pedpos as pedpos,
        pedubidet.cantiu as canpedi,
        pedubidet.canubi as canprepa,
        pedubidet.usuario as ap_usr,
        pedubidet.fecha as fecierre
    FROM
        pedubicab
    INNER JOIN pedubidet on
        pedubicab.pedubicod = pedubidet.pedubicod
    INNER JOIN artmov on
        pedubicab.pedclase = artmov.pedclase
    WHERE
        pedubidet.expst = '1'
        AND pedubidet.fecha = '$hoy'
        AND pedubicab.cod_alma = '$almacen'
        ";
$rs = $db->query($qry_main);
$usr = $tts = array();
$cc = 0;
$pedidos = $sku = array();
while ($ax = $rs->fetch_assoc()) {
    $pedidos[$ax['ap_usr']][$ax['pedido']][] = $ax; //el count del array me dice la cant de pedidos
    $sku[] = 1; //el count del array me dice la cant de sku

}
$dt = array();
$usrarr = [];
foreach ($pedidos as $usr => $dd) {
    $usrarr[] = $usr;
    foreach ($dd as $ped => $dx) {
        $dt[$usr]['Pedidos']++;
        $dt[$usr]['Materiales'] += count($dx);

        $tts['Pedidos']++;
        $tts['Materiales'] += count($dx);
        foreach ($dx as $idx => $vax) {
            $dt[$usr]['Cantidad'] += $vax['canprepa'];
            $tts['Cantidad'] += $vax['canprepa'];
        }

    }
}
//echo "<pre>";var_dump($tts);echo "</pre>";
//$usrarr[] = $ax['usuario'];
//    
//    $dt[$ax['usuario']]['Pedidos']++;
//    $tts['Pedidos']++;
//    $tts['Materiales']++;
//    $tts['Cantidad'] += $ax['canprepa'];
//    $dt[$ax['usuario']]['Materiales']++;
//    $dt[$ax['usuario']]['Cantidad'] += $ax['canprepa'];
$res = array();
if ($dt) {
    foreach ($dt as $usr => $data) {
        foreach ($data as $key => $val) {
            //        echo $key."<br/>";
            $res[$key][] = $val;
        }
    }
}

//var_dump($usrarr);
echo json_encode(array('usr' => $usrarr, 'dat' => $dt, 'series' => $res, 'totales' => $tts));