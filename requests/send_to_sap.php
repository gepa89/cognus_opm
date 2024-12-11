<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
define('CENTRO', 'CHEL');
define('ALMACEN', 'CD11');

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
//QAS
//$config = [
//    'ashost' => '192.168.10.125',
//    'sysnr' => '00',
//    'client' => '300',
//    'user' => 'wmschac',
//    'passwd' => 'chacomer',
//    'trace' => SapConnection::TRACE_LEVEL_OFF,
//];

//PRD
$config = [
    'ashost' => '192.168.10.125',
    'sysnr' => '00',
    'client' => '300',
    'user' => 'wmschac',
    'passwd' => 'chacomer',
    'trace' => SapConnection::TRACE_LEVEL_OFF,
];
$c = new SapConnection($config);

header('Content-Type: application/json; charset=utf-8');

$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$pd = $_POST['pedido'];
$pedubicod = $_POST['pedubicod'];
//echo $pd;
$pdstr = '';
$header = array();
$hd['PSTNG_DATE'] = date('Ymd', strtotime('now'));
$hd['DOC_DATE'] = date('Ymd', strtotime('now'));
$hd['REF_DOC_NO'] = $pd;
array_push($header, $hd);
$cod['GM_CODE'] = '01';

if ($pd != '') {
    /*$sq = "select a.*,b.*,c.artval, d.artserie from pedrecab a 
            inner join pedredet b on a.pedrefer = b.pedrefer 
            inner join arti c on b.artrefer = c.artrefer
            left join serati d on b.artrefer = d.artrefer and a.pedrefer = d.artped
            where a.pedrefer = '{$pd}' and b.canprepa <> '' and a.pedresitu = 'CE' order by b.pedpos asc";*/

    $sq = "SELECT
                a.*,
                b.*,
                c.artval,
                d.artserie
            from
                pedubicab a
            inner join pedubidet b on
                a.pedubicod = b.pedubicod
            inner join arti c on
                b.artrefer = c.artrefer
            left join serati d on
                b.artrefer = d.artrefer
                and a.pedrefer = d.artped
            where
                a.pedrefer = '{$pd}'
                and a.pedubicod = '{$pedubicod}'
                and b.canubi <> ''
                and a.situped = 'CE' 
                and b.pedsent IS NULL
            order by
                b.pedpos asc";
    $rs = $db->query($sq);

    while ($ax = $rs->fetch_assoc()) {
        //guardar_custom_log(__FILE__, $sq);
        $excedente = $ax['exedente'] ?? 0;
        $articulo = $ax['artrefer'];
        $pedubicod = $ax['pedubicod'];
        $pedpos = $ax['pedpos'];
        $cc = 0;
        $multireferencia = $ax['etnum'];

        $sql = "SELECT
                    pallet_mat.pedpos,
                    pedredet.idpadre,
                    sum(pallet_mat.ap_cant) AS ap_cant
                FROM
                    pallet_mat
                INNER JOIN pedredet ON
                    pallet_mat.ap_pedido = pedredet.pedrefer
                    and pallet_mat.ap_mat = pedredet.artrefer
                    and pallet_mat.pedpos = pedredet.pedpos
                WHERE
                    ap_pedido = '$pd'
                    AND ap_mat = '$articulo'
                    and pallet_mat.etnum = '$multireferencia'
                GROUP by
                    pallet_mat.pedpos";

        //$sql = "SELECT pedpos,artrefer,canpedi, idpadre FROM pedredet WHERE pedrefer = '$pd'  and artrefer = '$articulo' and expst=1";
        $query = $db->query($sql);
        if (!$query) {
            echo json_encode(array('msg' => "error al guardar", 'err' => 1));
            guardar_error_log(__FILE__, $sql);
            exit;
        }
        while ($fila = $query->fetch_object()) {

            $cantidad_a_enviar = $fila->ap_cant - $excedente;
            $posicion = $fila->pedpos;
            if (!empty($fila->idpadre)) {
                $sql = "SELECT pedpos FROM pedredet WHERE pedrefer = '$pd' AND artrefer= '{$fila->idpadre}'";
                $res = $db->query($sql);
                $posicion = $res->fetch_assoc()['pedpos'];
            }
            $datax[$posicion]['PO_ITEM'] = '' . str_pad($posicion, 5, '0', STR_PAD_LEFT);
            $datax[$posicion]['PLANT'] = CENTRO;
            $datax[$posicion]['MOVE_TYPE'] = '101';
            $datax[$posicion]['STGE_LOC'] = ALMACEN;
            $datax[$posicion]['MVT_IND'] = 'B';
            $datax[$posicion]['SALES_ORD'] = '' . $pd;
            $datax[$posicion]['PO_NUMBER'] = '' . $pd;
            $datax[$posicion]['VAL_TYPE'] = '' . $ax['artval'];
            $datax[$posicion]['ENTRY_QNT'] = (float) $cantidad_a_enviar;
            $datax[$posicion]['GR_RCPT'] = '' . $ax['pedalemi'];
            $posSer = ((int) $posicion) / 10;
            $chassis[$cc]['MATDOC_ITM'] = str_pad($posSer, 4, '0', STR_PAD_LEFT);
            $chassis[$cc]['SERIALNO'] = '' . $ax['artserie'];
            $cc++;
        }
        $cc2 = 0;
    }
}

