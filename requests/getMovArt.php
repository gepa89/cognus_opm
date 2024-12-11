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



$sq = "SELECT * from (
SELECT distinct 
arti.artrefer as arti,
arti.artdesc as artides,
CONCAT(pedrecab.codprove, ".'" - "'.",pedrecab.nomprove)  as coprov,
pedrecab.almrefer as almacen,
pedrecab.pedalemi as cliente,
pedrecab.pedrecie as fecierre,
pedrecab.pedrehorcie as horcierre,
pedrecab.pedclase as cladoc,
pedrecab.pedrefer as nrodoc,
pedredet.canprepa as canprep, 
pedredet.pedpos as posi, 
pallet_mat.ap_usr as userprep,
pedrecab.movref as movref,
pedrecab.obse1 as observacion,
pedredet.incidencia as incidencia
from arti, pedrecab,pedredet,pallet_mat

where pedrecab.pedrefer=pedredet.pedrefer

AND arti.artrefer=pedredet.artrefer
and pedrecab.pedrefer=pallet_mat.ap_pedido
and pedredet.artrefer=pallet_mat.ap_mat
AND pedrecab.pedresitu IN ('CE','UB')

UNION

SELECT 
arti.artrefer as arti,
arti.artdesc as artides,
pedexcab.obse1 as coprov,
pedexcab.almrefer as almacen,
pedexcab.clirefer as cliente,
pedexdet.fechcie as fecierre,
pedexdet.horcie as horcierre,
pedexcab.pedclase as cladoc,
pedexcab.pedexentre as nrodoc,
pedexdet.canprepa as canprep,
pedexdet.pedpos as posi,  
pedexdet.usuario as userprep,
pedexcab.movref as movref,
pedexcab.obse1 as observacion,
pedexdet.incidencia as incidencia
from arti, pedexcab,pedexdet
where pedexcab.pedexentre=pedexdet.pedexentre
AND arti.artrefer=pedexdet.artrefer
and pedexcab.siturefe in ('CE','PP')

UNION

SELECT 
arti.artrefer as arti,
arti.artdesc as artides,
pedexcab.obse1 as coprov,
pedexcab.almrefer as almacen,
pedexcab.clirefer as cliente,
pedexcab.pedexfec as fecierre,
pedexdet.horcie as horcierre,
pedexcab.pedclase as cladoc,
pedexcab.pedexentre as nrodoc,
pedexdet.canprepa as canprep,
pedexdet.pedpos as posi,  
pedexdet.usuario as userprep,
pedexcab.movref as movref,
pedexcab.obse1 as observacion,
pedexdet.incidencia as incidencia
from arti, pedexcab,pedexdet
where pedexcab.pedexentre=pedexdet.pedexentre
AND arti.artrefer=pedexdet.artrefer
and pedexcab.siturefe in ('CE','PP')
and pedexdet.fechcie is NULL

UNION

SELECT 
expeartce.id_articulo as arti,
arti.artdesc as artides,
expeartce.ubicacion as coprov,
expeartce.cod_alma as almacen,
pedexcab.clirefer as cliente,
expeartce.fecha as fecierre,
expeartce.hora as horcierre,
expeartce.inci as cladoc,
expeartce.id_pedido as nrodoc,
pedexdet.canpedi as canprep,
pedexdet.pedpos as posi, 
expeartce.usuario as userprep,
expeartce.inci as movref,
expeartce.inci as observacion,
expeartce.inci as incidencia
from expeartce 
INNER JOIN arti on expeartce.id_articulo=arti.artrefer
INNER JOIN pedexcab on expeartce.id_pedido=pedexcab.pedexentre
INNER JOIN pedexdet on expeartce.id_pedido=pedexdet.pedexentre and pedexdet.artrefer=expeartce.id_articulo

UNION

SELECT  
ajuart.artrefer as arti,
arti.artdesc as artides,
ajuart.ubirefer as coprov,
ajuart.almrefer as almacen,
ajuart.cliente as cliente,
ajuart.fecha as fecierre,
ajuart.hora as horcierre,
artmov.pedclase as cladoc,
ajuart.numdoc as nrodoc,
SUM(ajuart.canajuscaj+ajuart.canajusun) as canprep,
ajuart.posicion as posi, 
ajuart.usuario as userprep,
ajuart.ajuref as movref,
ajutip.ajudes as observacion,
ajutip.ajudes as incidencia
from  ajuart, arti, artmov,ajutip
where arti.artrefer=ajuart.artrefer
and artmov.movref=ajuart.ajuref
and ajuart.movref=ajutip.ajuref
GROUP BY nrodoc
) as fulldata 
where 
1=1 ".$artiCond.$cliCond.$provCond.$dFecCreCond.$almaCond.$clamovCond.$claseCond;

//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while($row = $rs->fetch_assoc()){
    $cabeceras[$cc]['arti'] = $row['arti'];
    $cabeceras[$cc]['artides'] = $row['artides'];
    $cabeceras[$cc]['coprov'] = $row['coprov'];
    $cabeceras[$cc]['almacen'] = $row['almacen'];
    $cabeceras[$cc]['cliente'] = $row['cliente'];
    $cabeceras[$cc]['fecierre'] = $row['fecierre'];
    $cabeceras[$cc]['horcierre'] = $row['horcierre'];    
    $cabeceras[$cc]['cladoc'] = $row['cladoc'];
    $cabeceras[$cc]['nrodoc'] = $row['nrodoc'];
    $cabeceras[$cc]['canprep'] = $row['canprep'];
    $cabeceras[$cc]['posi'] = $row['posi'];
    $cabeceras[$cc]['userprep'] = $row['userprep'];
    $cabeceras[$cc]['movref'] = $row['movref'];
    $cabeceras[$cc]['incidencia'] = $row['incidencia'];
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
