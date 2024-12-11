<?php
require('../conect.php');
require_once(__DIR__ . "/../vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$codigo_pedido = $_GET['pedido'];
$codalma = $_GET['codalma'];

$filtros = "";
if (!empty($codigo_pedido)) {
    $filtros = $filtros . " AND a.artrefer LIKE '%$codigo_pedido%' ";
}

$sql = "SELECT 
a.artrefer,
b.artdesc,
a.candispo,
a.canmure,
a.canpedven,
a.cantransfe,
a.cod_alma 
FROM stockart a inner join arti b on a.artrefer = b.artrefer 
where a.cod_alma = '{$codalma}' {$filtros} ";
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setCellValue('A1', 'Codigo');
$worksheet->setCellValue('B1', 'Descripcion');
$worksheet->setCellValue('C1', 'Libre');
$worksheet->setCellValue('D1', 'Muelle');
$worksheet->setCellValue('E1', 'Ventas');
$worksheet->setCellValue('F1', 'Transf.');
$worksheet->setCellValue('G1', 'Almacen');

try {
    $resultados = $db->query($sql);
    $row = 2;
    while ($fila = $resultados->fetch_assoc()) {
        $worksheet->setCellValue('A' . $row, $fila['artrefer']);
        $worksheet->setCellValue('B' . $row, $fila['artdesc']);
        $worksheet->setCellValue('C' . $row, $fila['candispo']);
        $worksheet->setCellValue('D' . $row, $fila['ubitipo']);
        $worksheet->setCellValue('E' . $row, $fila['canmure']);
        $worksheet->setCellValue('F' . $row, $fila['canpedven']);
        $worksheet->setCellValue('G' . $row, $fila['cantransfe']);
        $row++;
    }
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();
$filename = 'reporte_stock_articulo_' . date('Y-m-d_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officeDocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();