<?php
ini_set('memory_limit', '2G'); // Adjust the value as needed
require('../conect.php');
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../helper/productividad_dev.php");
require_once(__DIR__ . "/../logger/logger.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db = new mysqli($SERVER, $USER, $PASS, $DB);

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$fecha_inicio = $fecha_inicio . " 00:00:00";
$fecha_fin = $fecha_fin . " 23:59:59";

$indice = 2;

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setCellValue("A" . $indice, 'Fecha Desde');
$worksheet->setCellValue("B" . $indice, $fecha_inicio);
$indice += 1;
$worksheet->setCellValue("A" . $indice, 'Fecha Hasta');
$worksheet->setCellValue("B" . $indice, $fecha_fin);
$indice += 2;
$worksheet->setCellValue("A" . $indice, 'Pedido');
$worksheet->setCellValue("B" . $indice, 'Articulo');
$worksheet->setCellValue("C" . $indice, 'Desc. Articulo');
$worksheet->setCellValue("D" . $indice, 'Cant. Pedida');
$worksheet->setCellValue("E" . $indice, 'Cant. Preparada');
$worksheet->setCellValue("F" . $indice, 'Accion');
$worksheet->setCellValue("G" . $indice, 'Fecha Asignacion');
$worksheet->setCellValue("H" . $indice, 'Hora Asigacion');
$worksheet->setCellValue("I" . $indice, 'Fecha Cierre');
$worksheet->setCellValue("J" . $indice, 'Hora Cierre');
$worksheet->setCellValue("K" . $indice, 'Almacen');
$worksheet->setCellValue("L" . $indice, 'Tiempo Respuesta');
$worksheet->setCellValue("M" . $indice, 'Usuario');
$indice += 1;
setear_datos_expedicion($indice, $db, $worksheet, $fecha_inicio, $fecha_fin);
setear_datos_recepcion($indice, $db, $worksheet, $fecha_inicio, $fecha_fin);
setear_datos_ubicacion($indice, $db, $worksheet, $fecha_inicio, $fecha_fin);

$db->close();
$filename = 'reporte_productividad_' . date('Y-m-d_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officeDocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header("filename: " . $filename);


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();


function setear_datos_recepcion(&$indice, $db, &$worksheet, $fecha_inicio, $fecha_fin)
{
    $sqls = obtener_sql_recepcion($fecha_inicio, $fecha_fin, "", "");
    // Obtener cantidad total registros
    $res = $db->query($sqls[1]);
    if (!$res) {
        print_r("error");
        exit;
    }
    $limite = 20000;
    $cantidad = $res->fetch_assoc()['cantidad'];
    $pasos = ceil($cantidad / $limite);
    for ($i = 0; $i < $pasos; $i++) {
        $offset = $i * $limite;
        $sqls = obtener_sql_recepcion($fecha_inicio, $fecha_fin, $limite, $offset);
        $res = $db->query($sqls[0]);
        $pedidos = array();
        while ($fila = $res->fetch_object()) {
            $pedidos["$fila->pedido"][] = trim($indice);
            setear_hoja_excel($worksheet, $indice, $fila);
            $indice++;
        }
        obtener_fecha_hora_asignacion($db, $worksheet, $pedidos);
    }
}

function setear_datos_expedicion(&$indice, $db, &$worksheet, $fecha_inicio, $fecha_fin)
{
    $sqls = obtener_sql_expedicion($fecha_inicio, $fecha_fin, "", "");
    // Obtener cantidad total registros
    $res = $db->query($sqls[1]);
    if (!$res) {
        print_r("error");
        exit;
    }
    $limite = 20000;
    $cantidad = $res->fetch_assoc()['cantidad'];
    $pasos = ceil($cantidad / $limite);
    for ($i = 0; $i < $pasos; $i++) {
        $offset = $i * $limite;
        $sqls = obtener_sql_expedicion($fecha_inicio, $fecha_fin, $limite, $offset);
        $res = $db->query($sqls[0]);
        $pedidos = array();
        while ($fila = $res->fetch_object()) {
            $pedidos["$fila->pedido"][] = trim($indice);
            setear_hoja_excel($worksheet, $indice, $fila);
            $indice++;
        }
        obtener_fecha_hora_asignacion($db, $worksheet, $pedidos);
    }
}

function setear_datos_ubicacion(&$indice, $db, &$worksheet, $fecha_inicio, $fecha_fin)
{
    $sqls = obtener_sql_ubicacion($fecha_inicio, $fecha_fin, "", "");
    // Obtener cantidad total registros
    $res = $db->query($sqls[1]);
    if (!$res) {
        print_r("error");
        exit;
    }
    $limite = 20000;
    $cantidad = $res->fetch_assoc()['cantidad'];
    $pasos = ceil($cantidad / $limite);
    $pedidos = array();
    for ($i = 0; $i < $pasos; $i++) {
        $offset = $i * $limite;
        $sqls = obtener_sql_ubicacion($fecha_inicio, $fecha_fin, $limite, $offset);
        $res = $db->query($sqls[0]);
        $pedidos = array();
        while ($fila = $res->fetch_object()) {
            $pedidos["$fila->pedido"][] = trim($indice);
            setear_hoja_excel($worksheet, $indice, $fila);
            $indice++;
        }
        obtener_fecha_hora_asignacion($db, $worksheet, $pedidos);
    }
}

function obtener_tiempo_trabajado($hora_inicio, $hora_fin)
{
    $tiempo_respuesta = "No se pudo obtener";
    try {
        if ($hora_inicio) {
            $hora_inicio = new DateTime($hora_inicio);
            $hora_fin = new DateTime($hora_fin);

            // Calculate the difference
            $interval = $hora_inicio->diff($hora_fin);

            // Get the difference in minutes and seconds
            $minutos = $interval->format('%i');
            $segundos = $interval->format('%s');
            $segundos = strlen($segundos) > 1 ? $segundos : "0$segundos";
            $minutos = strlen($minutos) > 1 ? $minutos : "0$minutos";
            $tiempo_respuesta = $minutos . ":" . $segundos;
        } else {
            $tiempo_respuesta = "--:--";
        }
    } catch (\Throwable $th) {
        guardar_error_log(__FILE__, $th->getMessage());
    }
    return $tiempo_respuesta;
}

function obtener_fecha_hora_asignacion(&$db, &$worksheet, $datos)
{
    $datos_condicion = implode("','", array_keys($datos));
    $sql = "SELECT
                pedido,
                MAX(fecasig) as fecha,
                MAX(horasig) as hora
            FROM
                assig_ped
            WHERE
                pedido IN ('$datos_condicion')
            GROUP by pedido
            ";
    $res = $db->query($sql);

    while ($fila = $res->fetch_object()) {
        //print_r($fila->pedido . "--" . $datos[$fila->pedido] . "--" . $fila->hora . "<br>");
        //$pedido = trim($worksheet->getCell('A' . $indice_interno));
        $indices = $datos[$fila->pedido];
        foreach ($indices as $indice) {
            $worksheet->setCellValue('G' . trim($indice), $fila->fecha);
            $worksheet->setCellValue('H' . trim($indice), $fila->hora);
            $hora_cierre = trim($worksheet->getCell('J' . trim($indice)));
            $worksheet->setCellValue('L' . trim($indice), obtener_tiempo_trabajado($fila->hora, $hora_cierre));
        }
    }
}

function setear_hoja_excel(&$worksheet, $indice, $fila)
{
    $worksheet->setCellValue('A' . $indice, trim($fila->pedido));
    $worksheet->setCellValue('B' . $indice, $fila->arti);
    $worksheet->setCellValue('C' . $indice, $fila->artides);
    $worksheet->setCellValue('D' . $indice, $fila->canpedida);
    $worksheet->setCellValue('E' . $indice, $fila->canprepara);
    $worksheet->setCellValue('F' . $indice, $fila->descri);
    $worksheet->setCellValue('I' . $indice, $fila->fecierre);
    $worksheet->setCellValue('J' . $indice, $fila->horcierre);
    $worksheet->setCellValue('K' . $indice, $fila->codalma);
    $worksheet->setCellValue('M' . $indice, $fila->usuario);
}
