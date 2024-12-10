<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");

$conn = MysqlDB::obtenerInstancia();

$pedido = htmlspecialchars($_GET['pedido'] ?? "", ENT_QUOTES, 'UTF-8');

$sqlBuilder = new MySqlQuery($conn);
$sql = "SELECT 
        contdet.posicion,
        contdet.material,
        contdet.matdesc,
        contdet.unimed,
        contdet.canped,
        contdet.precuni,
        contdet.prectotal,
        contdet.cencod,
        contdet.codalma,
        contdet.artgrup

        FROM contdet  
        WHERE contdet.idcontra = '$pedido' 
        ORDER BY posicion ASC";

//print $sql;
$datos = $sqlBuilder->rawQuery($sql)->getAll();
foreach ($datos as &$dato) {
    $dato['canped'] = formatear_numero($dato['canped']);
    $dato['precuni'] = formatear_numero($dato['precuni']);
    $dato['prectotal'] = formatear_numero($dato['prectotal']);
 
}
$respuesta = ['detalles' => $datos];
$conn->close();

retorna_resultado(200, $respuesta);
