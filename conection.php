<?php
if(!isset($_SESSION)) { session_start(); } 
$SERVER = '127.0.0.1';
$USER = 'root';
$PASS = 'gepa5266';
$DB = 'copia';
$sel = '';
//var_dump($_SERVER);
//if(!isset($_SESSION['user'])){
//    if($_SERVER["SCRIPT_NAME"] != "/promotores/login.php"){
//        header('Location:/promotores/login.php');
//        exit();
//    }
//}

function gtGrArt($id){
    switch ($id){
        case 'EL':
            return 'EL - Electrodomésticos';
            break;
        case 'EG':
            return 'EG - Eq. Gimnasia';
            break;
        case 'BI':
            return 'BI - Biciletas';
            break;
    }
}
