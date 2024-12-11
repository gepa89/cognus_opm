<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
switch ($_POST['action']){
    case 'upd':
        $cod = $_POST['id'];
        $sq = "update ".$table." set ".
                "terdes = '".$_POST['Desc']."',".
                "tipac = '".$_POST['Tip']."',".
                "terzonpre = '".$_POST['Zona']."',".
                "almrefer = '".$_POST['Alm']."',".
                "canped = ".$_POST['cant']." where tercod = '".$_POST['Cod']."' ";
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
        $sq = "insert into ".$table." ({$fields}) values ";
        $sq .= "('".$_POST['Cod']."',".
                "'".$_POST['Desc']."',".
                "'".$_POST['Tip']."',".
                "'".$_POST['Zona']."',".
                "'".$_POST['Alm']."',".
                "'".$_POST['cant']."')";
                
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
