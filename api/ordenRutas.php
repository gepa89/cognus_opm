<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$response = array();
$sqr = "select rutcod from ruta where rutesta = 1";
//echo $sqr;
$rr = $db->query($sqr);
$rrx = $rr->fetch_assoc();
$alp = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$sq = "truncate table ruta_ubic";
        $db->query($sq);

$sqd = "select * from rutadet where rutcod = {$rrx['rutcod']} order by rutid asc";
//echo $sqd;
$rs = $db->query($sqd);
while($rsx = $rs->fetch_assoc()){
    switch($rsx['rutip']){
        case 'P':
//            Estante 1
            $est1 = $rsx['ubiestan'];
            $est2 = $rsx['ubiestanh'];
            
            if((int)$rsx['ubihuec'] < (int)$rsx['ubihuech']){                
                for($i = (int)$rsx['ubihuec']; $i <= (int)$rsx['ubihuech']; $i++){
                    for($j = (int)$rsx['ubiniv']; $j <= (int)$rsx['ubinivh']; $j++){
                        $ubi[] = $est1.str_pad($i, 3,'0',STR_PAD_LEFT).str_pad($j, 2,'0',STR_PAD_LEFT);
                    }
                    for($j = (int)$rsx['ubiniv']; $j <= (int)$rsx['ubinivh']; $j++){
                        $ubi[] = $est2.str_pad($i, 3,'0',STR_PAD_LEFT).str_pad($j, 2,'0',STR_PAD_LEFT);
                    }
                }
            }else{
                for($i = (int)$rsx['ubihuec']; $i >= (int)$rsx['ubihuech']; $i--){
//                    echo $i;
                    for($j = (int)$rsx['ubiniv']; $j <= (int)$rsx['ubinivh']; $j++){
                        $ubi[] = $est1.str_pad($i, 3,'0',STR_PAD_LEFT).str_pad($j, 2,'0',STR_PAD_LEFT);
                    }
                    for($j = (int)$rsx['ubiniv']; $j <= (int)$rsx['ubinivh']; $j++){
                        $ubi[] = $est2.str_pad($i, 3,'0',STR_PAD_LEFT).str_pad($j, 2,'0',STR_PAD_LEFT);
                    }
                }
            }
            break;
    }
}
    $cc = 1;
foreach($ubi as $k => $v){
    $sel = "select ubirefer from ubimapa where ubirefer like '{$v}%'";
    $rx = $db->query($sel);
    while($aux = $rx->fetch_assoc()){
         $sq = "insert into ruta_ubic set ubirefer = '{$aux['ubirefer']}', orden = {$cc}";
        if($db->query($sq)){

            $cc++;
        }
    }
}






































