<?php

require ('../conect.php');
require_once(__DIR__."/../utils/conversores.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$typ = $_POST['id'];
$art = $_POST['art'];
$pedrefer = $_POST['pedrefer'];
$codalma = $_POST['codalma'];
$ped = $_POST['ped'];
$response = array();
$título = $lk = '';
$header ="";

        $título ="Observation";
        $header = '<tr><th>Order</th><th>Date</th><th>Hour</th><th>User</th><th>Observation</th></tr>';
        $sq = "SELECT 
            pedprove,
            fecha,
            hora,
            CONCAT(usuarios.pr_nombre, ' ', usuarios.pr_apellido) as nombre,
            observacion
          
            FROM obsepedprove
            left join usuarios on obsepedprove.coduser=usuarios.pr_user
            
            WHERE pedprove='{$ped}' order by fecha,hora asc";
          //  $lk = '<a title="Modifiación de Ubicación" target="_blank" href="p_ref_ubic_upd.php?mat='."".$ped."".'"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>';
//                    echo $sq;
    

$rs = $db->query($sq);
$cc = 0;
while($ax = $rs->fetch_assoc()){
    if (is_numeric($ax['cantidad_presentacion'])) {
        $ax['canti']  = $ax['canti'] /  $ax['cantidad_presentacion'];
        $ax['preseref'] = 'CJ';
    }
    $ax['canti'] = formatear_numero($ax['canti']);
    unset($ax['cantidad_presentacion']);
    $response[$cc] = $ax;
    $response[$cc]['actio'] = $lk;
    $cc++;
}
if(count($response) > 0){
    $msg = '';
    $err = FALSE;
}else{
    $msg = 'No hay Observaciones para mostrar.';
    $err = TRUE;
}
echo json_encode(array( 'cab' => $response, 'tit' => $título, 'err' => $err, 'msg' => $msg, 'hdr' => $header));

exit();
