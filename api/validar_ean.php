<?php
require('../conect.php');
header('Content-type: application/json; charset=utf-8');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$ean = htmlspecialchars($_GET['ean'], ENT_QUOTES, 'UTF-8');
$material = htmlspecialchars($_GET['material'], ENT_QUOTES, 'UTF-8');

$sql = "SELECT * FROM artean WHERE ean = '$ean' AND artrefer='$material'";
$res = $db->query($sql);

if (!$res) {
    $error = $db->error;
    $db->close();
    echo json_encode(['mensaje' => "Error al ejecutar $error"], 500);
    exit;
}
$db->close();
echo json_encode(['existe_ean' => $res->num_rows > 0], 200);
