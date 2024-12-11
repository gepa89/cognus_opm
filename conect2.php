<?php
require ('hanaDB2.php');
require ('/var/www/html/saprfc/prd.php');
if(!isset($_SESSION)) { session_start(); } 
$SERVER = '127.0.0.1';
$USER = 'root';
$PASS = 'gepa5266';
$DB = 'copia';

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
    }
}
