<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$cod = $_POST['id'];
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//var_dump($_POST['ckAsig']);
$sq = "select * from rutadet where rutcod = {$cod}"; 
//echo $sq;
$rs = $db->query($sq);
$cc = 0;
//CODIGO: "1"
//MANDT: "300"
//TIPO: "IAC"
//DETALLES: "test1"
//TAMANO: "XL"
//COSTO: "2134" 
//TOTAL: "12346"
//CANTIDAD: "2"
//CECO: ""
//USUARIO: "CARTAMKE"
//FECHA: "20200204"
//HORA: "143808"
//LINE_INDEX: ""
$data = $datx = array();
while($ax = $rs->fetch_assoc()){
    $datx = array();
    array_push($datx,$ax['ubiestan']);
    array_push($datx,$ax['ubihuec']);
    array_push($datx,$ax['ubiniv']);
    array_push($datx,$ax['ubiestanh']);
    array_push($datx,$ax['ubihuech']);
    array_push($datx,$ax['ubinivh']);
    array_push($datx,$ax['cod_alma']);
    array_push($datx,$ax['rutip']);
    array_push($datx,$ax['ubisent']);
    array_push($data,$datx);;
}

echo json_encode(array('dat' => $data,'total' => count($data)));
