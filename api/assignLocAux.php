<?php require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$material = $_POST['mat'];
$mexp = ($_POST['ubi']);


if(isset($material)){
    
//    reviso si pallet esta vacio
    $sqhan = "select artrefer
                from artean
                where artrefer like  '%{$material}' limit 1";
//                echo $sqhan;
    $rst = $db->query($sqhan);
    $rw = $rst->fetch_assoc();
    
    if($rw['artrefer'] != ''){        
        $sq = "insert  mateubi set material = '{$rw['artrefer']}', ubicacion = '{$mexp}'";
    //            echo $sq;
        if($db->query($sq)){        

            $response["error"] = FALSE;
            $response['mensaje'] = "Guardado";
        }else{
            $response["error"] = TRUE;
            $response['mensaje'] = "Error al guardar.";
        }
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Material sin EAN.";
    }
    
    echo json_encode($response);
}

