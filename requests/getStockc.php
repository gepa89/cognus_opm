<?php

require ('../conect.php');
require_once("../utils/conversores.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$pd = $_POST['cliente'];
$codalma = $_POST['codalma'];

$pdcnd = $pdstr = '';
if($pd != ''){
    $cliente = explode(',', $pd);
    foreach($cliente as $k => $v){
        if($pdstr != ''){
           $pdstr .= ",'".$v."'";
        }else{
            $pdstr = "'".$v."'";
        }        
        $pdcnd = " and c.clirefer in (".$pdstr.")";
    }
    
}
$sq = "SELECT
	c.clirefer,
	c.clinom,
	a.cod_alma,
	SUM( a.candispo ) AS totals,
	SUM( a.candispo * b.costo ) AS totalvpyg,
	SUM( a.candispo * b.costo / 7000 ) AS totalvusd 
FROM
	stockart a
	INNER JOIN arti b ON a.artrefer = b.artrefer
	INNER JOIN clientes c ON b.clirefer = c.clirefer 
WHERE
	1 = 1 ".$pdcnd." 
	AND a.cod_alma = '{$codalma}' 
GROUP BY
	clirefer";
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
    $cabeceras[$cc]['clirefer'] = $row['clirefer'];
    $cabeceras[$cc]['clinom'] = $row['clinom'];
    $cabeceras[$cc]['cod_alma'] = $row['cod_alma'];
    $cabeceras[$cc]['totals'] = formatear_numero($row['totals']);
    $cabeceras[$cc]['totalvusd'] = number_format($row['totalvusd']);
    $cabeceras[$cc]['totalvpyg'] = number_format($row['totalvpyg']);
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
