<?php

require('../conect.php');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$pedido = $_POST['pedido'];

$sql = "UPDATE pedexcab set entregado=1 where pedexentre = '$pedido'";
$db->query($sql);
echo json_encode(array("guardado" => true));
exit();