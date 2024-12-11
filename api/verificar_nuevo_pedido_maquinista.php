<?php

require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../utils/conversores.php");
require_once(__DIR__ . "/../utils/sanitizador.php");

$conn = MysqlDB::obtenerInstancia();

$terminal = sanitizar($_GET['terminal']);

$sql = "SELECT * FROM assig_ped WHERE tercod = ? and st=0";
$sqlBuilder = new MySqlQuery($conn);
$asignacion = $sqlBuilder->rawQuery($sql, [$terminal])->getOne();
$respuesta = ['existe_pedido_asignado' => $asignacion != null];
$conn->close();
retorna_resultado(200, $respuesta);
