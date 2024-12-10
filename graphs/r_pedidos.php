<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$tt = 0;
$t_proc = 0;
$procesado = 0;
$filtro_almacen = "";
if (!empty($_GET['almacen'])) {
    $filtro_almacen = " AND almrefer =  '" . $_GET['almacen']."'";
};
$sq = "SELECT COUNT(pedrecab.pedresitu) as TotalPedRec, pedresitu
        FROM pedrecab
        WHERE pedrefeclle =date(NOW())
        $filtro_almacen
        GROUP BY pedresitu";

$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";
while($ax = $rs->fetch_assoc()){
    $tt += $ax['TotalPedRec'];
    $data['name']=getLabel($ax['pedresitu']).': '.$ax['TotalPedRec'];
    $data['y']=$ax['TotalPedRec'];
//    array_push($datax,$data);
    $datax[getLabel($ax['pedresitu'])] = $ax['TotalPedRec'];
    if($series != ''){
        $series .= ",{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalPedRec']."', y: ".$ax['TotalPedRec']."}";
    }else{
        $series = "{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalPedRec']."', y: ".$ax['TotalPedRec']."}";
    }
            
}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser'=> $series));
