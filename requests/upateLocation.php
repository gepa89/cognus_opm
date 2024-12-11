<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante

$estHash = $_POST["a_hash"];
$estante = strtoupper($_POST["a_cod_estanteria"]);
$codalma = strtoupper($_POST["codalma"]);
$estdes = str_pad($_POST["a_c_desde"], 4, '0',STR_PAD_LEFT);
$esthas = str_pad($_POST["a_c_hasta"], 4, '0',STR_PAD_LEFT);;
$estniv = $_POST["a_nivel"];



$alp = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$almacenaje = $_POST["almacenaje"];
$picking = $_POST["picking"];
$subniveles = $_POST["subniveles"];
$clases = $_POST["clases"];
for($i = (int)$estdes; $i <= (int)$esthas; $i++){
    for($j = 1; $j <= (int)$estniv; $j++){
        $pasillo[$i][$j] = array();
    }
}
//echo "<pre>";var_dump(($clases));echo "</pre>";
$flgFrst = 0;
foreach($subniveles as $k => $v){
    if(!empty($v)){
        for($i = (int)$v[0]; $i <= (int)$v[2]; $i++){
        //    echo $i;
            if($flgFrst == 0){
                for($j = 1; $j <= (int)$estniv; $j++){
                    $pasillo[$i][$j][1]['cod'] = $estante."".str_pad(substr($i, -3), 3, '0',STR_PAD_LEFT)."". str_pad($j, 2, '0',STR_PAD_LEFT)."";
                    $pasillo[$i][$j][1]['subn'] = "";
                }
                $flgFrst = 1;
            }
            for($j = (int)$v[1]; $j <= (int)$v[3]; $j++){
                for($k = 1; $k <= (int)$v[4]; $k++){
//                    echo "<pre>";var_dump($estante." ".substr($i, -3)." ". str_pad($j, 2, '0',STR_PAD_LEFT)." ".$alp[$k-1]); echo "</pre>";
//                    echo ;
                    $pasillo[$i][$j][$k]['cod'] = $estante."".str_pad(substr($i, -3), 3, '0',STR_PAD_LEFT)."". str_pad($j, 2, '0',STR_PAD_LEFT)."".$alp[$k];
                    $pasillo[$i][$j][$k]['subn'] = $alp[$k];
                    
//                    echo "<pre>";var_dump($k); echo "</pre>";
//                    echo "<pre>";var_dump($alp[$k]); echo "</pre>";
//                    echo "<pre>";var_dump($pasillo[$i][$j]); echo "</pre>";
//                    echo "<pre> ____________________________________________________________ </pre>";
                }
            }
        }
    }
}
//echo "<pre>";var_dump($pasillo);echo "</pre>";
foreach($clases as $k => $v){
    if(!empty($v)){
        for($i = (int)$v[0]; $i <= (int)$v[2]; $i++){
        //    echo $i;
            for($j = (int)$v[1]; $j <= (int)$v[3]; $j++){
                foreach($pasillo[$i][$j] as $ind => $hole){
                    $pasillo[$i][$j][$ind]['typ'] = $v[4];
                    $pasillo[$i][$j][$ind]['ubi'] = $v[5];
                    $pasillo[$i][$j][$ind]['dim'] = $v[6];
                }
            }
        }
    }
}
foreach($almacenaje as $k => $v){
    if(!empty($v)){
        for($i = (int)$v[0]; $i <= (int)$v[2]; $i++){
        //    echo $i;
            for($j = (int)$v[1]; $j <= (int)$v[3]; $j++){
                foreach($pasillo[$i][$j] as $ind => $hole){
                    $pasillo[$i][$j][$ind]['alm'] = $v[4];         
                }
            }
        }
    }
}
foreach($picking as $k => $v){
    if(!empty($v)){
        for($i = (int)$v[0]; $i <= (int)$v[2]; $i++){
        //    echo $i;
            for($j = (int)$v[1]; $j <= (int)$v[3]; $j++){
                foreach($pasillo[$i][$j] as $ind => $hole){
                    $pasillo[$i][$j][$ind]['pick'] = $v[4];
                }
            }
        }
    }
}
//echo "<pre>";var_dump(($pasillo));echo "</pre>";
function has_empty(array $array)
{
    return count($array) != count(array_diff($array, array('', null, array())));
}
$sel = "select * from ubica where ubcod = '{$estante}' and cod_alma = '{$codalma}'";
$rs = $db->query($sel);
if($rs->num_rows > 0){
    //Borro todo lo que hay fuera de la tabla ubica


    $sqdel1 = "delete from estalm where almest = '{$estHash}'";
    $sqdel2 = "delete from estpic where picest = '{$estHash}'";
    $sqdel3 = "delete from estsniv where snivest = '{$estHash}'";
    $sqdel4 = "delete from estcla where claest = '{$estHash}'";

//    echo $sqdel1;
    $db->query($sqdel1);
    $db->query($sqdel2);
    $db->query($sqdel3);
    $db->query($sqdel4);
    $insSq = "update ubica set 
        ubcod = '{$estante}',
   
        ubdes = '{$estdes}',
        ubhas = '{$esthas}',
        ubniv = '{$estniv}'
        where ubest = '{$estHash}'
        ";
//        echo $insSq;
    if($db->query($insSq)){
        $sqInsAlm = "insert into estalm (almest,cod_alma,almdes,almhas,almndes,almnhas,almzon,almts,almusr) values ";
        $cc = 0;
        foreach($almacenaje as $k => $v){
//            echo "<pre>";var_dump(($pasillo));echo "</pre>";
//            var_dump(has_empty($v));
//            $sqInsAlm = "insert into estalm (almest,almdes,almhas,almndes,almnhas,almzon,almts,almusr) values ";
            if(!has_empty($v)){
                if($cc == 0){
                    $sqInsAlm .= "('{$estHash}','{$codalma}','".$v[0]."','".$v[2]."','".$v[1]."','".$v[3]."','".$v[4]."',now(),1)";
                }else{
                    $sqInsAlm .= ",('{$estHash}','{$codalma}','".$v[0]."','".$v[2]."','".$v[1]."','".$v[3]."','".$v[4]."',now(),1)";
                }
                $cc++;
            }
            
        }
        if($db->query($sqInsAlm)){
            $sqInsPic = "insert into estpic (picdes,picndes,pichas,picnhas,piczon,picts,picusr,picest,cod_alma) values ";
            $cc = 0;
            foreach($picking as $k => $v){
    //            $sqInsPic = "insert into estpic (picdes,picndes,pichas,picnhas,piczon,picts,picusr,picest) values ";
                
                if(!has_empty($v)){
                    if($cc == 0){
                        $sqInsPic .= "('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."',now(),1,'{$estHash}','{$codalma}')";
                    }else{
                        $sqInsPic .= ",('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."',now(),1,'{$estHash}','{$codalma}')";
                    }
                    $cc++;
                }
                
            }
            if($db->query($sqInsPic)){
                $sqInsSN = "insert into estsniv (snivdes,snivndes,snivhas,snivnhas,snivsub,snivts,snivusr,snivest,cod_alma) values ";
                $cc = 0;
                foreach($subniveles as $k => $v){
        //            $sqInsPic = "insert into estpic (picdes,picndes,pichas,picnhas,piczon,picts,picusr,picest) values ";
                    if(!has_empty($v)){
                        if($cc == 0){
                            $sqInsSN .= "('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."',now(),1,'{$estHash}','{$codalma}')";
                        }else{
                            $sqInsSN .= ",('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."',now(),1,'{$estHash}','{$codalma}')";
                        }
                        $cc++;
                    }
                    
                }
                if($db->query($sqInsSN)){
                    $sqInsCla = "insert into estcla (clades,clandes,clahas,clanhas,clacla,tipoubi,dimension,clats,clausr,claest,cod_alma) values ";
                    $cc = 0;
                    foreach($clases as $k => $v){
                        $tipoubi = $d3['ubi'];
                        $dimension = $d3['dim'];
            //            $sqInsPic = "insert into estpic (picdes,picndes,pichas,picnhas,piczon,picts,picusr,picest) values ";
                        if(!has_empty($v)){
                            if($cc == 0){
                                $sqInsCla .= "('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."','".$v[5]."','".$v[6]."',now(),1,'{$estHash}','{$codalma}')";
                            }else{
                                $sqInsCla .= ",('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."','".$v[5]."','".$v[6]."',now(),1,'{$estHash}','{$codalma}')";
                            }
                            $cc++;
                        }
                        
                    }
//                    echo $sqInsCla;
                    if($db->query($sqInsCla)){
//                        guardo las ubicaciones
                        $sq = "delete from ubimapa where ubihash = '{$estHash}' and cod_alma = '{$codalma}'";
                        if($db->query($sq)){
//                            var_dump($pasillo);
                            foreach($pasillo as $est => $d1){
                                foreach($d1 as $hue => $d2){
                                    foreach($d2 as $niv => $d3){
                                        $tipoubi = $d3['ubi'];
                                        $dimension = $d3['dim'];
                                        if($d3["typ"] != 'NE' && $d3["typ"] != ''){
                                            if(key_exists("alm", $d3) && $d3["alm"] != ''){
                                                $alm = $d3["alm"];
                                            }else{
                                                $alm = '';
                                            }
                                            if(key_exists("pick", $d3) && $d3["pick"] != ''){
                                                $pick = $d3["pick"];
                                            }else{
                                                $pick = '';
                                            }
                                            $sq =" insert into ubimapa set 
                                                cod_alma = '{$codalma}',
                                                ubiestan = '{$estante}',
                                                ubihuec = '".str_pad(substr($est, -3), 3, '0',STR_PAD_LEFT)."',
                                                ubiniv = '".str_pad($hue, 2, '0',STR_PAD_LEFT)."',
                                                zoncodpre = '{$pick}',
                                                zoncodalm = '{$alm}',
                                                ubitipo = '{$d3["typ"]}',
                                                ubiestad = 'OK',
                                                ubisitu = 'VA',
                                                ubihash = '{$estHash}',
                                                ubirefer = '{$d3["cod"]}',
                                                tipoubi = '$tipoubi',
                                                dimension = '$dimension',     
                                                fecrea = now(),
                                                almrefer = '1'";
//                                                echo $sq;
                                            $db->query($sq);
                                        }
                                    }
                                }
                            }
                            $err = 0;
                            $msg = 'Datos guardados correctamente.';  
                        }
                    }else{
                        $err = 1;
                        $msg = 'Error al crear clases.';  
                    }
                }else{
                    $err = 1;
                    $msg = 'Error al crear clases.';  
                }
            }else{
                $err = 1;
                $msg = 'Error al crear sub niveles.';  
            }
        }else{
            $err = 1;
            $msg = 'Error al crear picking.';  
        }
    }else{
        $err = 1;
        $msg = 'Error al crear estante.';  
    }
}else{
    $err = 1;
    $msg = 'Codigo de estante no existe.';
}

echo json_encode(array('msg' => $msg, 'err' => $err));
exit();
