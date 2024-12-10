<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);
$db->set_charset("utf8");
$response = array();
//<option value="">Seleccionar</option>
//<option value="B1">EAN</option>
//<option value="B2">Ubicación</option>
//<option value="B3">EAN / Ubicación</option>
$sqr = "select lectura from config";
$rr = $db->query($sqr);
$rrx = $rr->fetch_assoc();
$sq = "select * from ped_multiref where terminal = '{$terminal}' and codst = 0";
//     echo $sq;
$cc = 0;
$r1 = $db->query($sq);

    $cc = 0;
        $ser = '';
        
        
$sqz ="select terzonpre from termi where tercod = '{$terminal}' limit 1";
//echo $sqz;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();


while($ax = $r1->fetch_assoc()){
//    por cada pedido para preparar, traigo sus materiales
    $fstData[$ax['pedido']]['pedido'] = $ax['pedido'];
    $fstData[$ax['pedido']]['multiref'] = $ax['multiref'];
    $sq2 = "select distinct
                a.artrefer, 
                a.canpedi, 
                a.canprepa, 
                b.artdesc,
                b.artser,
                (select GROUP_CONCAT(ean) as eans from artean c where c.artrefer = a.artrefer GROUP BY c.artrefer) as ean,
                #d.ubirefer,
								d.zoncodpre,
								e.canpresen,
                                                                e.preseref
                from pedexdet a 
                inner join arti b on a.artrefer = b.artrefer
								inner join stockubi c on a.artrefer = c.artrefer
								inner join ubimapa d on c.ubirefer = d.ubirefer                                                                
								left join artipresen e on a.artrefer = e.artrefer
                where a.pedexentre = '{$ax['pedido']}' and d.zoncodpre = '{$rszz['terzonpre']}'  and a.canprepa < a.canpedi and expst = 0";
//                echo $sq2;
    $rs = $db->query($sq2);
    
    while($ax2 = $rs->fetch_assoc()){
        $fstData[$ax['pedido']]['cantsku']++;
        $ax2['artrefer'] = $db->real_escape_string($ax2['artrefer']);
        $sqMin = "select artminve from artinve where artrefer = '{$ax2['artrefer']}' limit 1";
        $rxMin = $db->query($sqMin);
        $minAr = $rxMin->fetch_assoc();
        
        
        
        if($ax2['artser'] == 'ZCHA'){
//            echo 'entro '.$ax['artser'];
            for($i=0; $i < (int)$ax2['canpedi']; $i++){      
                $sqser = "SELECT a.ubirefer, a.artrefer, b.artserie FROM stockubi a 
                    left join serati b on a.artrefer =  b.artrefer and a.ubirefer = b.serubic
                    inner join ubimapa c on a.ubirefer = c.ubirefer and c.ubitipo = 'PI'
                    where 
                    a.artrefer = '{$ax2['artrefer']}' 
                        and b.sersitu <> 1  
                        and b.serprep <> 1
                    order by a.ubirefer asc, b.artmes asc, b.artanio asc limit 1";
//                        echo $sqser;
                $rser = $db->query($sqser);
                if($rser->num_rows > 0){
                    $xser = $rser->fetch_assoc();
                    if($ser != ''){
                        $ser .= ",'".$xser['artserie']."'";
                    }else{
                        $ser = "'".$xser['artserie']."'";
                    }
                    
                    $fstDataMat[$cc]['serie'] = $xser['artserie'];
                    $locAux = $xser['ubirefer'];
                    $fstDataMat[$cc]['ubirefer'] = separateLocation($xser['ubirefer']);
                    $sq = "update serati set sersitu = 1 where artserie = '{$xser['artserie']}'";
                    $db->query($sq);
                }
                $sqUbi = "select sum(canti) as tot from stockubi 
                        where artrefer = '{$ax2['artrefer']}' 
                        and ubirefer = '{$locAux}' group by artrefer, ubirefer";
                $rxUbi = $db->query($sqUbi);
                $ubiAr = $rxUbi->fetch_assoc();
                $fstDataMat[$cc]['stock'] = (int)$ubiAr['tot'];
                $fstDataMat[$cc]['tipo'] = $ax2['artser'];
                $fstDataMat[$cc]['pedido'] = $ax['pedido'];
                $fstDataMat[$cc]['ean'] = $ax2['ean'];
                $fstDataMat[$cc]['presen'] = $ax2['canpresen']. "x(".$ax2['preseref'].")";
                $fstDataMat[$cc]['artrefer'] = $ax2['artrefer'];
                $fstDataMat[$cc]['canpedi'] = 1;
                $fstDataMat[$cc]['artdesc'] = $ax2['artdesc'];
                $cc++;  
            }
        }else{
        
            $pendPrep = $ax2['canpedi']-$ax2['canprepa'];//lo que falta preparar
//            echo $ax2['preseref'];
            if($ax2['preseref'] != 'UNI'){
                $pendXPresen = intval($pendPrep / $ax2['canpresen']);
//                echo $pendXPresen;
                $pendXPresenXUni = $pendXPresen * $ax2['canpresen'];
                $pendXUni = abs($pendPrep % $ax2['canpresen']);
            }else{
                $pendXPresen = 0;
                $pendXUni = $pendPrep;
//                echo    $pendPrep;
            }
            
//            echo $pendXPresen."   ";

            
            if($pendXPresen > 0){
                $sqdu = "select * from (
                            select distinct 
                            a.ubirefer,a.artrefer, b.ubitipo, sum(a.canti) as stock
                            from stockubi a 
                            inner join ubimapa b on a.ubirefer = b.ubirefer 
                            where 
                            a.artrefer = '{$ax2['artrefer']}' and 
                            b.ubitipo = 'RE' 
                            GROUP BY a.ubirefer, a.artrefer, b.ubitipo 
                            ) as locx where locx.stock >= {$pendXPresenXUni} limit 1";
                $rser = $db->query($sqdu);
                if($rser->num_rows > 0){                
                    $xser = $rser->fetch_assoc();
                    $locAux = $xser['ubirefer'];
                    $fstDataMat[$cc]['ubirefer'] = separateLocation($xser['ubirefer']);//$xser['ubirefer'];
                    if(isset($minAr['artminve'])){
                        $fstDataMat[$cc]['minimo'] = $minAr['artminve'];
                    }
                    if(!$fstDataMat[$cc]['minimo']){
                        $fstDataMat[$cc]['minimo'] = 0;
                    }
                    $sqUbi = "select sum(canti) as tot
                        from stockubi 
                        where artrefer = '{$ax2['artrefer']}' 
                            and ubirefer = '{$locAux}' group by artrefer, ubirefer";

                    $rxUbi = $db->query($sqUbi);
                    $ubiAr = $rxUbi->fetch_assoc();
                    $fstDataMat[$cc]['stock'] = $ubiAr['tot'];
                    $fstDataMat[$cc]['tipo'] = ''.$ax2['artser'];
                    $fstDataMat[$cc]['serie'] = '';
                    $fstDataMat[$cc]['ean'] = $ax2['ean'];
                    $fstDataMat[$cc]['pedido'] = $ax['pedido'];
                    $fstDataMat[$cc]['artrefer'] = $ax2['artrefer'];
                    $fstDataMat[$cc]['canpedi'] = $pendXPresen;
                    $fstDataMat[$cc]['canpendi'] = $pendPrep;

                    $fstDataMat[$cc]['presenref'] = $ax2['preseref'];
                    $fstDataMat[$cc]['presenmul'] = $ax2['canpresen'];
                    $fstDataMat[$cc]['presen'] = $ax2['canpresen']. "x".$ax2['preseref'];
                    $fstDataMat[$cc]['artdesc'] = $ax2['artdesc'];
                    $cc++;
                }
            }
            if($pendXUni > 0){
                $sqdu = "select * from (
                            select distinct 
                            a.ubirefer,a.artrefer, b.ubitipo, sum(a.canti) as stock
                            from stockubi a 
                            inner join ubimapa b on a.ubirefer = b.ubirefer 
                            where 
                            a.artrefer = '{$ax2['artrefer']}' and 
                            b.ubitipo = 'PI' 
                            GROUP BY a.ubirefer, a.artrefer, b.ubitipo 
                            ) as locx where locx.stock >= {$pendXPresen} order by locx.ubirefer asc limit 1";
                $rser = $db->query($sqdu);
                if($rser->num_rows > 0){                
                    $xser = $rser->fetch_assoc();
                    $locAux = $xser['ubirefer'];
                    $fstDataMat[$cc]['ubirefer'] = separateLocation($xser['ubirefer']);//$xser['ubirefer'];
                    
                    if(isset($minAr['artminve'])){
                        $fstDataMat[$cc]['minimo'] = $minAr['artminve'];
                    }
                    if(!$fstDataMat[$cc]['minimo']){
                        $fstDataMat[$cc]['minimo'] = 0;
                    }
                    $sqUbi = "select sum(canti) as tot
                        from stockubi 
                        where artrefer = '{$ax2['artrefer']}' 
                            and ubirefer = '{$locAux}' group by artrefer, ubirefer";

                    $rxUbi = $db->query($sqUbi);
                    $ubiAr = $rxUbi->fetch_assoc();
                    $fstDataMat[$cc]['stock'] = $ubiAr['tot'];
                    $fstDataMat[$cc]['tipo'] = ''.$ax2['artser'];
                    $fstDataMat[$cc]['serie'] = '';
                    $fstDataMat[$cc]['ean'] = $ax2['ean'];
                    $fstDataMat[$cc]['pedido'] = $ax['pedido'];
                    $fstDataMat[$cc]['artrefer'] = $ax2['artrefer'];
                    $fstDataMat[$cc]['canpedi'] = $pendXUni;
                    $fstDataMat[$cc]['canpendi'] = $pendPrep;

                    $fstDataMat[$cc]['presenref'] = 'UN';
                    $fstDataMat[$cc]['presenmul'] = 1;
                    $fstDataMat[$cc]['presen'] = 'UN';
                    $fstDataMat[$cc]['artdesc'] = $ax2['artdesc'];
                    $cc++;
                }
            }
            
            
        }
        
        
    }
    
}
//echo "<pre>";var_dump($fstDataMat);echo "</pre>";
$sq = "update serati set sersitu = 0 where artserie in ({$ser})";
$db->query($sq);
$sq = "select * from ruta_ubic";
$rubi = $db->query($sq);
while($asx = $rubi->fetch_assoc()){
    $ubica[$asx['orden']] = $asx['ubirefer'];
}
//var_dump($fstDataMat);
foreach($fstDataMat as $k => $dat){
    $key = array_search ($dat['ubirefer'], $ubica);
    $data[$key][] = $dat;
}
ksort($data);
$ak = 0;
$data2 = array();
foreach($data as $k => $dat){
    foreach($dat as $v => $dat2){
        $dat2['cantsku'] = $fstData[$dat2['pedido']]['cantsku'];
        $dat2['multiref'] = $fstData[$dat2['pedido']]['multiref'];
        $data2[] = $dat2;
    }   
}    
if(count($data) > 0){
    $response['ruta'] = $data2;
    $response["error"] = FALSE;
}else{
   $response["error"] = TRUE;
   $response['mensaje'] = "Sin pedidos procesables.";
} 
    
echo json_encode($response);
