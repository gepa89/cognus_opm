<?php

require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = MysqlDB::obtenerInstancia();
$limite = $_GET['length'] ?? 18;
$offset = $_GET['start'] ?? 0;
$buscar = $_GET['buscar'] ?? null;

$filtro_buscar = "";

if ($buscar) {
    $buscar = $db->real_escape_string($buscar);
    $filtro_buscar = " AND artrefer LIKE '%$buscar%' OR artdesc LIKE '%$buscar%'";
}
$query = null;
$resultados = array();
try {
    $sql = "SELECT artrefer, artdesc,artean,artmarkit, unimed, artser, artgrup, clirefer, artlotemar, costo, almcod 
    FROM arti  WHERE true $filtro_buscar order by artrefer ASC LIMIT $limite offset $offset";
    $query = $db->query($sql);
    //print $sql;
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        retorna_resultado(500, array("mensaje" => "ocurrio un error al obtener Articulos"));
    }
    $resultados['data'] = $query->fetch_all(MYSQLI_ASSOC);


    $sql = "SELECT COUNT(*) as cantidad FROM arti WHERE true $filtro_buscar order by artrefer ASC";
    $query = $db->query($sql);
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        retorna_resultado(500, array("mensaje" => "ocurrio un error al obtener Articulos"));
    }
    $resultados['recordsFiltered'] = (int) $query->fetch_assoc()['cantidad'];
    $resultados['recordsTotal'] = $resultados['recordsFiltered'];

} catch (\Throwable $th) {
    $db->close();
    guardar_error_log(__FILE__, $th->getMessage());
    retorna_resultado(500, array("mensaje" => $th->getMessage()));
}
$db->close();
retorna_resultado(200, $resultados);
?>