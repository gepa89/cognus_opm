<?php
require('../conect.php');
header('Content-type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$version = htmlspecialchars(trim($_POST['version_apk']));
$sql = "SELECT version FROM version_apk WHERE version>$version";
$res = $db->query($sql)->fetch_object();
$version = $res->version;
$respuesta = [
    "actualizar" => $res != null,
    "url" => "http://192.168.136.31/wmsd/versions/version-$version.apk"
];
$db->close();
echo json_encode($respuesta);
exit;
?>