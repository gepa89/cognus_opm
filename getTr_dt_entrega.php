<?php
require ('conect.php');
include 'src/adLDAP.php';
require_once("hanaDB.php");
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad($_POST['codigo'], 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

$sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
    where a.ebeln = '{$pd}' and a.bukrs in ('1000','2000')";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    $data['ebeln'] = $rw->EBELN;
    $data['werks'] = $rw->WERKS;
    $data['lgort'] = $rw->LGORT;
    $data['reslo'] = $rw->RESLO;
    $data['bukrs'] = $rw->BUKRS;
    $data['lgobe'] = $rw->LGOBE;
    
    
    if($rw->BUKRS == '2000'){
        $data['emp'] = 'COMAGRO S.A.E.';
    }else if($rw->BUKRS == '1000'){
        $data['emp'] = 'CHACOMER S.A.E.';
    }    
}
$qrybulks = "select distinct caja, cantbultos from sapabap1.ZMM_BULTOS where VBLEN = '{$pd}'";
//    echo $qrybulks;
$rstbk = odbc_exec($prd, $qrybulks);
while ($rx = odbc_fetch_object($rstbk)){
    $data['bultos'] += (int)$rx->CANTBULTOS;
}
if(count($data) > 0){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}
if($err == 0){
    //var_dump($data);
    $sqc = "select sum(ca_bulto) as tot from log_cajas where ca_emp = '{$pd}'";
    //echo $sqc;
    $rsx = $db->query($sqc);
    $xx = $rsx->fetch_assoc();

}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data, 'cant' => $xx['tot']));

