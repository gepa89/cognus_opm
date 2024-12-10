<?php
require ('/var/www/html/saprfc/qas.php');
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
// var_dump($_POST);
if($_POST['codigo'] != ''){
    $response = array();
    $usr = ($_POST['usuario']);
    $pedido = str_pad($_POST['codigo'], 10, "0", STR_PAD_LEFT);
//    echo "aqtui ".$pedido;
    try {
        $f = $c->getFunction('ZMM_F_CONTROL');
        $result = $f->invoke([    
            'VUSUARIO' => $usr,
            'VENTREGA' => $pedido
        ]);
        if(count($result["ZZMENSAJES"]) > 0){
            $err = 1;
            $msg = $result["ZZMENSAJES"][0]["MENSAJE"];
        }else{
            $err = 0;
            $msg = '';        
        }
    }catch(SapException $ex) {
//            echo '<pre>';var_dump($ex);"</pre>";
//            echo '<pre>';var_dump($ex->getErrorInfo());"</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
    }
//    echo "<pre>"; var_dump($result);echo "</pre>"; 
}else{
    $err = 1;
    $msg = 'Debe ingresar un número de entrega';
}
//require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'ped' => $pedido));
