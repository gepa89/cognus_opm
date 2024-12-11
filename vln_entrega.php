<?php
require ('conect.php');
require_once("hanaDB.php");
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad($_POST['codigo'], 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

$sqhan = "select distinct a.posnr,a. matnr, a.arktx, a.lfimg, b.rfmng from  sapabap1.lips a 
            left join sapabap1.vbfa b on b.vbelv = a.vbeln and b.posnv = a.posnr
            where a.vbeln = '{$pd}'";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    $data[(int)$rw->POSNR]['pos'] = ''.$rw->POSNR;
    $data[(int)$rw->POSNR]['mat'] = ''.$rw->MATNR;
    $data[(int)$rw->POSNR]['desc'] = ''.$rw->ARKTX;
    $data[(int)$rw->POSNR]['cant'] = (int)$rw->LFIMG;
    $data[(int)$rw->POSNR]['cantpk'] = (int)$rw->RFMNG;
}
if(count($data) > 0){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data));

