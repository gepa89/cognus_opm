<?php

require ('../conect.php');
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
if(isset($_POST['dFecCre']) && isset($_POST['hFecCre'])){
    if(($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')){
        if(strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
           $dtd1 =  " and date(a.fecinvepic) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if(strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
           $dtd1 =  " and date(a.fecinvepic) = '{$dt1}' ";
        }
        $fl = 1;
    }
}
//var_dump($_POST["selCto"]);
if(is_array($_POST["selCto"]) && count($_POST["selCto"]) >= 1 ){
    $clase = " and a.invetipo IN ( ";
    foreach($_POST["selCto"] as $k => $v){
        if($k == 0){
            $clase .= "'".trim($v)."'";
        }else{
            $clase .= ", '".trim($v)."'";
        }
    }
    $clase .= ")";
    $fl = 1;
}else{
    $clase = "";
}
//var_dump($_POST["selSit"]);
if($_POST["inMat"] != '' ){
    $situ = " and a.artrefer like '%{$_POST["inMat"]}%'";
    $fl = 1;
}else{
    $situ = '';
}
$cabeceras = $detalles = array();
if($fl == 1){
    $sq = "SELECT distinct 
        a.*, 
        b.*
        FROM invenpic a 
left join arti b on a.artrefer = b.artrefer 
where 1=1  {$dtd1} {$clase} {$situ}";
//    echo $sq;
    $rs = $db->query($sq);
    while($row = $rs->fetch_assoc()){
        $cabeceras[] = $row;
    }
}


echo json_encode(array( 'cab' => $cabeceras ));

exit();
