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
$sq = "select distinct d.artlote,d.fecaduc,b.artrefer, b.artdesc, a.ubirefer,a.cod_alma, c.ubitipo, sum(a.canti) as total from stockubi a inner join arti b on a.artrefer = b.artrefer inner join ubimapa c on c.ubirefer = a.ubirefer INNER JOIN loteart d on a.artrefer=d.artrefer and a.artlote=d.artlote and c.cod_alma = a.cod_alma  where 1=1 ".$pdcnd.$pdcnd2." and b.artlotemar='SI' and a.cod_alma = '{$codalma}' group by d.artlote,a.ubirefer, a.artrefer,a.cod_alma";

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
    $cabeceras[$cc]['artlote'] = $row['artlote'];
    $cabeceras[$cc]['fecaduc'] = $row['fecaduc'];
    $cabeceras[$cc]['artrefer'] = $row['artrefer'];
    $cabeceras[$cc]['artdesc'] = $row['artdesc'];
    $cabeceras[$cc]['ubirefer'] = separateLocation($row['ubirefer']);
    $cabeceras[$cc]['ubitip'] = $row['ubitipo'];    
    $cabeceras[$cc]['total'] = $row['total'];
    $cabeceras[$cc]['cod_alma'] = $row['cod_alma'];
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
