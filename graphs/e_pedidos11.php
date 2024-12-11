<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$tt = 0;
$t_proc = 0;
$procesado = 0;
$sq = "SELECT COUNT(pedexcab.siturefe) as TotalPedalm, siturefe
        FROM pedexcab
        WHERE pedclase <>'REPO'
        AND pedexfec =date(NOW())/*fecha del dia*/ 
        GROUP BY siturefe";
 
$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";
while($ax = $rs->fetch_assoc()){
    $tt += $ax['TotalPedalm'];
    $data['name']=getLabel($ax['siturefe']).': '.$ax['TotalPedalm'];
    $data['y']=$ax['TotalPedalm'];
//    array_push($datax,$data);
    $datax[getLabel($ax['siturefe'])] = $ax['TotalPedalm'];
    if($series != ''){
        $series .= ",{name:'".getLabel($ax['siturefe']).": ".$ax['TotalPedalm']."', y: ".$ax['TotalPedalm']."}";
    }else{
        $series = "{name:'".getLabel($ax['siturefe']).": ".$ax['TotalPedalm']."', y: ".$ax['TotalPedalm']."}";
    }
            
}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser'=> $series));
