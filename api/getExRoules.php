<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);

$response = array();
//<option value="">Seleccionar</option>
//<option value="B1">EAN</option>
//<option value="B2">Ubicación</option>
//<option value="B3">EAN / Ubicación</option>
$sqr = "select lectura from config";
$rr = $db->query($sqr);
$rrx = $rr->fetch_assoc();
if($rrx['lectura']){
    $response["regla"] = $rrx['lectura'];
    $response["error"] = FALSE;
}else{
   $response["error"] = TRUE;
   $response['mensaje'] = "Sin pedidos asignados.";
}
    
echo json_encode($response);
