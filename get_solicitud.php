<?php
require('/var/www/html/hanaDB.php');
include('conection.php');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require ('conect.php');
//require_once(__DIR__ . "/../logger/logger.php");
$arrAux = array();
//if($_SESSION['user_rol'] == 2){
$solicitante = " and jefe = '" . $_SESSION['user'] . "'";
//}else{
//    $solicitante = '';
//}
$flg = $total = 0;
//var_dump($_SESSION);
//require ('/var/www/html/saprfc/prd.php');
//require ('/var/www/html/hanaDB.php');
// require ('/var/www/html/conect.php');

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
// require_once $_SERVER["DOCUMENT_ROOT"] . 'class/crmtiDb.php';


$SERVER = '192.168.136.32';
$USER = 'wms_luque';
$PASS = 'wms_luque_2024';
$DB = 'copia';

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$config = [
    'ashost' => '192.168.10.10',
    'sysnr'  => '00',
    'client' => '300',
    'user'   => 'comprasc',
    'passwd' => 'chacomer',
    'trace'  => SapConnection::TRACE_LEVEL_OFF,
];

$sq = '';
//var_dump($grupos);
if (isset($_REQUEST) && $_REQUEST['id']) {
    $sq = " AND pedidosincab.BANFN = '{$_REQUEST['id']}'";
    $flg = 1;
} else if ($_REQUEST['arg']) {
    $sq = " AND pedidosincab.BANFN like '%{$_REQUEST['arg']}%'";
    $flg = 1;
}
$sql = "SELECT
pedidosincab.grupcompra,
pedidosincab.fecre,
pedidosincab.clasdoc,
pedidosincab.situped,
pedidosincab.indica_liberacion,
pedidosincab.docompra as banfn,
zgcompras.correo,
zgcompras.jefe
from pedidosincab 
LEFT JOIN zgcompras on pedidosincab.grupcompra=zgcompras.grupo_compras
WHERE pedidosincab.clasdoc='PROF'
AND pedidosincab.situped ='PD'
and pedidosincab.indica_liberacion is NULL 
and zgcompras.jefe = '" . $_SESSION['user'] . "'" . $sq . " order by pedidosincab.fecre desc";
//echo $sql;
$nop = '';
$result = $db->query($sql);
//print $sql;
while ($row = $result->fetch_object()) {
    $pedidos[$row->BANFN] = $row->BANFN;

    $total++;

    $d = new SapConnection($config);

    $g = $d->getFunction('BAPI_REQUISITION_GETDETAIL');
    //        echo "<pre>";var_dump($f);echo "</pre>";
    $optionsCom = [
        'rtrim' => true
    ];
    $resultCom = $g->invoke([
        'NUMBER' => str_pad($row->BANFN, 10, '0', STR_PAD_LEFT),
        'ACCOUNT_ASSIGNMENT' => 'X',
        'ITEM_TEXTS' => 'X'
    ], $optionsCom);
    //    $dt_ofertas[$row->BANFN]['comment'] = '';
    //    echo "<pre>";var_dump($resultCom);echo "</pre>";
    $pd = str_pad($row->BANFN, 10, '0', STR_PAD_LEFT);
    //$sqsa = "select com_txt from header_comment where com_solped = '{$pd}' limit 1";
    //    echo $sqsa;
    //$rs = $db->query($sqsa);
    //    var_dump($rs);
    //$ax = $rs->fetch_object();
    $dt_ofertas[$row->BANFN]['comment'] = ($ax['com_txt'] != '') ? $ax['com_txt'] : '';
    $sapCommento = '';

    try {

        $dx = new SapConnection($config);
        $sapCommento = '';
        $gx = $dx->getFunction('ZREAD_TEXT');
        //        echo "<pre>";var_dump($f);echo "</pre>";
        $optionsComx = [
            'rtrim' => true
        ];
        $resultComx = $gx->invoke([
            'VID' => 'B01',
            'VLANGUAGE' => 'S',
            'VNAME' => str_pad($pd, 10, '0', STR_PAD_LEFT),
            'VOBJECT' => 'EBANH',
            'VLINES' => array()
        ], $optionsComx);
        if ($resultComx["VLINES"][0]["TDLINE"]) {
            $sapCommento = $resultComx["VLINES"][0]["TDLINE"];
        }

        //        echo "<pre>";var_dump($resultCom);echo "</pre>";
    } catch (SapException $ex) {
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;

        //        echo "<pre>";var_dump($ex);echo "</pre>";
    }

    //    
    if ($sapCommento != '') {
        $dt_ofertas[$row->BANFN]['comment'] = $sapCommento;
    }

    $dt_ofertas[$row->BANFN]['cuenta'] = $resultCom["REQUISITION_ACCOUNT_ASSIGNMENT"][0]['G_L_ACCT'];
    $dt_ofertas[$row->BANFN]['ceco'] = $resultCom["REQUISITION_ACCOUNT_ASSIGNMENT"][0]['COST_CTR'];
    $dt_ofertas[$row->BANFN]['ordint'] = $resultCom["REQUISITION_ACCOUNT_ASSIGNMENT"][0]['ORDER_NO'];
    $dt_ofertas[$row->BANFN]['n_op'] = $resultCom["REQUISITION_ITEMS"][0]['PREQ_NO'];
    $dt_ofertas[$row->BANFN]['moneda'] = $resultCom["REQUISITION_ITEMS"][0]['CURRENCY'];
    if (strtolower($resultCom["REQUISITION_ITEMS"][0]['CREATED_BY']) != 'comprasc') {
        $usr_sol = $dt_ofertas[$row->BANFN]['usr'] = $resultCom["REQUISITION_ITEMS"][0]['CREATED_BY'];
    } else {
        $usr_sol = $dt_ofertas[$row->BANFN]['usr'] = !empty($row->BEDNR) ? $row->BEDNR : $resultCom["REQUISITION_ITEMS"][0]['CREATED_BY'];
    }

    foreach ($resultCom["REQUISITION_ITEMS"] as $k => $dat) {
        $pos[$k]['desc'] = $dat['SHORT_TEXT'];
        $pos[$k]['cant'] = $dat['QUANTITY'];
        $pos[$k]['pre'] = $dat['PRICE_UNIT'];
    }
    foreach ($resultCom["REQUISITION_ACCOUNT_ASSIGNMENT"] as $k => $dat) {
        $pos[$k]['pos'] = $dat['PREQ_ITEM'];
        $pos[$k]['cta'] = $dat['G_L_ACCT'];
        $pos[$k]['ceco'] = $dat['COST_CTR'];
        $pos[$k]['oi'] = $dat['ORDER_NO'];
        //        try{
        //            $f = $d->getFunction('ZREAD_TEXT');
        //            $result = $f->invoke([    
        //               'VID' => 'F01',
        //               'VLANGUAGE' => 'S',
        //               'VNAME' => $row->BANFN.$dat['PREQ_ITEM'],
        //               'VOBJECT' => 'EKPO'
        //            ]);
        //            $ant = '';
        //            foreach($result["VLINES"] as $k => $km){
        //                if($pos[$k]['txt'] != ""){
        //                    $pos[$k]['txt'] .= "<br/>". trim($km["TDLINE"]);
        //                }else{
        //                    $pos[$k]['txt'] = " ".trim($km["TDLINE"]);
        //                }
        //            }
        //        }catch(SapException $ex) {
        //            $pos[$k]['txt'] = '';
        //        }

    }

    if ($resultCom["REQUISITION_ITEMS"][0]['CREATED_BY'] == 'comprasc') {
        $usr_sol = $dt_ofertas[$row->BANFN]['n_op'] = $resultCom["REQUISITION_ITEMS"][0]['TRACKINGNO'];
    }

    $dt_ofertas[$row->BANFN]['fecha'] = date("d-m-Y", strtotime($resultCom["REQUISITION_ITEMS"][0]['CH_ON']));
    $usr_qry = "SELECT pr_user, pr_nombre as NAME_TEXT 
                FROM usuarios
                WHERE pr_user = 'GUIDAGOG' limit 1";
    //    echo $usr_qry;
   // $result = $db->query($usr_qry);            
    $usrs = $db->query($usr_qry);  
  //  print $usr_qry;
    while ($rs = $usrs->fetch_object()) {
    $user_data[$rs->NAME_TEXT] = $rs->NAME_TEXT;
   // while ($rs = odbc_fetch_object($usrs)) {
    //    $user_data = $rs->NAME_TEXT;
    }
    $dt_ofertas[$row->BANFN]['usr_name'] = $user_data;
}

$data['total'] = $total;
//var_dump($dt_ofertas);
echo json_encode(array("total" => $data, "ofertas" => $ofertas, "pedidos" => $pedidos, "datos" => $dt_ofertas, "d_posicion" => $pos, "provs" => $provs));
