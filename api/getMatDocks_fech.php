<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$muelle = $_POST['muelle'];
//modo = 0 es muelle, modo = 1 es MR
$modo = $_POST['modo'];
$sqCnd = '';
if($modo == 0){
    $sqCnd = " and a.muelle = '{$muelle}'";
    $lblb = "muelle";
}else{
    $sqCnd = " and b.etnum = '{$muelle}'";
    $lblb = "Multi referencia";
}
//$response["error"] = TRUE;
//$response['mensaje'] = "Ningun Material ubicado en muelle. modo ".$modo.", valor:".$muelle;
     $response = array();
//     $query = "select * from prt_users where prt_user = '{$_POST['user']}' and prt_psw = '{$_POST['pass']}'";
     $qry = "SELECT distinct a.muelle, b.artrefer, b.canti, c.artdesc FROM pedmuelle a 
	inner join stockubi b on a.muelle = b.ubirefer 
	inner join arti c on b.artrefer = c.artrefer
	where 1=1 {$sqCnd} and a.pedstatus = 0";
//        echo $qry;
//     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
//    $cc = 0;
    $r1 = $db->query($qry);
    $response['materiales'][]['material'] = 'Todo';
    while ($row = $r1->fetch_assoc()){
        $sql = "select distinct * from artubiref where artrefer = '{$row['artrefer']}'";
        $rx = $db->query($sql);
        while($ax = $rx->fetch_assoc()){   
            if(!in_array($ax['ubirefer'], $response['ubicaciones'][$row['artrefer']][$ax['ubitipo']])){                
                $response['ubicaciones'][$row['artrefer']][$ax['ubitipo']] = separateLocation($ax['ubirefer']); 
            }
        }
        $response['materiales'][]['material'] = $row['artrefer'] .' - '.$row['artdesc']; 
    }
    if(isset($response['materiales']) && count($response['materiales']) > 1){
        $response["error"] = FALSE;
        $response['mensaje'] = '';
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Ningun Material ubicado en ".$lblb;
    }
  
//$response["error"] = TRUE;
//$response['mensaje'] = "Ningun Material ubicado en muelle. modo ".$modo.", valor:".$muelle." qru ".$qry;
       echo json_encode($response);
 include '/var/www/html/closeconn.php';
