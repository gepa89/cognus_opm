<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//var_dump($_POST['ckAsig']);
if($_POST['desc'] == 1){
    $upd = "update ruta set rutesta = 0";
    $db->query($upd);
}

$upd = "update ruta set rutesta = {$_POST['desc']} where rutcod = '{$_POST['id']}'";
//echo $upd;
if($db->query($upd)){
    $msg = 'Datos guardads';
    $err = 0;
}else{
    $msg = 'Error al guardar';
    $err = 1;
}


echo json_encode(array('msg' => $msg,'err' => $err));
