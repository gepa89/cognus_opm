<?php
require('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante

$response = array();
//     $query = "select * from prt_users where prt_user = '{$_POST['user']}' and prt_psw = '{$_POST['pass']}'";
$qry = "SELECT distinct ubirefer FROM ubimapa where ubitipo like 'MR' and ubisitu = 'VA'";
//     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
$cc = 0;
$r1 = $db->query($qry);

while ($row = $r1->fetch_assoc()) {
     $response['muelles'][]['muelle'] = $row['ubirefer'];
}
if (isset($response['muelles'])) {
     $response["success"] = 1;
} else {
     $response["success"] = 0;
     $response['message'] = "Ningun muelle encontrado";
}

echo json_encode($response);
include '/var/www/html/closeconn.php';