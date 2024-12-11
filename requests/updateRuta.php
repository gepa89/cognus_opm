<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$row = $_POST['data'];
$det = $_POST['det'];
$id = $_POST['id'];

if($det == 'true'){
    $detx = 1;
    $upd = "update ruta set rutesta = 0";
    $db->query($upd);
    $upd = "update ruta set rutesta = 1 where rutcod = {$id}";
    $db->query($upd);
}

$sq = "delete from rutadet where rutcod = {$id}";
//        echo $sq;
if($db->query($sq)){
    foreach($row as $k => $v){
        $sq_ins = " insert into rutadet set 
                 rutcod = {$id},
                cod_alma = '{$v[6]}',     
                ubiestan = '{$v[0]}',
                rutip = '{$v[7]}',
                ubisent = '{$v[8]}',
                ubiestanh = '{$v[3]}',
                ubihuec = {$v[1]},
                ubihuech = {$v[4]},
                ubiniv = {$v[2]},
                ubinivh = {$v[5]}";
//                echo $sq_ins;
        if($db->query($sq_ins)){
            $lerr[] = 0;
            include "ordenRutas.php";
        }else{
            $lerr[] = 1;
        }
        
    }
    
}
if(in_array(1, $lerr)){
    $msg = 'Error al guardar ruta';
    $err = 1;
}else{
    $msg = 'Ruta guardada';
    $err = 0;
}
echo json_encode(array('msg' => $msg, 'err' => $err));
exit();
