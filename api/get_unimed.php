<?php
//require ('/var/www/html/hanaDB.php');
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
     $response = array();
//     $query = "select * from prt_users where prt_user = '{$_POST['user']}' and prt_psw = '{$_POST['pass']}'";
     $oci_qry = "select preseref from presen";
//     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
    $cc = 0;
    $rs = $db->query($oci_qry);
    

    while ($ax = $rs->fetch_assoc()){
//        if(!is_numeric($row->WERKS)){
            $response['unimed'][]['uni'] = $ax['preseref'];
//        }
    }
    if(isset($response['unimed'])){
        $response["success"] = 1;
        echo json_encode($response);
    }else{
        $response["success"] = 0;
         $response['message'] = "Ninguna unidad de medida encontrada";
         echo json_encode($response);
    }
// include '/var/www/html/closeconn.php';
