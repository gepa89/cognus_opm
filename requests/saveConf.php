<?php

require('../conect.php');
include 'GO/Crontab.php';
use GO\Crontab;

$cronController = new Crontab();

$pd = '';
$fl = 0;
$fecha = date('Y-m-d');
$hora = date("H:i:s");
$usuario = $_SESSION['user'];
$script = 'conf_asig_auto';

$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//var_dump($_POST['ckAsig']);
if ($_POST['selPrep'] != '' && $_POST['selLect'] != '') {
    $ck = 0;
    $cp = $_POST['ckSpex'] == 'true' ? '1' : '0';
    if (($_POST['ckAsig'] == 'true')) {
        $ck = 1;
        $sctexists = $cronController->doesJobNameExist($script);
        if ($sctexists) {
            $jobout = $cronController->getJobString($script);
            $jobin = str_replace('#', '', $jobout);
            $resultx = $cronController->updateJob($jobin, $jobout);
        }
    } else {
        $sctexists = $cronController->doesJobNameExist($script);

        if ($sctexists) {
            $jobout = $cronController->getJobString($script);
            //            var_dump($jobout);
            $jobin = str_replace('#', '', $jobout);
            $jobin = "#" . $jobout;
            $resultx = $cronController->updateJob($jobin, $jobout);
        }
    }

    $sql = "SELECT * FROM config WHERE cod_alma = '{$_POST['codalma']}'";
    $dato = $db->query($sql)->fetch_object();
    if ($dato) {
        $sq = "UPDATE config 
        set asig = {$ck}, 
        ruta = '{$_POST['selPrep']}', 
        lectura = '{$_POST['selLect']}', 
        estaparam = {$cp}, 
        estaparanterior = {$cp}, 
        estaparnuevo = {$cp},  
        usuario = '{$usuario}',       
        fecha = '{$fecha}',     
        hora = '{$hora}', 
        cod_alma = '{$_POST['codalma']}' 
        WHERE cod_alma='{$_POST['codalma']}'";
        print_r($sq);
    }else{
        $sq = "INSERT into config set asig = {$ck}, ruta = '{$_POST['selPrep']}', lectura = '{$_POST['selLect']}', estaparam = {$cp}, cod_alma = '{$_POST['codalma']}'";
    }
    if ($db->query($sq)) {
           
        $msg = "Datos guardados.";
    }
} else {
    $msg = "Favor completar los campos.";
}
//ckAsig
//selPrep
//selLect

echo json_encode(array('msg' => $msg));