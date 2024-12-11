<?php

require ('../conect.php');
$cod_alma = $_POST['codalma'];
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$sq = "select * from config WHERE cod_alma = '$cod_alma' limit 1";
$rs = $db->query($sq);
$data = $rs->fetch_assoc();
echo json_encode(array('asig' => $data['asig'],'ruta' => $data['ruta'],'lectura' => $data['lectura'],'codalma' => $data['cod_alma'],'estaparam' => $data['estaparam']));
