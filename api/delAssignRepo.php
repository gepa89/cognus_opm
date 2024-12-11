<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$pedido = strtoupper($_POST['pedido']);
$terminal = strtoupper($_POST['terminal']);

if($pedido != ''){
    $sq = "delete from assig_ped where tercod = '{$terminal}' and pedido = '{$pedido}'";
    if($db->query($sq)){
        $response["error"] = FALSE;
        $response['mensaje'] = "Asignación Anulada";
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo anular asignación.";
    }
}else{
    $response["error"] = TRUE;
    $response['mensaje'] = "No hay pedido asignado a esta terminal.";
}
    
         echo json_encode($response);
 include '/var/www/html/closeconn.php';
