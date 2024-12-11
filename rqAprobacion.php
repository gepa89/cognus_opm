<?php
//require ('/var/www/html/saprfc/qas.php');
require ('/var/www/html/hanaDB.php');
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
//QAS
$config = [
    'ashost' => '192.168.10.10',
    'sysnr'  => '00',
    'client' => '300',
    'user'   => 'comprasc',
    'passwd' => 'chacomer',
    'trace'  => SapConnection::TRACE_LEVEL_OFF,
];

if(isset($_POST["of"]) && $_POST["of"] != ''){
     $oci_qry = "SELECT * FROM SAPABAP1.ZGCOMPRAS where JEFE = '{$_POST["usr"]}' limit 1";
//    echo $oci_qry;
    $result = odbc_exec($prd, $oci_qry);
    while ($row = odbc_fetch_object($result)){
        $dt_jf = $row;
    }
//    var_dump($dt_jf);
//        'PO_REL_CODE' => $dt_jf->DEPARTAMENTO,
//        'USE_EXCEPTIONS' => 'X'));
//    
//    $f = $c->getFunction('BAPI_PO_RELEASE');
//    
//    $options = [
//        'rtrim' => true
//    ];
//    $result = $f->invoke([            
//        'PURCHASEORDER' => trim($_POST["of"]),
//        'PO_REL_CODE' => $dt_jf->DEPARTAMENTO,
//        'USE_EXCEPTIONS' => 'X'
//    ],$options);
//    var_dump($result);
//    
    
//    echo "'PURCHASEORDER' => {$_POST["of"]},'PO_REL_CODE' => {$dt_jf->DEPARTAMENTO},'USE_EXCEPTIONS' => 'X'";
    try {
        $c = new SapConnection($config);

        $f = $c->getFunction('BAPI_PO_RELEASE');
    //    $f->setParameterActive('ARCHIVE_HANDLE', false);
    //    $f->setParameterActive('VCLIENT', false); 
        $options = [
            'rtrim' => true
        ];
//        echo "'PURCHASEORDER' => ".trim($_POST["of"]);
//        echo "'PO_REL_CODE' =>". $dt_jf->DEPARTAMENTO;
        $result = $f->invoke([            
            'PURCHASEORDER' => trim($_POST["of"]),
            'PO_REL_CODE' => $dt_jf->DEPARTAMENTO,
            'USE_EXCEPTIONS' => 'X'
        ],$options);
    //    echo "aca";
//         echo "<pre>";var_dump($f);echo "<pre>";
//        echo "<pre>";var_dump($result);echo "<pre>";
        /*
        * array(2) {
        *   ["COUNTER"]=>
        *   int(2)
        *   ["RESULT"]=>
        *   int(1)
        *   }
        */
        $err = 0;
        if($result["REL_STATUS_NEW"] == 'X'){
            $err = 0;
            $msg = 'Oferta aprobada correctamente';
        }
        
    } catch(SapException $ex) {
        echo '<pre>';var_dump($ex);"</pre>";
        echo '<pre>';var_dump($ex->getErrorInfo());"</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
    }
    
    echo json_encode(array("err" => $err, "msg" =>$msg));
    
    
}
