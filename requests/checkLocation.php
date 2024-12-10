<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$col = trim(preg_replace('/[0-9]+/', '', $_POST['cell']));
$row = $_POST['row'];
//var_dump($row);
switch ($col){
    case "A":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubiestan from ubimapa where ubiestan = '{$est}'";
//        echo $sq;
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            
            $exist = 0;
            $msg = 'Estante no existe';
        }
        $dt = $rs->fetch_assoc();
        echo json_encode(array('dat' => ''.@$dt['ubiestan'],'res' => $exist, 'msg' => $msg));
        break;
    case "B":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
        $huec = str_pad($row[1], 3, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubihuec from ubimapa where ubiestan = '{$est}' and ubihuec = '{$huec}'";
//        echo $sq;
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            $exist = 0;
            $msg = 'Hueco no existe';
        }
            $dt=$rs->fetch_assoc();
        echo json_encode(array('dat' => ''.@$dt['ubihuec'],'res' => $exist, 'msg' => $msg));
        break;
    case "C":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
        $huec = str_pad($row[1], 3, '0', STR_PAD_LEFT);
        $niv = str_pad($row[2], 2, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubiniv from ubimapa where ubiestan = '{$est}' and ubihuec = '{$huec}' and ubiniv = '{$niv}'";
//        echo $sq;
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            $exist = 0;
            $msg = 'Nivel no existe';
        }
            $dt=$rs->fetch_assoc();
        echo json_encode(array('dat' => ''.@$dt['ubiniv'],'res' => $exist, 'msg' => $msg));
        break;
    case "D":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubiestan from ubimapa where ubiestan = '{$est}'";
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            $exist = 0;
            $msg = 'Estante no existe';
        }
            $dt=$rs->fetch_assoc();
        echo json_encode(array('dat' => ''.@$dt['ubiestan'],'res' => $exist, 'msg' => $msg));
        break;
    case "E":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
        $huec = str_pad($row[1], 3, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubihuec from ubimapa where ubiestan = '{$est}' and ubihuec = '{$huec}'";
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            $exist = 0;
            $msg = 'Hueco no existe';
        }
            $dt=$rs->fetch_assoc();
        echo json_encode(array('dat' => ''.$dt['ubihuec'],'res' => $exist, 'msg' => $msg));
        break;
    case "F":
        $est = str_pad($row[0], 4, '0', STR_PAD_LEFT);
        $huec = str_pad($row[1], 3, '0', STR_PAD_LEFT);
        $niv = str_pad($row[2], 2, '0', STR_PAD_LEFT);
//        var_dump();
        $sq = "select distinct ubiniv from ubimapa where ubiestan = '{$est}' and ubihuec = '{$huec}' and ubiniv = '{$niv}'";
        $rs = $db->query($sq);
        $exist = 1;
        $msg = '';
        if($rs->num_rows == 0){
            $exist = 0;
            $msg = 'Nivel no existe';
        }
            $dt=$rs->fetch_assoc();
        echo json_encode(array('dat' => ''.@$dt['ubiniv'],'res' => $exist, 'msg' => $msg));
        break;
}
