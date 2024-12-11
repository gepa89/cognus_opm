<?php
require ('conect.php');
require_once("hanaDB.php");
$db = new mysqli($SERVER,$USER,$PASS,$DB);
date_default_timezone_set('America/Asuncion');

$arr = array_unique($_POST['codigos'], SORT_NUMERIC);

$sqhan = "select * from sapabap1.zmm_manifiesto order by nromanif desc limit 1";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$rw = odbc_fetch_object($rst);
//echo "<pre>"; var_dump((int)$rw->NROMANIF);echo "<pre>";

$lastManif = (int)$rw->NROMANIF;
//
//
odbc_autocommit($prd,false); 

$Ymd = date('Ymd', strtotime('now'));
$His = date('His', strtotime('now'));
$crManif = str_pad($lastManif+1, 10, '0', STR_PAD_LEFT);
$cc = 0;
foreach($arr as $k => $v){
    $dt[$cc]['MANDT'] = '300';
    $dt[$cc]['NROMANIF'] = $crManif;
    $dt[$cc]['ENTREGA'] = str_pad($v, 10, '0', STR_PAD_LEFT);
    $dt[$cc]['CHOFER'] = ''.$_POST['mnChofer'];
    $dt[$cc]['OBSER'] = ''.$_POST['mnObs'];
    $dt[$cc]['USUARIO'] = ''.$_SESSION['user'];
    $dt[$cc]['FECHA'] = $Ymd;
    $dt[$cc]['HORA'] = $His;
    $cc++;
}

$hanaqr = "INSERT INTO sapabap1.zmm_manifiesto "
                . "(MANDT, NROMANIF, ENTREGA, CHOFER, OBSER, USUARIO, FECHA, HORA) "
                . "values (?,?,?,?,?,?,?,?)";
//                            . "values ".$vals.'';
$res = odbc_prepare($prd, $hanaqr);
foreach($dt as $k => $val){
    if(!odbc_execute($res, $val)){
        $errs[] = 1;
    }else{
        $errs[] = 0;
    }
}
//echo "<pre>";var_dump($dt);echo "</pre>";
if(!in_array(1, $errs)){
    odbc_commit($prd); 
    $err = 0;
    $msg = "";
}else{
    $err = 1;
    $msg = "Ocurrió un error al cerrar el documento";
}
//
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $crManif));

