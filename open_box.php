<?php
require ('conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
$bid = $_POST['dl_id'];
$bped = $_POST['pedido'];


$sqx = "select ca_id, ca_caja, ca_st, sum(lg_c_cant) as total from log_cajas left join log_material on lg_c_emp = ca_emp and lg_c_caja = ca_caja where ca_emp = '{$bped}' and ca_st = 0 GROUP BY ca_caja order by ca_caja desc limit 1";
//echo $sq;
$rx = $db->query($sqx);
while($ax = $rx->fetch_assoc()){
    if(!$ax['total'] || $ax['total'] <= 0){
        $sqd = "delete from log_cajas where ca_st = 0 and ca_id = {$ax['ca_id']}";
        //echo $sq;
        $db->query($sqd);
    }
}

$sq = "update log_cajas set 
        ca_st = 0
        where 
        ca_id = {$bid}";
//echo $sq;
if($db->query($sq)){
    $err = 0;
    $msg = "Caja ".$bid." abierta correctamente";
    $dt = str_pad($bid, 5, "0", STR_PAD_LEFT);
}else{
    $err = 1;
    $msg = 'No se pudo Cerrar caja';        
}

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'res' => $dt));
