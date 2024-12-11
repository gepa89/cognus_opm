<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
function obtenerPresentacion($material, $tipo_presentacion)
{
    $sql = "SELECT * from artipresen WHERE artrefer = '$material' and preseref='$tipo_presentacion'";
    $sqlBuilder = new MySqlQuery();
    $presentacion = $sqlBuilder->rawQuery($sql, [])->getOne();
    print_r($presentacion);
    return $presentacion;
}
print_r("<pre>");
$pedido = $_GET['pedido'];

$sql = "SELECT * from pedexdet  
        left join artipresen on pedexdet.artrefer = artipresen.artrefer
        where pedexentre = '$pedido' and artipresen.preseref = 'CJ'";
$db = MysqlDB::obtenerInstancia();
$sqlBuilder = new MySqlQuery($db);
$datos = $sqlBuilder->rawQuery($sql, [])->getAll();
$mostrar_boton = false;
foreach ($datos as $dato) {
    $material = $dato['artrefer'];
    $tipo_presentacion = $dato['preseref'];
    $cantidad_presentacion = $dato['canpresen'];
    $cantidad_restante = $dato['canpedi'] - $dato['canprepa'];
    if ($cantidad_restante % $cantidad_presentacion != 0) {
        $mostrar_boton = true;
        break;
    }
}
print_r($mostrar_boton);
print_r("</pre>");
