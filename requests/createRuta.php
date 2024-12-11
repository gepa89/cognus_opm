<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$row = $_POST['data'];
$det = $_POST['det'];

$sel_r = "select rutcod from ruta order by rutcod desc limit 1";
$rs = $db->query($sel_r);
$ax = $rs->fetch_assoc();
$rut = 1;
if(is_null($ax)){    
    $desc = 'Ruta '.$rut;
}else{
    $rut = (int)$ax['rutcod']+1;
    $desc = 'Ruta '.$rut;
}
   foreach($row as $k2 => $v2){
    $alma = $v2[6];
    }

$detx = 0;
if($det == 'true'){
    $detx = 1;
    $upd = "update ruta set rutesta = 0 where cod_alma = '{$alma}' ";
    $db->query($upd);
}
 
$sq = "insert into ruta set 
        rutcod = {$rut},
        rutdes = '{$desc}',
        rutesta = {$detx}, 
        cod_alma = '{$alma}'
        ";
//        echo $sq;
if($db->query($sq)){
    foreach($row as $k => $v){
        $sq_ins = " insert into rutadet set 
                rutcod = {$rut},
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
    $msg = 'Ruta creada';
    $err = 0;
}
echo json_encode(array('msg' => $msg, 'err' => $err));
exit();
