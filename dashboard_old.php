<?php
// header('Location:http://192.168.12.50/empaque/dashboard.php');
//exit();
require ('conect.php');
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//$db = new mysqli($SERVER,$USER,$PASS,$DB);
//var_dump($_SESSION);

switch ($_SESSION["user_rol"]){
    case 0:
        header("Location: dashboard_pri.php");
        break;
    case 1:
        header("Location: dashboard_sec.php");
        break;
    case 2:
        header("Location: dashboard_thd.php");
        break;
}
?>