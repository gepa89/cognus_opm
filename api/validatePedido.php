<?php require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);
$pedido = strtoupper($_POST['pedido']);


if(isset($pedido)){
    
//    reviso si pallet esta vacio
    $sq = "select distinct a.artrefer,  a.canpedi, a.canpendi, a.canprepa, b.artser 
            FROM pedredet a 
            left join arti b on a.artrefer = b.artrefer  
            left join artean c on a.artrefer = c.artrefer 
            where pedrefer = '{$pedido}' and a.tienekit <> 'SI' and a.canprepa < a.canpedi";
    $rs = $db->query($sq);
    $flg = 0;
    if($rs->num_rows == 0){        
        $response["error"] = FALSE;
        $response['mensaje'] = "Se puede cerrar el pedido";
    }else if($flg == 0){
        $response["error"] = TRUE;
        $response['mensaje'] = "Falta ".$rs->num_rows." material(es) pendiente(s) de control.";
    }
    
    echo json_encode($response);
}

