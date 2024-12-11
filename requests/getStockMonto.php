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
$cl = $_POST['cliente'];
$ub = $_POST['ubicaciones'];
$codalma = $_POST['codalma'];
$ubstr = $pdcnd = '';
if($_POST['ubicaciones'] != ''){
//    var_dump($_POST['ubicaciones']);
//    $ubicaciones = explode(',', $ub);
    
//var_dump($ubicaciones);
//    foreach($ubicaciones as $k => $v){
//        if($ubstr != ''){
//           $ubstr .= ",'".$v."'";
//        }else{
//            $ubstr = "'".$v."'";
//        }     
//        $pdcnd = " and a.ubirefer in (".$ubstr.")";
//    }
    $pdcnd = " and a.ubirefer = '".$_POST['ubicaciones']."'";
    
//    echo $sq;
}
if($_POST['cliente'] != ''){
//    var_dump($_POST['ubicaciones']);
//    $ubicaciones = explode(',', $ub);
    
//var_dump($ubicaciones);
//    foreach($ubicaciones as $k => $v){
//        if($ubstr != ''){
//           $ubstr .= ",'".$v."'";
//        }else{
//            $ubstr = "'".$v."'";
//        }     
//        $pdcnd = " and a.ubirefer in (".$ubstr.")";
//    }
    $pdcnd3 = " and d.clirefer like '%".$_POST['cliente']."'";
    
//    echo $sq;
}
if($_POST['articulo'] != ''){
//    var_dump($_POST['ubicaciones']);
//    $ubicaciones = explode(',', $ub);
    
//var_dump($ubicaciones);
//    foreach($ubicaciones as $k => $v){
//        if($ubstr != ''){
//           $ubstr .= ",'".$v."'";
//        }else{
//            $ubstr = "'".$v."'";
//        }     
//        $pdcnd = " and a.ubirefer in (".$ubstr.")";
//    }
    $pdcnd2 = " and a.artrefer like '%".$_POST['articulo']."%'";
    
//    echo $sq;
}
$sq = "select distinct d.clirefer,d.clinom,b.artrefer, b.artdesc, a.ubirefer,a.cod_alma,  sum(a.canti) as total,sum(a.canti*b.costo) as valor,sum(a.canti*b.costo/7000) as valorus from stockubi a inner join arti b on a.artrefer = b.artrefer inner join ubimapa c on c.ubirefer = a.ubirefer INNER JOIN clientes d ON b.clirefer = d.clirefer and c.cod_alma = a.cod_alma  where 1=1 ".$pdcnd.$pdcnd3.$pdcnd2." and a.cod_alma = '{$codalma}' group by a.ubirefer, a.artrefer,a.cod_alma";

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
    $cabeceras[$cc]['clirefer'] = $row['clirefer'];
    $cabeceras[$cc]['clinom'] = $row['clinom'];
    $cabeceras[$cc]['artrefer'] = $row['artrefer'];
    $cabeceras[$cc]['artdesc'] = $row['artdesc'];
    $cabeceras[$cc]['ubirefer'] = separateLocation($row['ubirefer']);
    $cabeceras[$cc]['total'] = $row['total'];
    $cabeceras[$cc]['valor'] = number_format($row['valor']);
    $cabeceras[$cc]['valorus'] = number_format($row['valorus']);
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
