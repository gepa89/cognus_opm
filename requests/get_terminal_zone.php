<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require ('../conect.php');
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$zon = $_POST['zon'];
$tipo = $_POST['tip'];
$alma = $_POST['alma'];
$pdcnd = $pdstr = '';
$filtro_zona = "";
if ($zon != "todos") {
    $filtro_zona ="and terzonpre = '$zon'";
}
$sq = "select * from termi where tipac = '{$tipo}' and almrefer = '$alma' $filtro_zona";

$rs = $db->query($sq);
$cc = '';
while($ax = $rs->fetch_assoc()){
    if($cc == ''){
        $cc = '<option value="'.$ax['tercod'].'">'.$ax['tercod'].' - '. utf8_encode($ax['terdes']).'</option>';
    }else{
        $cc .= '<option value="'.$ax['tercod'].'">'.$ax['tercod'].' - '. utf8_encode($ax['terdes']).'</option>';
    }
}


echo $cc;

exit();
