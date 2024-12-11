<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);

$response = array();
$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";
//     echo $sq;
$cc = 0;
$r1 = $db->query($sq);

if ($r1->num_rows > 0) {
    $tiene_pedido_por_cajas = $r1->fetch_object()->pedcajas == 1;
    $response["error"] = FALSE;
    $response["tiene_pedido_por_caja"] = $tiene_pedido_por_cajas;
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "Sin pedidos asignados.";
}
echo json_encode($response);
include '/var/www/html/closeconn.php';