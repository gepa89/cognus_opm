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
$sq = "SELECT COUNT(pedrecab.pedresitu) as TotalPedComraRec, pedresitu
        FROM pedrecab
        WHERE pedclase NOT in ('EUB','ZUB')
        $filtro_almacen
        AND pedrefeclle =date(NOW())/* fecha del dia*/
        GROUP BY pedresitu";

$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";
while($ax = $rs->fetch_assoc()){
    $tt += $ax['TotalPedComraRec'];
    $data['name']=getLabel($ax['pedresitu']).': '.$ax['TotalPedComraRec'];
    $data['y']=$ax['TotalPedComraRec'];
//    array_push($datax,$data);
    $datax[getLabel($ax['pedresitu'])] = $ax['TotalPedComraRec'];
    if($series != ''){
        $series .= ",{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalPedComraRec']."', y: ".$ax['TotalPedComraRec']."}";
    }else{
        $series = "{name:'".getLabel($ax['pedresitu']).": ".$ax['TotalPedComraRec']."', y: ".$ax['TotalPedComraRec']."}";
    }
            
}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser'=> $series));
