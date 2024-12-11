<?php

require ('../conect.php');

$art = trim(strtoupper($_POST['mat']));
$artdx = $_POST['desc'];
$codalma = $_POST['codalma'];

$db = new mysqli($SERVER,$USER,$PASS,$DB);
$sq = "SELECT 
    arti.artrefer,
    arti.artdesc,
    arti.artrot,
    arti.artean,
    arti.artgrup,
    arti.artser,
    arti.almcod,
    arti.unimed
from arti
WHERE arti.artrefer = '{$art}'";
//echo $sq;
$rs = $db->query($sq);
$data = array();

while($ax = $rs->fetch_assoc()){
    $data[] = $ax;
}

if(count($data) > 0){
    $err = FALSE;
    $msg = "";
}else{
    $err = TRUE;
    $msg = "Favor ingresar Código de Material";
}

echo json_encode(array( 'dat' => $data,'err' => $err,'msg' => $msg));

exit();
