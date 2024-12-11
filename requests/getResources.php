<?php

require ('../conect.php');

$db = new mysqli($SERVER,$USER,$PASS,$DB);
$sq = "select a.*,b.*, (select count(1) as total from assig_ped c where a.tercod = c.tercod and c.st = 0) as asigped from termi_assign a inner join termi b on a.tercod = b.tercod";
//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while($row = $rs->fetch_assoc()){
    $cabeceras[$cc]['tercod'] = $row['tercod'];
    $cabeceras[$cc]['ope'] = $row['ope'];
    $cabeceras[$cc]['terdes'] = $row['terdes'];
    $cabeceras[$cc]['canmax'] = $row['canped'];
    $cabeceras[$cc]['almrefer'] = $row['almrefer'];
    $cabeceras[$cc]['tipac'] = $row['tipac'];
    $cabeceras[$cc]['zonas'] = $row['terzonpre'];
    $cabeceras[$cc]['asigped'] = $row['asigped'];
    $cabeceras[$cc]['accion'] = '<a title="Ver Asignaciones" href="javascript:void(0);" onclick="ckAssign('."'".$row['tercod']."'".')"><span style="font-size:14px" class="glyphicon glyphicon-search"></span></a>';
    if($row['asigped'] == 0){
        $cabeceras[$cc]['accion'] .= '  |  <a title="Cerrar Sesión" href="javascript:void(0);" onclick="delSession('."'".$row['ope']."',"."'".$row['tercod']."'".')"><span style="font-size:14px" class="glyphicon glyphicon-log-out"></span></a>';   
    }
     
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
