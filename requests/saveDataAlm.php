<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$table = $_POST['table'];
$alm = strtoupper($_POST['id']); 
$descr = $_POST['desc']; 
$cen = strtoupper($_POST['cen']);
$descr = $db->real_escape_string(ucwords(strtolower($descr)));
switch ($_POST['action']){
    case 'upd':
        $cod = $_POST['id'];
        $sq = "update ".$table." set  almcod = '{$alm}', almdes = '{$descr}', cencod = '{$cen}' where almcod = '{$alm}' ";
//        echo $sq;
        if($db->query($sq)){
            $err = 0;
            $msg = 'Datos guardados.';
        }else{
            $err = 1;
            $msg = 'No se pudo guardar registro.';
        }
        break;
    case 'add':
        $sq = "insert into ".$table." set almcod = '{$alm}', almdes = '{$descr}', cencod = '{$cen}'";
//        echo $sq;
        if($db->query($sq)){
            $err = 0;
            $msg = 'Datos guardados.';
        }else{
//            echo $db->error;
            $err = 1;
            $msg = 'No se pudo guardar registro.';
        }
        break;
}
echo json_encode(array('err' => $err,'msg' => $msg));
