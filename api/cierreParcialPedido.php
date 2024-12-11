<?php require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);
$pedido = strtoupper($_POST['pedido']);


if(isset($pedido)){
    
//    reviso si pallet esta vacio
    $sq = "update pedrecab set
            pedresitu = 'PP',
            pedrecie = now(),
            pedrehorcie = now()
            where pedrefer = '{$pedido}'";
    $flg = 0;
    if($db->query($sq)){        
        $response["error"] = FALSE;
        $response['mensaje'] = "Pedido cerrado parcialmente";
    }else if($flg == 0){
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo actualizar pedido.";
    }
    
    echo json_encode($response);
}

