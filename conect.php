<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('America/Asuncion');
if(!isset($_SESSION)) { session_start(); } 
$SERVER = '193.203.175.72';
$USER = 'u920596356_opm';
$PASS = 'Guidagog2050.';
$DB = 'u920596356_opm';
define('SERVER_MYSQL', $SERVER);
define('USER_MYSQL', $USER);
define('PASS_MYSQL', $PASS);
define('DB_MYSQL', $DB);

function getLabel($id){
    switch($id){
        case 'CE':
            return 'Cerrado';
            break;
        case 'PD':
            return 'Pendiente';
            break;
        case 'PP':
            return 'Prep. Parcial';
            break;        
        case 'UB':
            return 'Ubicado';
            break;     
        case 'AN':
            return 'Anulado';
            break;
    }
}
function separateLocation($location){
    $es = substr($location, 0, 4);
    $hu = substr($location, 4, 3);
    $ni = substr($location, 7);
    $ubix = $es."-".$hu."-".$ni;//.$sbNiv[$l];
    return $ubix;
}
