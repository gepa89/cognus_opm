<?php
require('../conect.php');
require_once(__DIR__ . "/../vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$codalma = $_GET['codalma'];

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
a.ubirefer,
a.cod_alma, 
c.ubitipo, 
sum(a.canti) as total 
from stockubi a 
inner join arti b on a.artrefer = b.artrefer 
inner join ubimapa c on c.ubirefer = a.ubirefer and c.cod_alma = a.cod_alma  
where a.cod_alma = '{$codalma}' {$filtros} group by a.ubirefer, a.artrefer,a.cod_alma";
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setCellValue('A1', 'Articulo');
$worksheet->setCellValue('B1', 'Descripcion');
$worksheet->setCellValue('C1', 'Ubicacion');
$worksheet->setCellValue('D1', 'Tipo');
$worksheet->setCellValue('E1', 'Cantidad');
try {
    $resultados = $db->query($sql);
    $row = 2;
    while ($fila = $resultados->fetch_assoc()) {
        $worksheet->setCellValue('A' . $row, $fila['artrefer']);
        $worksheet->setCellValue('B' . $row, $fila['artdesc']);
        $worksheet->setCellValue('C' . $row, $fila['ubirefer']);
        $worksheet->setCellValue('D' . $row, $fila['ubitipo']);
        $worksheet->setCellValue('E' . $row, $fila['total']);
        $row++;
    }
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();
$filename = 'reporte_stock_ubicacion_' . date('Y-m-d_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officeDocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();