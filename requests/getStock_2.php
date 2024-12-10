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
$pd = $_POST['pedidos'];
$codalma = $_POST['codalma'];

$pdcnd = $pdstr = '';
if($pd != ''){
    $pedidos = explode(',', $pd);
    foreach($pedidos as $k => $v){
        if($pdstr != ''){
           $pdstr .= ",'".$v."'";
        }else{
            $pdstr = "'".$v."'";
        }        
        $pdcnd = " and a.artrefer in (".$pdstr.")";
    }
    
}
$sq = "select distinct  a.artrefer,b.artdesc,a.candispo,a.canmure,a.canpedven,a.cantransfe,a.cod_alma from stockart a inner join arti b on a.artrefer = b.artrefer where a.cod_alma = '{$codalma}' and 1=1 ".$pdcnd ;
//echo $sq;
$ub = $_POST['ubicaciones'];
$ubstr = '';
if($ub =! ''){
    $ubicaciones = explode(',', $ub);
    foreach($ubicaciones as $k => $v){
        if($ubstr != ''){
           $ubstr .= ",'".$v."'";
        }else{
            $ubstr = "'".$v."'";
        }        
    }
    
}


//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while($row = $rs->fetch_assoc()){
//        <th>Código</th>
//        <th>Descripción</th>
//        <th>Libre</th>
//        <th>Muelle</th>
//        <th>Transf.</th>
//        <th>Devolución</th>
    $cabeceras[$cc]['artrefer'] = $row['artrefer'];
    $cabeceras[$cc]['artdesc'] = $row['artdesc'];
    $cabeceras[$cc]['candispo'] = $row['candispo'];
    $cabeceras[$cc]['canmure'] = $row['canmure'];
    $cabeceras[$cc]['canpedven'] = $row['canpedven'];
    $cabeceras[$cc]['cantransfe'] = $row['cantransfe'];
    $cabeceras[$cc]['cod_alma'] = $row['cod_alma'];
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