foreach ($datax as $pos => $ax) {
    //        var_dump($ax);
    $data[$cc2]['PO_ITEM'] = '' . str_pad($ax['PO_ITEM'], 5, '0', STR_PAD_LEFT);
    //        $data[$cc2]['MATERIAL'] = ''.$ax['artrefer'];
    $data[$cc2]['PLANT'] = CENTRO;
    $data[$cc2]['MOVE_TYPE'] = '101';
    $data[$cc2]['STGE_LOC'] = ALMACEN;
    $data[$cc2]['MVT_IND'] = 'B';
    $data[$cc2]['SALES_ORD'] = '' . $pd;
    $data[$cc2]['PO_NUMBER'] = '' . $pd;
    $data[$cc2]['VAL_TYPE'] = '' . $ax['VAL_TYPE'];
    $data[$cc2]['ENTRY_QNT'] = (float) $ax['ENTRY_QNT'];
    $data[$cc2]['GR_RCPT'] = '' . $ax['GR_RCPT'];
    $cc2++;
}

try {
    $f = $c->getFunction('BAPI_GOODSMVT_CREATE');
    //        echo "<br/>---------------------------------------<br/>";
    $options = [
        'rtrim' => true
    ];
    $datos = [
        'GOODSMVT_HEADER' => $hd,
        'GOODSMVT_CODE' => $cod,
        'GOODSMVT_ITEM' => $data,
        'GOODSMVT_SERIALNUMBER' => $chassis
    ];
    $result = $f->invoke($datos, $options);

    if (is_numeric(trim($result["MATERIALDOCUMENT"])) && (int) trim($result["MATERIALDOCUMENT"]) > 0) {
        $g = $c->getFunction('BAPI_TRANSACTION_COMMIT');
        $result = $g->invoke([
            'WAIT' => 'X'
        ], $options);

        $tosql = "update pedubicab
         set pedsent = 1 where pedrefer = '{$pd}' and pedubicod='$pedubicod'";
        $db->query($tosql);
        $tosql = "UPDATE pedubidet set pedsent = '1' 
        where pedrefer = '{$pd}' and artrefer = '$articulo' and pedubicod= '$pedubicod'";
        $db->query($tosql);

        $err = 0;
        $msg = 'Datos enviados correctamente.';
    } else {
        guardar_error_log(__FILE__, $msg);
        guardar_error_log(__FILE__, json_encode($datos));
        //guardar_error_log(__FILE__, json_encode($result, JSON_PRETTY_PRINT));
        $err = 1;
        $msg = $result['RETURN'][0]['MESSAGE'];
    }

    $ant = '';
} catch (SapException $ex) {
    $err = 1;
    $msg = 'Error en bapi. Consultar SAP.';
    $msg = $ex->getMessage();
    $db->rollback();
    guardar_error_log(__FILE__, $msg);
    guardar_error_log(__FILE__, json_encode($datos));
} catch (Exception $e) {
}
$db->commit();
echo json_encode(array('msg' => $msg, 'err' => $err));

exit();
