<?php
require ('conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
$bid = explode('-', $_POST['dl_id']);
$bid = (int)$bid[1];

$sqc = "select count(*) as tot from log_material where lg_c_caja = {$bid} and lg_c_emp = '{$_POST['ent']}'";
//echo $sqc;
$rsx = $db->query($sqc);
$xx = $rsx->fetch_assoc();
if($xx['tot']>0){
    $sq = "update log_cajas set 
            ca_st = 1,
            ca_peso = '".$db->real_escape_string($_POST['peso'])."',
            ca_bulto = '".$db->real_escape_string($_POST['bulto'])."',
            ca_ubi = '".$db->real_escape_string($_POST['ubicacion'])."',
            ca_desc = '".$db->real_escape_string($_POST['descripcion'])."',
            ca_ts = now()
            where 
            ca_caja = {$bid} and ca_emp = '{$_POST['ent']}'";
    //echo $sq;
    if($db->query($sq)){
        $err = 0;
        $msg = "Caja ".$bid." cerrada correctamente";
        $last = "select ca_caja from log_cajas where ca_emp = '{$_POST['ent']}' order by ca_caja desc limit 1";
        $rl = $db->query($last);
        $rx = $rl->fetch_assoc();
        $dt = str_pad($rx['ca_caja']+1, 5, "0", STR_PAD_LEFT);
    }else{
        $err = 1;
        $msg = 'No se pudo Cerrar caja';        
    }
}else{
    $err = 1;
    $msg = 'La caja está vacía';        
}

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'res' => $dt));
