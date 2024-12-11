<?php

require('../conect.php');
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/utils/etiquetas.php");
require_once(__DIR__ . "/utils/componente_etiqueta.php");

$pd = '';
$fl = 0;
$data = array();
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$tipo = $_GET['impresion'];
$codalma = $_GET['codalma'];
$configuracion_etiqueta = obtenerConfiguracionEtiqueta($_GET['configuracion']);

//$sbNiv = array(0=>"-",1=>"A", 2=>"B", 3=>"C", 4=>"D", 5=>"E", 6=>"F", 7=>"G", 8=>"H");
$sbNiv = array('-', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
//echo count($sbNiv);
switch ($tipo) {
    case 'reean':
        if ($_GET['etNros'] != '') {
            $nros = explode(',', $_GET['etNros']);
            $inquery = '';
            foreach ($nros as $k => $v) {
                $data[] = $v;
            }
            //            $qry = "select * from etiquetas where etnum in (".$inquery.")";
//            $rs = $db->query($qry);
//            while($ax = $rs->fetch_assoc()){
//                $data[] = $ax['etnum'];
//            }
        }
        break;
    case 'reex':
        if ($_GET['etNros'] != '') {
            $nros = explode(',', $_GET['etNros']);
            $inquery = '';
            foreach ($nros as $k => $v) {
                if (is_numeric($v)) {
                    if ($inquery == '') {
                        $inquery = "'" . trim($v) . "'";
                    } else {
                        $inquery .= ",'" . trim($v) . "'";
                    }
                }
            }
            $qry = "select * from etiquetas where etnum in (" . $inquery . ")";
            $rs = $db->query($qry);
            while ($ax = $rs->fetch_assoc()) {
                $data[] = $ax['etnum'];
            }
        } else if ($_GET['etTipo'] != '' && (int) $_GET['etCantidad'] > 0) {
            for ($i = 0; $i < (int) $_GET['etCantidad']; $i++) {
                $qr = "insert into etiquetas set ettip = '{$_GET['etTipo']}'";
                //                echo $qr;
                $db->query($qr);
                $nr = (string) $db->insert_id;
                $data[] = $nr;
            }
        }
        break;
    case 'reub':
        $sqHuec = $sqNiv = '';

        if ($_GET['dEstante'] != '') {
            $fst = substr($_GET['dEstante'], 0, 1);

            $desdeEst = (int) substr($_GET['dEstante'], -3);
            //            echo $desdeEst;
//            echo $desdeEst;
            if ($_GET['hEstante'] != '') {
                $hastaEst = (int) substr($_GET['hEstante'], -3);
            } else {
                $hastaEst = (int) $desdeEst;
            }
        }
        if ($_GET['dHueco'] != '') {
            $desdeHuec = (int) $_GET['dHueco'];
            if ($_GET['hHueco'] != '') {
                $hastaHuec = (int) $_GET['hHueco'];
                $sqHuec = " and CAST(ubihuec AS SIGNED INTEGER) between {$desdeHuec} and {$hastaHuec} ";
            } else {
                $hastaHuec = (int) $desdeHuec;
                $sqHuec = " and CAST(ubihuec AS SIGNED INTEGER) = {$desdeHuec}";
            }
        }
        if ($_GET['dNiv'] != '') {
            $desdeNiv = (int) $_GET['dNiv'];
            if ($_GET['hNiv'] != '') {
                $hastaNiv = (int) $_GET['hNiv'];
                $sqNiv = " and ubiniv between {$desdeNiv} and {$hastaNiv} ";
            } else {
                $hastaNiv = (int) $desdeNiv;
                $sqNiv = " and ubiniv =  {$desdeNiv}";
            }
        }
        $sbNivUs = array();
        if ($_GET['dSubNiv'] != '') {
            $desdeSbNiv = (int) $_GET['dSubNiv'];
            if ($_GET['hSubNiv'] != '') {
                $hastaSbNiv = (int) $_GET['hSubNiv'];
            } else {
                $hastaSbNiv = (int) $desdeSbNiv;
            }
        }
        array_push($sbNivUs, $sbNiv[0]);
        for ($k = $desdeSbNiv; $k <= $hastaSbNiv; $k++) {
            array_push($sbNivUs, $sbNiv[$k]);
        }
        //        var_dump($sbNivUs);
        $mango = 0;
        $ubicss = '';
        $cc = 0;
        $data = array();
        for ($i = $desdeEst; $i <= $hastaEst; $i++) {
            $estante = $fst . str_pad($i, 3, '0', STR_PAD_LEFT);

            $sq = "select ubirefer from ubimapa where ubiestan = '{$estante}' {$sqHuec} {$sqNiv} and cod_alma='$codalma'";
            //            echo $sq."<br/>";
            $rs = $db->query($sq);
            while ($ax = $rs->fetch_assoc()) {
                $ubifull = str_pad($ax['ubirefer'], 10, '-', STR_PAD_RIGHT);
                $lst = substr($ubifull, -1, 1);
                if (in_array($lst, $sbNivUs)) {
                    $es = substr($ax['ubirefer'], 0, 4);
                    $hu = substr($ax['ubirefer'], 4, 3);
                    $ni = substr($ax['ubirefer'], 7);
                    $ubix = $es . "-" . $hu . "-" . $ni; //.$sbNiv[$l];
                    $data[$cc]['forcod'] = $ubifull;
                    $data[$cc]['forlbl'] = $ubix;
                    $cc++;
                }
            }
        }
        break;
}

$generador = new Picqer\Barcode\BarcodeGeneratorPNG();
$mpdf = new \Mpdf\Mpdf([
    'tempDir' => '/tmp',
    'mode' => 'utf-8',
    'format' => $configuracion_etiqueta['tamanho'],
    'margin_top' => $configuracion_etiqueta['margin_top'],
    'margin_bottom' => 0,
    'margin_left' => 0,
    'margin_right' => 0
]);

$estilos = file_get_contents(__DIR__ . '/utils/estilos_codigo_barras.css');
$mpdf->WriteHTML($estilos, \Mpdf\HTMLParserMode::HEADER_CSS);
$nombre_archivo = "codigo-de-barras.pdf";

$html = "";
$primera_pagina = true;
foreach ($data as $dato) {
    if ($primera_pagina) {
        $primera_pagina = false;
    } else {
        $html .= "<pagebreak/>";
    }
    $url = base64_encode($generador->getBarcode($dato['forcod'], $generador::TYPE_CODE_128, 2, $configuracion_etiqueta['weight']));
    $html = $html . hojaEtiqueta($url, $dato['forlbl'], $configuracion_etiqueta);
}
$db->close();
$mpdf->WriteHTML($html);
$mpdf->Output($nombre_archivo, "I");