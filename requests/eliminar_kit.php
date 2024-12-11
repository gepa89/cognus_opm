<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
require_once("../conect.php");
require_once("../utils/respuesta.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$id = htmlspecialchars($_POST['id']);

$sql = "DELETE FROM artkit WHERE poskit = ? ";
$stmt = $db->prepare($sql);
if (!$stmt) {
    echo $db->error;
}
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $db->close();
    retorna_resultado(["error" => "error"], 500);
}
$db->commit();
$db->close();
header('Content-type: application/json; charset=utf-8', true, 200);
echo json_encode(['mensaje'=>"eliminado con exito"]);