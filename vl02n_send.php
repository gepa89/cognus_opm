<?php
require ('conect.php');
require_once(__DIR__ . "/../saprfc/prd.php");
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
$db = new mysqli($SERVER,$USER,$PASS,$DB);
if($_POST['codigos'] != ''){
    
    $response = $posas = array();
    $usr = ($_POST['usuario']);
    $VBKOK_WA = array();
    $msg = '';
    $pedido = str_pad($_POST['entrega'], 10, "0", STR_PAD_LEFT);
    
    try {
        $d = new SapConnection($config);
        $f = $d->getFunction('WS_DELIVERY_UPDATE_2');
        $VBKOK_WA['VBELN_VL'] = $pedido;
        $VBKOK_WA['KOMUE'] = 'X';

        foreach($_POST['codigos'] as $k => $val){
            $posas[$k]['VBELN_VL'] = ''.$pedido;
            $posas[$k]['POSNR_VL'] = ''.$val['pos'];
            $posas[$k]['VBELN'] = ''.$pedido;
            $posas[$k]['POSNN'] = ''.$val['pos'];
            if($val['pk']){
                $posas[$k]['PIKMG'] = (int)$val['pk'];// ENVIAR SOLO SI ES PICKING
            }else if($val['anu']){
                $posas[$k]['LIPS_DEL'] = 'X';// ENVIAR SOLO SI ES ANULACION
            }

        }
        
        $result = $f->invoke([    
            'VBKOK_WA' => $VBKOK_WA,
            'SYNCHRON' => 'X',
            'COMMIT' => 'X',
            'DELIVERY' => $pedido,
            'UPDATE_PICKING' => 'X',
            'NICHT_SPERREN_1' => 'X',
            'IF_DATABASE_UPDATE_1' => '1',
            'IF_ERROR_MESSAGES_SEND' => 'X',
            'VBPOK_TAB' => $posas
        ]);
    //    
        if(count($result["PROT"]) == 0){
            $err = 0;
            $msg = 'Documento modificado correctamente';
        }else{
            foreach($result["PROT"] as $ind => $vl){
                if($vl["MSGNO"] == '602'){
                    $err = 1;
                    $msg .= 'Entrega ya contabilizada ';
                }else{
                    $err = 1;
                    $msg .= 'Ocurrió un error al contabilizar entrega ';
                }
            }
        }
    }catch(SapException $ex) {
//            echo '<pre>';var_dump($ex);"</pre>";
//            echo '<pre>';var_dump($ex->getErrorInfo());"</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
    }    
}else{
    $err = 1;
    $msg = 'Debe ingresar un número de entrega';
}
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'ped' => $pedido));
