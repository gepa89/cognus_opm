<?php
require ('conect.php');
require_once("hanaDB.php");
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
odbc_autocommit($prd,false); 
$pd = str_pad($_POST['codigo'], 10, '0', STR_PAD_LEFT);
$cr = strtoupper($_POST['centro']);
$usr = strtoupper($_POST['usuario']);
//codigo
//centro
$db = new mysqli($SERVER,$USER,$PASS,$DB);
date_default_timezone_set('America/Asuncion');
// var_dump($_POST);
$usr = ($_POST['usuario']);
$vals['MANDT'] = '300';
$vals['ENTREGA'] = $pd;
$vals['TIPO'] = '001';
$vals['PEXPEDICION'] = $cr;
$vals['HORA'] = date("His", strtotime('now'));
$vals['FECHA'] = date("Ymd", strtotime('now'));
$vals['USUARIO'] = $usr;
//MANDT = 300
//ENTREGA = ENTREGA
//TIPO = 001
//PEXPEDICION = CD01
//HORA = His
//FECHA = Ymd
//USUARIO = user
$sqhan = "insert into sapabap1.ZMM003_REGISTRO (MANDT, ENTREGA, TIPO, PEXPEDICION, HORA, FECHA, USUARIO) values (?,?,?,?,?,?,?) ";
//                echo $sqhan;
$res = odbc_prepare($prd,$sqhan);
if(!odbc_execute($res, $vals)){
    $errs[] = 1;
}else{
    $errs[] = 0;
}
if(!in_array(1, $errs)){
    odbc_commit($prd); 
    $err = 0;
    $msg = "Tiempo registrado correctamente";
}else{
    $err = 1;
    $msg = "Ocurrió un error al registrar tiempo";
}
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data));