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

$sqhan = "select * from sapabap1.ZMM003_EMP_CLASE";
//                echo $sqhan;
$rst = odbc_exec($qas, $sqhan);
while ($rw = odbc_fetch_object($rst)){
//    $data[] = $rw;
    $sq = "insert into usuarios set pr_user = '". utf8_decode($rw->USUARIO)."', pr_ts = now()";
    $db->query($sq);
}

//echo "<pre>";
//var_dump($data);
//echo "</pre>";
require ('closeconn.php');
//echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data));

