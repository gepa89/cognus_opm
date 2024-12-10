<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$row = $_POST['data'];



    foreach($row as $k => $v){
        $sq = "select * from artinve where artrefer = '".trim(mb_strtoupper($v[0]))."' ";
//                echo $sq;

        $rs = $db->query($sq);
        if($rs->num_rows > 0){
            $sq_ins = " update artinve set
                artminve = '{$v[1]}'
                 where artrefer = '".trim(mb_strtoupper($v[0]))."'"
                ;
//                echo $sq_ins;
            if($db->query($sq_ins)){
                $lerr[] = 0;
            }else{
                $lerr[] = 1;
//                echo $sq_ins;
            }
        }else{
            $sq_ins = " insert into artinve set
                artminve = '{$v[1]}',
                artrefer = '".trim(mb_strtoupper($v[0]))."'"
                ;
//                echo $sq_ins;
            if($db->query($sq_ins)){
                $lerr[] = 0;
            }else{
                $lerr[] = 1;
//                echo $sq_ins;
            }
        }
    }
if(in_array(1, $lerr)){
    $msg = 'Error al guardar Configuración';
    $err = 1;
}else{
    $msg = 'Configuración guardada';
    $err = 0;
}
echo json_encode(array('msg' => $msg, 'err' => $err));
exit();
