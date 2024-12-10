<?php
require('../conect.php');
header('Content-type: application/json; charset=utf-8');

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);

$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$response = array();
$response['data'][0]['asignados'] = 0;
$response['data'][0]['pendientes'] = 0;
$sq = "SELECT DISTINCT
    assig_ped.pedido,
    assig_ped.st,
    assig_ped.ex_st,
    termi.terzonpre,
    (
        SELECT COUNT(artrefer) AS tsku
        FROM pedubidet b
        WHERE b.pedubicod = assig_ped.pedido
    ) AS tsku,
    (
        SELECT pedclase AS pclase
        FROM pedubicab c
        WHERE c.pedubicod = assig_ped.pedido
    ) AS clase
    FROM
    assig_ped
    INNER JOIN pedubidet ON pedubidet.pedubicod = assig_ped.pedido
    INNER JOIN stockubi ON stockubi.artrefer = pedubidet.artrefer
    INNER JOIN ubimapa ON ubimapa.ubirefer = stockubi.ubirefer
    INNER JOIN termi ON termi.tercod = assig_ped.tercod
    WHERE
    assig_ped.tercod = '$terminal'
    AND assig_ped.st = 0
    AND assig_ped.ex_st = 0;";
$cc = 0;
$r1 = $db->query($sq);

while ($row = $r1->fetch_assoc()) {
    $response['pedidos'][] = $row;
}

$sq = "SELECT DISTINCT
        a.pedido,
        a.st,
        a.ex_st,
        d.zoncodpre,
        f.multiref,
        (SELECT COUNT(artrefer) AS tsku FROM pedubidet b WHERE b.pedubicod = a.pedido) AS tsku,
        (SELECT pedclase AS pclase FROM pedubicab c WHERE c.pedubicod = a.pedido) AS clase
    FROM
        assig_ped a
        INNER JOIN pedubidet b ON b.pedubicod = a.pedido
        INNER JOIN stockubi c ON c.artrefer = b.artrefer
        INNER JOIN ubimapa d ON d.ubirefer = c.ubirefer
        INNER JOIN termi e ON e.tercod = a.tercod
        LEFT JOIN ped_multiref f ON f.pedido = a.pedido
    WHERE
        a.tercod = '$terminal' AND
        a.st = 0;";
//     echo $sq;
$cc = 0;
$r1 = $db->query($sq);

while ($row = $r1->fetch_assoc()) {

    if ($row['multiref'] == '') {
        $response['data'][0]['pendientes']++;
    } else {
        $response['data'][0]['asignados']++;
    }
}
$response['data'][0]['pendientes'] = '' . $response['data'][0]['pendientes'];
$response['data'][0]['asignados'] = '' . $response['data'][0]['asignados'];
if (isset($response['pedidos'])) {
    $response["error"] = FALSE;
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "Sin pedidos pendientes de relacion MR.";
}

echo json_encode($response);
include '/var/www/html/closeconn.php';