<?php

require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);



$arti = $_POST['arti'];//ss
$cli = $_POST['cli'];
$prov = $_POST['prov'];
$dFecCre = $_POST['dFecCre'];
$hFecCre = $_POST['hFecCre'];
$alma = $_POST['alma'];
$clamov = $_POST['clamov'];
$pedclase = $_POST['pedclase'];
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
   $artiCond = " and fulldata.arti like '%{$arti}%'";
}
IF($cli != ""){
   $cliCond = " and fulldata.cliente like '%{$cli}%'";
}
IF($prov != ""){
   $provCond = " and fulldata.coprov like '%{$prov}%'";
}

$dsAr='';
if(count($alma) > 0 && is_array($alma)){
//    echo "entro";
    $dsAr = " and fulldata.almacen IN ( ";
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
if(count($clamov) > 0 && is_array($clamov)){
    $dsAr = " and fulldata.movref IN ( ";
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
if(count($clamov) > 0 && is_array($clamov)){
    $dsAr = " and fulldata.movref IN ( ";
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
    $dsAr = " and fulldata.cladoc IN ( ";
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
           $dtd1 =  " and date(fulldata.fecierre) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if(strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
           $dtd1 =  " and date(fulldata.fecierre) = '{$dt1}' ";
        }
        $fl = 1;
    }
    $dFecCreCond = $dtd1;
}

$sq = "SELECT * FROM (SELECT 
movimientos.artrefer as arti,
arti.artdesc as artides,
movimientos.ubirefer as ubicacion,
movimientos.etnum as multiref,
movimientos.cod_alma as almacen,
movimientos.fecmov as fecierre,
movimientos.hormov as horcierre,
movimientos.clamov as cladoc,
movimientos.pedido as nrodoc,
movimientos.canti as canprep,
movimientos.id as posi,
movimientos.usuario as userprep,
movimientos.clamov as movref,
movimientos.clamov as observacion
from movimientos, arti
where movimientos.artrefer=arti.artrefer
and movimientos.cod_alma=arti.almcod

UNION

SELECT  
ajuart.artrefer as arti,
arti.artdesc as artides,
ajuart.ubirefer as ubicacion,
ajuart.ubirefer as multiref,
ajuart.almrefer as almacen,
ajuart.fecha as fecierre,
ajuart.hora as horcierre,
artmov.pedclase as cladoc,
ajuart.numdoc as nrodoc,
SUM(ajuart.canajuscaj+ajuart.canajusun) as canprep,
ajuart.posicion as posi, 
ajuart.usuario as userprep,
ajuart.ajuref as movref,
ajutip.ajudes as observacion
from  ajuart, arti, artmov,ajutip
where arti.artrefer=ajuart.artrefer
and artmov.movref=ajuart.ajuref
and ajuart.movref=ajutip.ajuref
GROUP BY nrodoc
) as fulldata 

WHERE 1=1".$artiCond.$cliCond.$provCond.$dFecCreCond.$almaCond.$clamovCond.$claseCond;

//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while($row = $rs->fetch_assoc()){
    $cabeceras[$cc]['nrodoc'] = $row['nrodoc'];
    $cabeceras[$cc]['posi'] = $row['posi'];
    $cabeceras[$cc]['arti'] = $row['arti'];
    $cabeceras[$cc]['artides'] = $row['artides'];
    $cabeceras[$cc]['multiref'] = $row['multiref'];
    $cabeceras[$cc]['ubicacion'] = $row['ubicacion'];
    $cabeceras[$cc]['almacen'] = $row['almacen'];
    $cabeceras[$cc]['fecierre'] = $row['fecierre'];
    $cabeceras[$cc]['horcierre'] = $row['horcierre'];    
    $cabeceras[$cc]['cladoc'] = $row['cladoc'];
    $cabeceras[$cc]['canprep'] = $row['canprep'];
    $cabeceras[$cc]['userprep'] = $row['userprep'];
    $cabeceras[$cc]['movref'] = $row['movref'];
    
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
