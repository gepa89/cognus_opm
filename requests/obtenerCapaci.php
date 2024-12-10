<?php

require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = MysqlDB::obtenerInstancia();
$limite = $_GET['length'] ?? 10;
$offset = $_GET['start'] ?? 0;
$buscar = $_GET['buscar'] ?? null;
$limite = $db->real_escape_string($limite);
$offset = $db->real_escape_string($offset);
$query = null;
$resultados = array();
$filtro_buscar = "";

if ($buscar) {
    $buscar = $db->real_escape_string($buscar);
    $filtro_buscar = " AND dimension like '%$buscar%' OR artrefer like '%$buscar%'";
}
try {

    $sql = "SELECT
	capaubi.dimension AS dimension,
	capaubi.artrefer AS artrefer,
	arti.artdesc AS artdesc,
	capaubi.capacidad AS capacidad,
	capaubi.codalma AS codalma 
        FROM
	capaubi
	INNER JOIN arti ON capaubi.artrefer = arti.artrefer
        WHERE TRUE {$filtro_buscar} order by dimension ASC LIMIT $limite offset $offset";
    $query = $db->query($sql);
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        retorna_resultado(500, array("mensaje" => "ocurrio un error al obtener eanes"));
    }
    $resultados['data'] = $query->fetch_all(MYSQLI_ASSOC);


    $sql = "SELECT COUNT(*) as cantidad FROM capaubi WHERE TRUE {$filtro_buscar} order by dimension ASC";
    $query = $db->query($sql);
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        retorna_resultado(500, array("mensaje" => "ocurrio un error al obtener eanes"));
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