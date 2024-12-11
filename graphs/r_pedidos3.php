<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$tt = 0;
$t_proc = 0;
$procesado = 0;
$filtro_almacen = '';
if (!empty($_GET['almacen'])) {
    $filtro_almacen = " AND almrefer =  '" . $_GET['almacen']."'";
};
$sq = "SELECT COUNT(pedrecab.pedresitu) as TotalTransfRec, pedresitu
FROM pedrecab
WHERE pedclase in ('EUB','ZUB')
$filtro_almacen
AND pedrefeclle =date(NOW())
GROUP BY pedresitu";

$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";

while($ax = $rs->fetch_assoc()){
    $tt += $ax['TotalTransfRec'];
    $data['name']=getLabel($ax['pedresitu']).': '.$ax['TotalTransfRec'];
    $data['y']=$ax['TotalTransfRec'];
//    array_push($datax,$data);
    $datax[getLabel($ax['pedresitu'])] = $ax['TotalTransfRec'];
    if($series != ''){
        $series .= ",{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalTransfRec']."', y: ".$ax['TotalTransfRec']."}";
    }else{
        $series = "{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalTransfRec']."', y: ".$ax['TotalTransfRec']."}";
    }
            
}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser'=> $series));
