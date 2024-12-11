<?php
require ('conect.php');
require_once("hanaDB.php");
include 'src/adLDAP.php';

if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad(trim($_POST['codigo']), 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);


$sqhan = "select NROMANIF from sapabap1.zmm_manifiesto where ENTREGA = '{$pd}'";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    $res[] = $rw;
}
if(count($res) == 0){
    $sqc = "select * from log_cajas where ca_emp = '{$pd}' and ca_st = 1";
    //echo $sqc;
    $rsx = $db->query($sqc);
    $cc = 0;
    while($xx = $rsx->fetch_assoc()){
        $dt[$cc]['ca_id'] = $xx['ca_id'];
        $dt[$cc]['ca_emp'] = $xx['ca_emp'];
        $dt[$cc]['ca_caja'] = 'CAJA-'.str_pad($xx['ca_caja'], 5, '0', STR_PAD_LEFT);
        $dt[$cc]['ca_peso'] = $xx['ca_peso'];
        $dt[$cc]['ca_ubi'] = $xx['ca_ubi'];
        $dt[$cc]['ca_bulto'] = $xx['ca_bulto'];
        $dt[$cc]['ca_desc'] = $xx['ca_desc'];
        $cc++;
    }
    if(count($dt) > 0){
        $msg = '';
        $err = 0;
    }else{
        $msg = 'Pedido sin cajas cerradas';
        $err = 1;
    }
}else{
        $msg = 'Pedido con manifiesto generado, no se puede modificar';
        $err = 1;
    }

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $dt));

