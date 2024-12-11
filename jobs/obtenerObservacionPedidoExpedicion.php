<?php
require(__DIR__ . '/../conect.php');
include(__DIR__ . '/src/adLDAP.php');
require_once(__DIR__ . "/../../saprfc/prd.php");
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
function obtenerMensajePedido($config, $id_pedido)
{
    try {
        $con_sap = new SapConnection($config);
        //    echo $data['vgbel']." - ";
        $f = $con_sap->getFunction('ZREAD_TEXT');

        //    $f->setParameterActive('ARCHIVE_HANDLE', false);
        //    $f->setParameterActive('VCLIENT', false);
        $result = $f->invoke([
            'VID' => 'Z003',
            'VLANGUAGE' => 'S',
            'VNAME' => '1009595234',
            'VOBJECT' => 'VBBK'
        ]);
        return $result['VLINES'][0]['TDLINE'];
    } catch (SAPNWRFC\ConnectionException $error) {
        print_r($error);

    } catch (SapException $ex) {
        //    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
        //echo $msg;
    }
    return "";
}
?>