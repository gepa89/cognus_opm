<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$data = array();
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$tipo = $_POST['impresion'];
$codalma = $_POST['codalma'];
//$sbNiv = array(0=>"-",1=>"A", 2=>"B", 3=>"C", 4=>"D", 5=>"E", 6=>"F", 7=>"G", 8=>"H");
$sbNiv = array('-','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//echo count($sbNiv);
switch($tipo){
    case 'reean':
        if($_POST['etNros'] != ''){
            $nros = explode(',',$_POST['etNros']);
            $inquery = '';
            foreach ($nros as $k => $v){
                $data[] = $v;
            }
//            $qry = "select * from etiquetas where etnum in (".$inquery.")";
//            $rs = $db->query($qry);
//            while($ax = $rs->fetch_assoc()){
//                $data[] = $ax['etnum'];
//            }
        }
        echo json_encode(array('data' => $data));
        break;
    case 'reex':
        if($_POST['etNros'] != ''){
            $nros = explode(',',$_POST['etNros']);
            $inquery = '';
            foreach ($nros as $k => $v){
                if(is_numeric($v)){
                    if($inquery == ''){
                       $inquery = "'".trim($v)."'"; 
                    }else{
                        $inquery .= ",'".trim($v)."'"; 
                    }
                }
            }
            $qry = "select * from etiquetas where etnum in (".$inquery.")";
            $rs = $db->query($qry);
            while($ax = $rs->fetch_assoc()){
                $data[] = $ax['etnum'];
            }
        }else if($_POST['etTipo'] != '' && (int)$_POST['etCantidad'] > 0){
            for($i = 0; $i < (int)$_POST['etCantidad']; $i++){
                $qr = "insert into etiquetas set ettip = '{$_POST['etTipo']}'";
//                echo $qr;
                $db->query($qr);
                $nr = (string)$db->insert_id;
                $data[] = $nr;
            }
        }
        echo json_encode(array('data' => $data));
        break;
    case 'reub':
        $sqHuec = $sqNiv = '';
        
        if($_POST['dEstante'] != ''){
            $fst = substr($_POST['dEstante'],0,1);
            
            $desdeEst = (int)substr($_POST['dEstante'],-3);
//            echo $desdeEst;
//            echo $desdeEst;
            if($_POST['hEstante'] != ''){
                $hastaEst = (int)substr($_POST['hEstante'],-3);
            }else{
                $hastaEst = (int)$desdeEst;
            }
        }
        if($_POST['dHueco'] != ''){
            $desdeHuec = (int)$_POST['dHueco'];
            if($_POST['hHueco'] != ''){
                $hastaHuec = (int)$_POST['hHueco'];                
                $sqHuec = " and CAST(ubihuec AS SIGNED INTEGER) between {$desdeHuec} and {$hastaHuec} ";
            }else{
                $hastaHuec = (int)$desdeHuec;
                $sqHuec = " and CAST(ubihuec AS SIGNED INTEGER) = {$desdeHuec}";
            }
        }
        if($_POST['dNiv'] != ''){
            $desdeNiv = (int)$_POST['dNiv'];
            if($_POST['hNiv'] != ''){
                $hastaNiv = (int)$_POST['hNiv'];
                $sqNiv = " and ubiniv between {$desdeNiv} and {$hastaNiv} ";
            }else{
                $hastaNiv = (int)$desdeNiv;
                $sqNiv = " and ubiniv =  {$desdeNiv}";
            }
        }
        $sbNivUs = array();
        if($_POST['dSubNiv'] != ''){
            $desdeSbNiv = (int)$_POST['dSubNiv'];
            if($_POST['hSubNiv'] != ''){
                $hastaSbNiv = (int)$_POST['hSubNiv'];
            }else{
                $hastaSbNiv = (int)$desdeSbNiv;
            }
        }
        array_push($sbNivUs, $sbNiv[0]);
        for($k = $desdeSbNiv; $k <= $hastaSbNiv; $k++){
            array_push($sbNivUs, $sbNiv[$k]);
        }
//        var_dump($sbNivUs);
        $mango = 0;
        $ubicss = '';
            $cc = 0;
        $data = array();
        for($i = $desdeEst; $i <= $hastaEst; $i++){
            $estante = $fst.str_pad($i, 3, '0', STR_PAD_LEFT);
            
            $sq = "select ubirefer from ubimapa where ubiestan = '{$estante}' {$sqHuec} {$sqNiv} and cod_alma='$codalma'";
//            echo $sq."<br/>";
            $rs = $db->query($sq);
            while($ax = $rs->fetch_assoc()){
                $ubifull = str_pad($ax['ubirefer'], 10, '-', STR_PAD_RIGHT);
                $lst = substr($ubifull, -1, 1);
                if(in_array($lst, $sbNivUs)){
                    $es = substr($ax['ubirefer'], 0, 4);
                    $hu = substr($ax['ubirefer'], 4, 3);
                    $ni = substr($ax['ubirefer'], 7);
                    $ubix = $es."-".$hu."-".$ni;//.$sbNiv[$l];
                    $data[$cc]['forcod'] = $ubifull;
                    $data[$cc]['forlbl'] = $ubix;
                    $cc++;                    
                }
            }
        }
//        $qry = "select * from ubimapa where ubirefer REGEXP '{$ubicss}'";
//        var_dump($data);
        echo json_encode(array('data' => $data));
        break;
}
