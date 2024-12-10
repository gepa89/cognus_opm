<?php

require('../conect.php');
require_once(__DIR__ . "/../vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db = new mysqli($SERVER, $USER, $PASS, $DB);


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

$condicional = "
    ";
try {
    $spreadsheet = new Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->setCellValue('A1', 'Ubicacion');
    $worksheet->setCellValue('B1', 'Z. Preparacion');
    $worksheet->setCellValue('C1', 'Z. Almacenaje');
    $worksheet->setCellValue('D1', 'Tipo');
    $worksheet->setCellValue('E1', 'Estado');
    $worksheet->setCellValue('F1', 'Situacion');

    $sql = "SELECT COUNT(*) as cantidad  
        FROM ubimapa 
        WHERE 
        cod_alma = '{$codalma}' 
        {$filtros}";

    $cantidad = $db->query($sql)->fetch_object()->cantidad;
    $particion = 10000;
    $numero_iteraciones = (int) ($cantidad / $particion) + 1;
    $offset = 0;
    $row = 2;
    for ($i = 0; $i < $numero_iteraciones; $i++) {
        $sql = "SELECT ubirefer,zoncodpre,zoncodalm,ubitipo,ubiestad,ubisitu 
            FROM ubimapa 
        WHERE 
        cod_alma = '{$codalma}'
        {$filtros} limit $particion offset $offset";
        $resultados = $db->query($sql);
        while ($fila = $resultados->fetch_assoc()) {
            $worksheet->setCellValue('A' . $row, $fila['ubirefer']);
            $worksheet->setCellValue('B' . $row, $fila['zoncodpre']);
            $worksheet->setCellValue('C' . $row, $fila['zoncodalm']);
            $worksheet->setCellValue('D' . $row, $fila['ubitipo']);
            $worksheet->setCellValue('E' . $row, $fila['ubiestad']);
            $worksheet->setCellValue('F' . $row, $fila['ubisitu']);
            $row++;
        }
        $offset = ($i + 1) * $particion;
    }



} catch (\Throwable $th) {
    print_r($th->getMessage());
    exit;
}

$db->close();
$filename = 'reporte_ubicaciones_' . date('Y-m-d_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officeDocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();