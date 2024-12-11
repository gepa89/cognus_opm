<?php

require('../conect.php');
require_once("../utils/conversores.php");

//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$codalma = $_GET['codalma'];
$limite = $_GET['length'] ? $_GET['length'] : 10;
$offset = $_GET['start'] ? $_GET['start'] : 0;

$articulo = htmlspecialchars(trim($_GET['articulo']));
if (!empty($articulo)) {
    $filtros = $filtros . " AND a.artrefer LIKE '%$articulo%'";
}
$ubicacion = htmlspecialchars(trim($_GET['ubicaciones']));
if (!empty($ubicacion)) {
    $filtros = $filtros . " AND a.ubirefer LIKE '%$ubicacion%'";
}

$sql = "SELECT b.artrefer, 
b.artdesc, 
CONCAT(SUBSTRING(a.ubirefer, 1, 4), '-', SUBSTRING(a.ubirefer, 5, 3), '-', SUBSTRING(a.ubirefer, 8)) AS ubirefer,
a.cod_alma, 
c.ubitipo, 
c.ubisitu,
sum(a.canti) as total 
from stockubi a 
inner join arti b on a.artrefer = b.artrefer 
inner join ubimapa c on c.ubirefer = a.ubirefer and c.cod_alma = a.cod_alma  
where a.cod_alma = '{$codalma}' {$filtros} group by a.ubirefer, a.artrefer,a.cod_alma ";

try {
    $sql_contar = "SELECT COUNT(*) AS cantidad FROM ({$sql}) as e";
    $cantidad = $db->query($sql_contar)->fetch_assoc()['cantidad'];

    $sql = $sql . "LIMIT $limite offset $offset";
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($resultados); $i++) {
        $resultados[$i]['total'] = formatear_numero($resultados[$i]['total']);
    }
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();

$respuesta = array(
    'data' => $resultados,
    'recordsFiltered' => (int) $cantidad,
    'recordsTotal' => (int) $cantidad
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit();
