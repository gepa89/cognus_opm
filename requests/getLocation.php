<?php

require('../conect.php');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
/*print_r("<pre>");
print_r($_GET);
print_r("</pre>");*/

$codalma = $_GET['codalma'];
$estDesde = $_GET['estDesde'];
$estHasta = $_GET['estHasta'];
$huecDesde = $_GET['huecDesde'];
$huecHasta = $_GET['huecHasta'];
$nivDesde = $_GET['nivDesde'];
$nivHasta = $_GET['nivHasta'];

$limite = $_GET['length'] ? $_GET['length'] : 10;
$offset = $_GET['start'] ? $_GET['start'] : 0;

$filtros = "";
if ($_GET['estDesde'] != "") {
    $estante_desde = str_pad(strtoupper($_GET['estDesde']), 4, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubiestan >= '$estante_desde'";
}
if ($_GET['estHasta'] != "") {
    $estante_hasta = str_pad(strtoupper($_GET['estHasta']), 4, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubiestan <= '$estante_hasta'";
}

if ($_GET['huecDesde'] != "") {
    $hueco_desde = str_pad(strtoupper($_GET['huecDesde']), 3, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubihuec >= '$hueco_desde'";
}
if ($_GET['huecHasta'] != "") {
    $hueco_hasta = str_pad(strtoupper($_GET['huecHasta']), 3, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubihuec <= '$hueco_hasta'";
}

if ($_GET['nivDesde'] != "") {
    $hueco_desde = str_pad(strtoupper($_GET['nivDesde']), 4, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubiniv >= '$hueco_desde'";
}
if ($_GET['nivHasta'] != "") {
    $hueco_hasta = str_pad(strtoupper($_GET['nivHasta']), 4, "0", STR_PAD_LEFT);
    $filtros = $filtros . " AND ubiniv <= '$hueco_hasta'";
}

if ($_GET['buscar'] != "") {
    $buscar = trim(htmlspecialchars($_GET['buscar']));
    $filtros = $filtros . " AND (ubirefer LIKE '%$buscar%' OR ubitipo = '$buscar')";
}

$condicional = "FROM ubimapa 
WHERE 
    cod_alma = '{$codalma}'
    {$filtros} 
    ";
try {
    $sql = "SELECT ubirefer,zoncodpre,zoncodalm,ubitipo,ubiestad,ubisitu,tipoubi,dimension
    {$condicional} 
    LIMIT $limite OFFSET $offset";
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    $sql = "SELECT COUNT(*) AS cantidad {$condicional}";
    $cantidad = $db->query($sql)->fetch_assoc()['cantidad'];
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();
$resultados = array(
    'data' => $resultados,
    'recordsFiltered' => (int) $cantidad,
    'recordsTotal' => (int) $cantidad
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($resultados);
exit();