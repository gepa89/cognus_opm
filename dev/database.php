<?php
require_once("../database/database.php");
$query = new MySqlQuery();
$ar = "LA002029";

$actualizar_articulo = new MySqlQuery();
$datos = ["artdesc" => "prueb"];
$actualizar = $actualizar_articulo->table('arti')->where("artrefer = ?", [$ar])->executeUpdate($datos);
print_r($actualizar);
