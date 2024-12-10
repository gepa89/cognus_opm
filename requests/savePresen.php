<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante

$flag = $_POST['flag'];
if($flag == 1){
    $row[] = $_POST['data'];
}else{
    $row = $_POST['data'];
}

//var_dump($row);
//$db->autocommit(false);
$msgx = '';
    foreach($row as $k => $v){        
        if($v[0] != ''){
            $material = strtoupper(trim($v[0]));   
            $cant = $v[1];
            $pres = $v[2];
            $alm = $v[3];            
            $sq = "insert into artipresen set artrefer = '{$material}', canpresen = {$cant}, preseref = '{$pres}', cod_alma = '{$alm}'";
//            echo $sq;
            if(!$db->query($sq)){
                $sq2 = "update artipresen set canpresen = {$cant}, preseref = '{$pres}' where artrefer = '{$material}' and cod_alma = '{$alm}' ";
                if(!$db->query($sq2)){
                    $msg = 'Error al guardar Configuración.';
                    $fgil = $k+1;
//                    var_dump($sq);
                    $msgx .= "Fila ".$fgil.':  \n'.$sq;
                    $lerr[] = 1;
                    continue;   
                }
            }
        }
    }
    
    if(in_array(1, $lerr)){
        $msg = 'Error al guardar Configuración.\n'.$msgx;        
        $err = 1;
    }else{
        $db->commit();
        $msg = 'Configuración guardada';
        $err = 0;
    }
    

    echo json_encode(array('msg' => $msg, 'err' => $err));
    exit();
