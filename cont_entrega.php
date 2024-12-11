<?php
require ('conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
if($_POST['codigo'] != ''){
    $response = array();
    $usr = ($_POST['usuario']);
    $VBKOK_WA = array();
    $msg = '';
    $pedido = str_pad($_POST['codigo'], 10, "0", STR_PAD_LEFT);
//    echo "aqtui";
    $f = $c->getFunction('WS_DELIVERY_UPDATE_2');
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $VBKOK_WA['VBELN_VL'] = $pedido;
    $VBKOK_WA['WABUC'] = 'X';
    $result = $f->invoke([    
        'VBKOK_WA' => $VBKOK_WA,
        'SYNCHRON' => 'X',
        'COMMIT' => 'X',
        'DELIVERY' => $pedido,
        'NICHT_SPERREN_1' => 'X',
        'IF_DATABASE_UPDATE_1' => '1',
        'IF_ERROR_MESSAGES_SEND' => 'X'
    ]);
    
//    echo "<pre>"; var_dump($result["PROT"]);echo "</pre>"; 
    if(count($result["PROT"]) == 0){
        $sq_c = "update log_cierre set ct_st = 2, ct_ts_cont = now() where ct_empaque = {$pedido}";
        $db->query($sq_c);
        $err = 0;
        $msg = 'Documento contabilizado correctamente';
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
}else{
    $err = 1;
    $msg = 'Debe ingresar un número de entrega';
}
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'ped' => $pedido));
