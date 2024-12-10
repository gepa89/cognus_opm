<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
switch ($_POST['action']){
    case 'upd':
        $Art = $_POST['id'];
        $sq = "update ".$table." set ".
                "clinom = '".$_POST['Nom']."',".
                "clidirec = '".$_POST['Dir']."',".
                "clitel = '".$_POST['Tel']."',".        
                "cod_alma = '".$_POST['Alm']."' where clirefer = '".$_POST['Pro']."' ";
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
        $sq .= "('".$_POST['Pro']."',".
                "'".$_POST['Nom']."',".
                "'".$_POST['Dir']."',".
                "'".$_POST['Tel']."',".
                "'".$_POST['Alm']."')";
                
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
