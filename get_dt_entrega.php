<?php
require ('conect.php');
require_once("hanaDB.php");
require_once(__DIR__ . "/../saprfc/prd.php");
include 'src/adLDAP.php';

if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad($_POST['codigo'], 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
$sqhanax = "select a.vstel, a.kunnr, a.kunag from sapabap1.likp a                  
            where a.vbeln = '{$pd}'";
//                echo $sqhan;
$rstax = odbc_exec($prd, $sqhanax);
while ($rwx = odbc_fetch_object($rstax)){
    $uax_dt['KUNNR'] = $rwx->KUNNR;
    $uax_dt['KUNAG'] = $rwx->KUNAG;
}
//var_dump($uax_dt);
if($uax_dt['KUNNR'] != $uax_dt['KUNAG']){
    $sqhan = "select a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                left join sapabap1.lips d on d.vbeln = a.vbeln
                left join sapabap1.vbak e on e.vbeln = d.vgbel
                inner join sapabap1.kna1 b on a.kunnr = b.kunnr 
                left join sapabap1.t005u c on b.regio = c.bland
                left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                left join sapabap1.lfa1 h on h.lifnr = g.lifnr  
            where a.vbeln = '{$pd}'";
            

}else{
    $sqhan = "select a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                left join sapabap1.lips d on d.vbeln = a.vbeln
                left join sapabap1.vbak e on e.vbeln = d.vgbel
                inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                left join sapabap1.t005u c on b.regio = c.bland
                left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
            where a.vbeln = '{$pd}'";
    
}
//echo $sqhan;
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    $data['kunnr'] = $rw->KUNNR;
    $data['vgbel'] = $rw->VGBEL;
    $data['name1'] = $rw->NAME1;
    $data['ort01'] = $rw->ORT01;
    $data['regio'] = $rw->REGIO;
    $data['stras'] = $rw->STRAS;
    $data['bezei'] = $rw->BEZEI;
    $data['lifnr'] = $rw->TRANSP;
    $data['vsbed'] = $rw->VSBED.' '.$rw->VTEXT;
    if($rw->VKORG == 'COMA'){
        $data['vkorg'] = 'COMAGRO S.A.E.';
    }else if($rw->VKORG == 'ALAS'){
        $data['vkorg'] = 'ALAS S.A.';
    }else if($rw->VKORG == 'ATLA'){
        $data['vkorg'] = 'ATLANTIC S.A.';
    }else{
        $data['vkorg'] = 'CHACOMER S.A.E.';
    }
    
}
if(count($data) > 0){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}

$sqc = "select sum(ca_bulto) as tot from log_cajas where ca_emp = '{$pd}'";
//echo $sqc;
$rsx = $db->query($sqc);
$xx = $rsx->fetch_assoc();

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data, 'cant' => $xx['tot']));

