<?php require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
header('Content-Type: application/json; charset=utf-8');

$terminal = $_POST['term'];

$xq = "select distinct multiref from ped_multiref where codst = 0 and terminal = '{$terminal}'";
$rs = $db->query($xq);
$response = array();
$response['total'] = 0;
while ($ax = $rs->fetch_assoc()) {
    $response['multirefs'][] = trim($ax['multiref']);
    $response['total']++;

}
$pedido = obtenerPedido($db, $terminal);
$muelle_entrega = obtenerMuelleEntrega($db, $pedido);
if ($muelle_entrega != null) {
    $response["error"] = false;
    $response['muelles'] = [$muelle_entrega];
    echo json_encode($response);
    exit;
}
//todas las ubicaciones de muelle disponibles
$xq = "select distinct ubirefer from ubimapa where ubitipo = 'ME'";
$rs = $db->query($xq);
while ($ax = $rs->fetch_assoc()) {
    $response['muelles'][] = $ax['ubirefer'];
}
if (count($response['multirefs']) > 0) {
    $response["error"] = FALSE;
    $response['mensaje'] = "";
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "No se encuentran MR sin ubicación en Muelle";
}

echo json_encode($response);


function obtenerMuelleEntrega($db, $pedido)
{
    $sql = "SELECT * FROM mref_exp WHERE pedido='$pedido'";
    $query = $db->query($sql);
    if (!$query) {
        print_r($db->error);
        exit;
    }
    if ($query->num_rows == 0) {
        return null;
    }
    return $query->fetch_assoc()['ubirefer'];

}

function obtenerPedido($db, $terminal)
{
    $sql = "SELECT * FROM ped_multiref WHERE terminal = '$terminal' AND codst=0";
    $query = $db->query($sql);
    if (!$query) {
        print_r($db->error);
        exit;
    }
    if ($query->num_rows == 0) {
        return null;
    }
    return $query->fetch_assoc()['pedido'];

}