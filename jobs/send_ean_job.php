<?php
require_once(__DIR__ . "/../../saprfc/prd.php");
require_once(__DIR__ . "/../logger/logger.php");

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
$config = [
    'ashost' => '192.168.10.10',
    'sysnr' => '00',
    'client' => '300',
    'user' => 'wmschac',
    'passwd' => 'chacomer',
    'trace' => SapConnection::TRACE_LEVEL_OFF,
];

require_once '/var/www/html/ws.php';
require('/var/www/html/hanaDB.php');

//require ('/var/www/html/alertas/phpmailer/class.phpmailer.php');
//require ('/var/www/html/alertas/phpmailer/class.smtp.php');
// var_dump($_POST);
$SERVER = '127.0.0.1';
$USER = 'root';
$PASS = 'gepa5266';
$DB = 'wmad';
$db = new mysqli($SERVER, $USER, $PASS, $DB);

guardar_info_log(__FILE__,json_encode($_POST));
//$sq = "select * from mean";
$sq = "select a.*, b.ing_mat from la_restore a left join log_registro_ean b on a.ean11 = b.ing_ean and a.matnr = b.ing_mat where b.ing_mat is null";
$rs = $db->query($sq);

$response = array();
while ($ax = $rs->fetch_assoc()) {

    $unimed = strtoupper(trim($ax['meinh']));
    $ean = strtoupper(trim($ax['ean11']));
    $tipo = strtoupper(trim($ax['eantp']));
    


    try {
        $mat = strtoupper(trim($ax['matnr']));
        if (is_numeric($mat)) {
            $materials = str_pad($mat, 18, "0", STR_PAD_LEFT);
        } else {
            $materials = $mat;
        }
        $xc = new SapConnection($config);
        $f = $xc->getFunction('BAPI_MATERIAL_SAVEDATA');
        //            var_dump($f);
        $options = [
            //            'rtrim' => true
        ];
        $settings = array(
            'HEADDATA' => array('MATERIAL' => $materials),
            'UNITSOFMEASURE' => array(array('ALT_UNIT' => $unimed, 'EAN_UPC' => $ean, 'EAN_CAT' => $tipo)),
            'UNITSOFMEASUREX' => array(array('ALT_UNIT' => $unimed, 'EAN_UPC' => 'X', 'EAN_CAT' => 'X'))
        );
        $result = $f->invoke($settings, $options);
        if ($result["RETURN"]) {
            $sq = "insert into log_registro_ean set
                        ing_ean = '{$ean}',
                        ing_mat  = '{$materials}'
                        ";
            $db->query($sq);
            $response['error'] = false;
            $response['mensaje'] = $result["RETURN"]["MESSAGE"];
        } else {
            $response['error'] = true;
            $response['mensaje'] = 'No se pudo registrar material';
        }
        var_dump($settings);
    } catch (SapException $ex) {
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;

        echo "<pre>";
        var_dump($ex);
        echo "</pre>";
    }

}
echo json_encode($response);