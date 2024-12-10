<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$table = $_POST['table'];
$descr = $_POST['desc']; 
$val = strtoupper($_POST['cecod']);
$descr = $db->real_escape_string(ucwords(strtolower($descr)));
switch ($_POST['action']){
    case 'upd':
        $cod = $_POST['id'];
        $sq = "update ".$table." set cendes = '{$descr}', cencod = '{$val}' where cencod = '{$val}' ";
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
        $sq = "insert into ".$table." set cendes = '{$descr}', cencod = '{$val}'";
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
