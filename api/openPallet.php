<?php require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$pallet = $_POST['pallet'];
$terminal = strtoupper($_POST['terminal']);
$pedido = strtoupper($_POST['pedido']);
$palletAct = ($_POST['palletAct']);



if(isset($pedido)){
    
//    reviso si pallet esta vacio
    $sqs = "select * from pallets a inner join pallet_mat b on a.pal_pedido = b.ap_pedido and a.pal_desc = b.ap_pall where pal_pedido = '{$pedido}' and pal_desc= {$pallet} limit 1";
    $r1 = $db->query($sqs);
    if($r1->num_rows > 0){
        $datPal = $r1->fetch_assoc();
        $sq = "update pallets set 
            pal_status = 1
            where 
            pal_pedido = '{$pedido}'
        ";
        $sqx = "update pallets set 
            pal_auto = 1
            where 
            pal_pedido = '{$pedido}' and pal_desc = {$palletAct}
        ";
        $db->query($sqx);
    //            echo $sq;
        if($db->query($sq)){        
            $sq2 = "update pallets set 
                pal_status = 0
                where 
                pal_pedido = '{$pedido}'
                and pal_desc= {$pallet}
            ";
            if($db->query($sq2)){        
                    
                $response["mref"] = $datPal['etnum'];
                $response["error"] = FALSE;
                $response['mensaje'] = "Pallet ".$pallet." abierto correctamente.";
            }else{
                $response["error"] = TRUE;
                $response['mensaje'] = "Error al abrir Pallet.";
            }
        }else{
            $response["error"] = TRUE;
            $response['mensaje'] = "Error al actualizar Pallets.";
        }
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Pallet no existe";
    }
    
    echo json_encode($response);
}

