<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//require_once(__DIR__ . "/../../saprfc/prd.php");

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;

$config = [
    'ashost' => '192.168.10.125',
    'sysnr' => '00',
    'client' => '300',
    'user' => 'wmschac',
    'passwd' => 'chacomer',
    'trace' => SapConnection::TRACE_LEVEL_OFF,
];
$c = new SapConnection($config);

$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$pd = $_POST['pedido'];
//echo $pd;
$pdstr = '';
$header = array();
//ZUB Y EUB
$hd['PSTNG_DATE'] = date('Ymd', strtotime('now'));
$hd['DOC_DATE'] = date('Ymd', strtotime('now'));
$hd['REF_DOC_NO'] = $pd;
array_push($header, $hd);
$hd2['VBELN_VL'] = $pd;
$hd2['WABUC'] = 'X';

//array_push($header,$hd);
$cod['GM_CODE'] = '04';
if ($pd != '') {
    $sq = "select a.*,b.*,c.artval, d.artserie from pedexcab a 
            inner join pedexdet b on a.pedexentre = b.pedexentre 
            inner join arti c on b.artrefer = c.artrefer
            left join serati d on b.artrefer = d.artrefer and a.pedexentre = d.artentr
            where b.idpadre is null and a.pedexentre = '{$pd}' and a.siturefe IN ('CE','PP') order by b.pedpos asc ";
    guardar_info_log(__FILE__, $sq);
    $rs = $db->query($sq);
    $cc = 0;
    $tipo = null;
    while ($ax = $rs->fetch_assoc()) {
        $tipo = $ax['pedclase'];
        $datax[$ax['pedpos']]['PO_ITEM'] = '' . str_pad($ax['pedpos'], 5, '0', STR_PAD_LEFT);
        $datax[$ax['pedpos']]['PLANT'] = 'LCC1';
        $datax[$ax['pedpos']]['MOVE_TYPE'] = '351';
        $datax[$ax['pedpos']]['STGE_LOC'] = 'LDAL';
        $datax[$ax['pedpos']]['SPEC_STOCK'] = 'E';
        $datax[$ax['pedpos']]['SALES_ORD'] = '' . $pd;
        $datax[$ax['pedpos']]['PO_NUMBER'] = '' . $pd;
        $datax[$ax['pedpos']]['VAL_TYPE'] = '' . $ax['artval'];
        $datax[$ax['pedpos']]['ENTRY_QNT'] = (float) $ax['canprepa'];
        $datax[$ax['pedpos']]['GR_RCPT'] = '' . $ax['clirefer'];
        $posSer = ((int) $ax['pedpos']) / 10;
        $chassis[$cc]['MATDOC_ITM'] = str_pad($posSer, 4, '0', STR_PAD_LEFT);
        $chassis[$cc]['SERIALNO'] = '' . $ax['artserie'];
        $chassisVta[$cc]['RFBEL'] = $pd;
        $chassisVta[$cc]['RFPOS'] = str_pad($ax['pedpos'], 6, '0', STR_PAD_LEFT);
        $chassisVta[$cc]['SERNR'] = '' . $ax['artserie'];
        $cc++;
    }
    $cc2 = 0;
    foreach ($datax as $pos => $ax) {
        $cantidad = (float) $ax['ENTRY_QNT'];
        if ($cantidad == 0.0 && in_array($tipo, ['ZUB', 'EUB'])) {
            continue;
        }
        if (strtoupper($tipo) == "ZCOS") {
            $dataVta[$cc2]['LIANP'] = 'X';
            $dataVta[$cc2]['LFIMG'] = $cantidad;
        } else {
            $dataVta[$cc2]['PIKMG'] = $cantidad;
        }
        $data[$cc2]['PO_ITEM'] = '' . str_pad($ax['PO_ITEM'], 5, '0', STR_PAD_LEFT);
        $data[$cc2]['PLANT'] = 'LCC1';
        $data[$cc2]['MOVE_TYPE'] = '351';
        $data[$cc2]['STGE_LOC'] = 'LDAL';
        $data[$cc2]['SPEC_STOCK'] = 'E';
        $data[$cc2]['SALES_ORD'] = '' . $pd;
        $data[$cc2]['PO_NUMBER'] = '' . $pd;
        $data[$cc2]['VAL_TYPE'] = '' . $ax['VAL_TYPE'];
        $data[$cc2]['ENTRY_QNT'] = $cantidad;
        $data[$cc2]['GR_RCPT'] = '' . $ax['GR_RCPT'];

        if ($cantidad == 0.0) {
            $dataVta[$cc2]['LIPS_DEL'] = 'X';
        }
        $dataVta[$cc2]['VBELN_VL'] = '' . $pd;
        $dataVta[$cc2]['POSNR_VL'] = '' . str_pad($ax['PO_ITEM'], 6, '0', STR_PAD_LEFT);
        $dataVta[$cc2]['VBELN'] = '' . $pd;
        $dataVta[$cc2]['POSNN'] = '' . str_pad($ax['PO_ITEM'], 6, '0', STR_PAD_LEFT);

        $cc2++;
    }
    if ($tipo == 'ZUB' || $tipo == 'EUB') {
        try {
            $f = $c->getFunction('BAPI_GOODSMVT_CREATE');
            $options = [
                'rtrim' => true
            ];
            $arrPar = array(
                'GOODSMVT_HEADER' => $hd,
                'GOODSMVT_CODE' => $cod,
                'GOODSMVT_ITEM' => $data,
                'GOODSMVT_SERIALNUMBER' => $chassis
            );
            $datos = [
                'GOODSMVT_HEADER' => $hd,
                'GOODSMVT_CODE' => $cod,
                'GOODSMVT_ITEM' => $data,
                'GOODSMVT_SERIALNUMBER' => $chassis
            ];
            guardar_sap_log(json_encode($datos));
            $result = $f->invoke($datos, $options);
            if (is_numeric(trim($result["MATERIALDOCUMENT"])) && (int) trim($result["MATERIALDOCUMENT"]) > 0) {
                $g = $c->getFunction('BAPI_TRANSACTION_COMMIT');
                $result = $g->invoke([
                    'WAIT' => 'X'
                ], $options);

                $tosql = "update pedexcab set pedsent = 1, send=1 where pedexentre = '{$pd}'";
                $db->query($tosql);

                $err = 0;
                $msg = 'Datos enviados correctamente.';
            } else {
                $err = 1;
                $msg = 'Error al enviar documento. #1';
                guardar_error_log("error envio material send", json_encode($datos, JSON_PRETTY_PRINT));
                guardar_error_log("error envio material rec", json_encode($result, JSON_PRETTY_PRINT));
            }

            $ant = '';
        } catch (SapException $ex) {

            $err = 1;
            $msg = 'Error en bapi. Consultar SAP.';
        }
    } else {
        $arrex = array();
        try {
            $f = $c->getFunction('WS_DELIVERY_UPDATE_2');
            $options = [
                'rtrim' => true
            ];
            $arrex = array(
                'SYNCHRON' => 'X',
                'COMMIT' => 'X',
                'DELIVERY' => $pd,
                'IF_DATABASE_UPDATE_1' => '1',
                'IF_ERROR_MESSAGES_SEND' => 'X',
                'VBPOK_TAB' => $dataVta,
                'IT_SERNR_UPDATE' => $chassisVta
            );
            if ($tipo != "ZCOS") {
                $arrex['UPDATE_PICKING'] = 'X';
                $hd2['KOMUE'] = 'X';
            }
            $arrex['VBKOK_WA'] = $hd2;
            $result = $f->invoke($arrex, $options);
            if (trim($result["EF_ERROR_ANY"]) == '') {
                $tosql = "update pedexcab set pedsent = 1,send=1 where pedexentre = '{$pd}'";
                $db->query($tosql);
                $err = 0;
                $msg = 'Datos enviados correctamente.';
            } else {
                $err = 1;
                $msg = 'Error al enviar documento. #2';
                guardar_error_log("error WS_DELIVERY_UPDATE_2", json_encode($result, JSON_PRETTY_PRINT));
            }

            $ant = '';
        } catch (SapException $ex) {
            //            var_dump($ex);
            guardar_error_log("error critico WS_DELIVERY_UPDATE_2", json_encode($arrex, JSON_PRETTY_PRINT));
            $err = 1;
            $msg = $ex->getMessage();
            guardar_error_log("error", $msg);
            guardar_error_log("error reqeust", $ex);
        }
    }
}
if ($err == 0) {
    $db->commit();
} else {
    $db->rollback();
}
$db->close();
echo json_encode(array('msg' => $msg, 'err' => $err));

exit();
