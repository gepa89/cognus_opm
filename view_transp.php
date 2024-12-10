<?php
require ('conect.php');
require_once("hanaDB.php");
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pds = $_POST['codigo'];
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

//foreach($pds as $ind => $val) {
    $vpd = str_pad($pds, 10, '0', STR_PAD_LEFT);
    $rst = odbc_exec($prd, $sqhan);
    $nmanif = "select distinct nromanif from sapabap1.zmm_manifiesto where entrega = '{$vpd}' order by nromanif desc limit 1";
    $rst = odbc_exec($prd, $nmanif);
    while ($rw = odbc_fetch_object($rst)){
        $data[$vpd]['cabecera']['manif'] = $rw->NROMANIF;
    }
    if(!$data[$vpd]['cabecera']['manif']){
        $data[$vpd]['cabecera']['manif'] = '';
    }
    if($vpd >= 4000000000){
        $sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
                left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
                where a.ebeln = '{$vpd}' and a.bukrs in ('1000','2000')";
    }else{
        $sqhan = "select a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                left join sapabap1.lips d on d.vbeln = a.vbeln
                left join sapabap1.vbak e on e.vbeln = d.vgbel
                inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                left join sapabap1.t005u c on b.regio = c.bland
                left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
                where a.vbeln = '{$vpd}'";
//                echo $sqhan;
    }
    $rst = odbc_exec($prd, $sqhan);
//    select distinct vbeln from sapabap1.vbfa where vbelv = '0012712950' and vbtyp_n = 'M'
//select distinct lifnr from sapabap1.vbpa where vbeln = 'vgbel' and parvw = 'SP'
    while ($rw = odbc_fetch_object($rst)){
        if($vpd>= 4800000000){
            $transp = '';
        }else{
            $transp = ''.$rw->TRANSP;
        }
    }
//}

//echo "<pre>"; var_dump($data); echo "<pre>";


require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'trs' => $transp));

