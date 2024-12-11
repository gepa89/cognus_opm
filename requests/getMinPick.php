<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$mat = $_POST['mat'];

if($mat != ''){
    $mats = explode(',', $mat);
    //var_dump($mats);

    if(is_array($mats) && count($mats) > 0){
        $mates = " and artrefer in (";
        $matesx = '';
    //    echo "aqui entro";
        foreach($mats as $k => $val){
    //        echo "aqui entro";
            if(is_numeric($val)){
                $val = str_pad($val, 18, "0", STR_PAD_LEFT);
            }else{
                $val = $val;
            }
    //        var_dump($val);
            if($matesx == ''){
                $matesx .= "'".$val."'";
            }else{
                $matesx .= ",'".$val."'";
            }
            $mates .= $matesx;
        }
        $mates .= ")";
    }
}else{
    $mates = '';
}
$sql = "select * from artinve where 1=1 {$mates}";
//echo $sql;
$rs = $db->query($sql);
$calm = 0;
if($rs->num_rows > 0){    
    while($rx = $rs->fetch_assoc()){
//        almacenajes
        
//                    colHeaders: ['Material', 'Mínimo', 'Máximo','Centro', 'Almacen'],
        $responde['almacenaje'][$calm][0] = $rx['artrefer'];
        $responde['almacenaje'][$calm][1] = $rx['artminve'];
        $responde['almacenaje'][$calm][2] = $rx['cencod'];
        $responde['almacenaje'][$calm][3] = $rx['almcod'];
        $calm++;
    }    
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Material(es) no encontrado(s)';
}

echo json_encode(array( 'dat' => $responde, 'err' => $err,'msg' => $msg));

exit();
