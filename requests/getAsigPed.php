<?php

require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);



$arti = $_POST['arti'];//ss
$cli = $_POST['cli'];
$ped = $_POST['ped'];
$dFecCre = $_POST['dFecCre'];
$hFecCre = $_POST['hFecCre'];
$alma = $_POST['alma'];
$clamov = $_POST['clamov'];
$pedclase = $_POST['pedclase'];
$fecha = date("Y-m-d");
//+

//var_dump($alma);
//almacen
//cliente
//fecierre
//horcierre
//cladoc
//nrodoc
//canprep
//posi
//userprep
//movref

$artiCond = $cliCond = $provCond = $dFecCreCond = $almaCond = $clamovCond = '';
IF($arti != ""){
   $artiCond = " and assig_ped like '%{$arti}%'";
}


$dsAr='';
if(count($alma) > 0 && is_array($alma)){
//    echo "entro";
    $dsAr = " and almacen IN ( ";
    foreach($alma as $k => $v){
        if(is_numeric($v)){
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        }else{
            $arti = $v;
        }
        if($k == 0){
            $dsAr .= "'".trim($arti)."'";
        }else{
            $dsAr .= ", '".trim($arti)."'";
        }
    }
    $dsAr .= " ) ";
    
    $almaCond = $dsAr;
    $fl = 1;
}else{
    $dsAr = "";
    
    $almaCond = $dsAr;
}
//echo $almaCond;
//IF($clamov != ""){
//   $clamovCond = " and fulldata.movref like '%{$clamov}%'";
//}

$dsAr='';
if(count($ped) > 0 && is_array($ped)){
    $dsAr = " and assig_ped IN ( ";
    foreach($ped as $k => $v){
        if(is_numeric($v)){
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        }else{
            $arti = $v;
        }
        if($k == 0){
            $dsAr .= "'".trim($arti)."'";
        }else{
            $dsAr .= ", '".trim($arti)."'";
        }
    }
    $dsAr .= " ) ";
    
    $clamovCond = $dsAr;
    $fl = 1;
}else{
    $dsAr = "";
    
    $clamovCond = $dsAr;
}
$dsAr='';
if(count($clamov) > 0 && is_array($clamov)){
    $dsAr = " and assig_ped IN ( ";
    foreach($clamov as $k => $v){
        if(is_numeric($v)){
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        }else{
            $arti = $v;
        }
        if($k == 0){
            $dsAr .= "'".trim($arti)."'";
        }else{
            $dsAr .= ", '".trim($arti)."'";
        }
    }
    $dsAr .= " ) ";
    $clamovCond = $dsAr;
    $fl = 1;
}else{
    $dsAr = "";
    $clamovCond = $dsAr;
}

$dsAr='';
if(count($pedclase) > 0 && is_array($pedclase)){
    $dsAr = " and cladoc IN ( ";
    foreach($pedclase as $k => $v){
        if(is_numeric($v)){
            $arti = str_pad($v, 18, '0', STR_PAD_LEFT);
        }else{
            $arti = $v;
        }
        if($k == 0){
            $dsAr .= "'".trim($arti)."'";
        }else{
            $dsAr .= ", '".trim($arti)."'";
        }
    }
    $dsAr .= " ) ";
    $claseCond = $dsAr;
}else{
    $dsAr = "";
    $claseCond = $dsAr;
}

if(isset($_POST['dFecCre']) && isset($_POST['hFecCre'])){
    if(($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')){
        if(strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
           $dtd1 =  " date(fecasig) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if(strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
           $dtd1 =  " and date(fecasig) = '{$dt1}' ";
        }
        $fl = 1;
    }
    $dFecCreCond = $dtd1;
}

if(isset($_POST['dFecCre']) && isset($_POST['hFecCre'])){
    if(($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')){
        if(strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
           $dtd2 =  " date(fecha) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if(strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
           $dtd2 =  " and date(fecha) = '{$dt1}' ";
        }
        $fl = 1;
    }
    $dFecCreCond2 = $dtd2;
}


$sq = "SELECT DISTINCT
    pedido,
    tercod,
    cod_alma,
    fecasig,
    horasig,
    usuario,
    'Asignado' as accion
    FROM assig_ped
    where $dFecCreCond
UNION ALL

SELECT DISTINCT
    pedido,
    tercod,
    cod_alma,
    fecha,
    hora AS horasig,
    usuario,
    'Desasignado' as accion
FROM anul_asignaciones
    where $dFecCreCond2";


//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
//echo $sq;
while($row = $rs->fetch_assoc()){
    $cabeceras[$cc]['pedido'] = $row['pedido'];
    $cabeceras[$cc]['tercod'] = $row['tercod'];
    $cabeceras[$cc]['cod_alma'] = $row['cod_alma'];
    $cabeceras[$cc]['fecasig'] = $row['fecasig'];
    $cabeceras[$cc]['horasig'] = $row['horasig'];
    $cabeceras[$cc]['usuario'] = $row['usuario'];
    $cabeceras[$cc]['accion'] = $row['accion'];
    $cc++;
}
//echo $sq;

echo json_encode(array( 'cab' => $cabeceras));

exit();
