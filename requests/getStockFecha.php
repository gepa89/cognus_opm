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
    $filtros = $filtros . " AND artrefer LIKE '%$articulo%'";
}
$ubicacion = htmlspecialchars(trim($_GET['ubicaciones']));
if (!empty($ubicacion)) {
    $filtros = $filtros . " AND ubirefer LIKE '%$ubicacion%'";
}

$sql = "SELECT
    artrefer,
    artdesc,
    totalubi,
    CONCAT(SUBSTRING(ubirefer, 1, 4), '-', SUBSTRING(ubirefer, 5, 3), '-', SUBSTRING(ubirefer, 8)) AS ubirefer,
    ubitipo,
    cod_alma,
    MIN(fecha) AS fecha, 
    MIN(hora) AS hora    
FROM
    stock_diario
WHERE
    cod_alma = '{$codalma}' $filtros
GROUP BY
    ubirefer,
    artrefer,
    artdesc,
    ubitipo,
    cod_alma";

try {
    $sql_contar = "SELECT COUNT(*) AS cantidad FROM ({$sql}) as e";
    $cantidad = $db->query($sql_contar)->fetch_assoc()['cantidad'];

    $sql = $sql . " LIMIT $limite offset $offset";
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($resultados); $i++) {
        $resultados[$i]['totalubi'] = formatear_numero($resultados[$i]['totalubi']);
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