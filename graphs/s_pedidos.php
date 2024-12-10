<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$tt = 0;
$t_proc = 0;
$procesado = 0;
$sq = "SELECT COUNT(pedexcab.siturefe) as TotalPedidos, siturefe
        FROM pedexcab
        WHERE pedclase <>'REPO'
        AND pedexfec =date(NOW())/*fecha del dia*/ 
        GROUP BY siturefe";
 
$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";
while($ax = $rs->fetch_assoc()){
    $tt += $ax['TotalPedidos'];
    $data['name']=getLabel($ax['siturefe']).': '.$ax['TotalPedidos'];
    $data['y']=$ax['TotalPedidos'];
//    array_push($datax,$data);
    $datax[getLabel($ax['siturefe'])] = $ax['TotalPedidos'];
    if($series != ''){
        $series .= ",{name:'".getLabel($ax['siturefe']).": ".$ax['TotalPedidos']."', y: ".$ax['TotalPedidos']."}";
    }else{
        $series = "{name:'".getLabel($ax['siturefe']).": ".$ax['TotalPedidos']."', y: ".$ax['TotalPedidos']."}";
    }
            
}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser'=> $series));
