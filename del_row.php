<?php
require ('conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
$sq = "delete from log_material where lg_id = {$_POST['dl_id']}";
//echo $sq;
if($db->query($sq)){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'No se pudo eliminar el registro';        
}
echo json_encode(array('msg' => $msg, 'err' => $err));
