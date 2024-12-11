<?php
require('../conect.php');
require("../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$muelle = $_POST['mref'];
$response = array();
//     $query = "select * from prt_users where prt_user = '{$_POST['user']}' and prt_psw = '{$_POST['pass']}'";
$qry = "select a.etnum from etiquetas a where a.ettip = 'RE' and a.etnum = '{$muelle}'";
//     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
$cc = 0;
$r1 = $db->query($qry);

$response['error'] = $r1->num_rows == 0;
$response['mensaje'] = $r1->num_rows > 0 ? '' : 'Multireferencia no existe.';
$response['mref'] = $muelle;

echo json_encode($response);
include '/var/www/html/closeconn.php';
