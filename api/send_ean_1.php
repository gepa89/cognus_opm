<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
if(isset($_POST['ean']) && isset($_POST['material'])){
    $materials = preg_replace('/\s/', '', $_POST['material']);
    $materials = preg_replace('/\t/', '', $materials);   
    $materials = strtoupper($materials);
    $ean = str_pad($_POST['ean'], 18, "0", STR_PAD_LEFT);
    $unimed = 'ST';
    if($_POST['unimed'] !=''){
        $unim = strtoupper($_POST['unimed']);
        
        $sq = "select presereftec from presen where preseref = '{$unim}' limit 1";
//        echo $sq;
        $rs = $db->query($sq);
        $ax = $rs->fetch_assoc();
        $unimed = $ax['presereftec'];
    }
    
    $response = array();
    
    $sqhan = "select artrefer
                from arti
                where artrefer like  '%{$materials}' limit 1";
//                echo $sqhan;
    $rst = $db->query($sqhan);
    $rw = $rst->fetch_assoc();
    if($rw['artrefer'] != ''){
            $sq = "insert into artean set
                    ean = '{$ean}',
                    artrefer  = '{$materials}',
                    fecaut = now()
                    ";
        if($db->query($sq)){
            
            $response['error'] = false;
            $response['mensaje'] = "Ean registrado correctamente.";
        }else{
            $response['error'] = true;
            $response['mensaje'] = 'No se pudo registrar material.';
        }
    }else{
        $response['error'] = true;
        $response['mensaje'] = 'No se pudo registrar material. Material no existe.';
    }
    echo json_encode($response);        
}
