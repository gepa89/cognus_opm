<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$read = $_POST['read'];
$flag = $_POST['flag'];


if($flag == 0){
    $sqhan = "select artrefer
                from artean
                where ean like  '%{$read}' limit 1";
//                echo $sqhan;
    $rst = $db->query($sqhan);
    $rw = $rst->fetch_assoc();
    if($rw['artrefer'] != ''){        
        $response["error"] = FALSE;
        $response['mensaje'] = "";
        $response['viaje'] = "mat";
        $response['material'] = "".$rw['artrefer'];
        $response['flag'] = "1";
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Material no existe.";
    }
}else{
    $sqhan = "select ubirefer
                from ubimapa
                where ubirefer like  '%{$read}' and ubitipo in ('RE','PI') limit 1";
//                echo $sqhan;
    $rst = $db->query($sqhan);
    $rw = $rst->fetch_assoc();
    if($rw['ubirefer'] != ''){        
        $response["error"] = FALSE;
        $response['mensaje'] = "";
        $response['viaje'] = "ubi";
        $response['ubicacion'] = "".$rw['ubirefer'];
        $response['flag'] = "1";
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Ubicacion no existe.";
    }
}
    
         echo json_encode($response);
 include '/var/www/html/closeconn.php';
