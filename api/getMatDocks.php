<?php
require('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
header("Content-Type: application/json");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$muelle = $_POST['muelle'];
//modo = 0 es muelle, modo = 1 es MR
$modo = (int) $_POST['modo'];
//print_r($_POST);exit;
$sqCnd = '';
if ($modo == 0) {
    $sqCnd = " and a.muelle = '{$muelle}'";
    $lblb = "muelle";
} else {
    $sqCnd = " and b.etnum = '{$muelle}'";
    $lblb = "Multi referencia";
}
$cod_almacen = null;
if ($modo == 1) { // trae un solo registro para obtrener almacen
    $sql = "SELECT cod_alma from pallet_mat WHERE etnum = '$muelle' limit 1"; // busca codalma multiref
    $cod_almacen = $db->query($sql)->fetch_assoc()['cod_alma'];

} else {
    $sql = ""; // busca en ped muelle
};
//$response["error"] = TRUE;
//$response['mensaje'] = "Ningun Material ubicado en muelle. modo ".$modo.", valor:".$muelle;
$response = array();
//     $query = "select * from prt_users where prt_user = '{$_POST['user']}' and prt_psw = '{$_POST['pass']}'";
$qry = "SELECT distinct a.muelle, b.artrefer, b.canti, c.artdesc, a.cod_alma FROM pedmuelle a 
	inner join stockubi b on a.muelle = b.ubirefer 
	inner join arti c on b.artrefer = c.artrefer
	where 1=1 {$sqCnd} and a.cod_alma='$cod_almacen' and a.pedstatus = 0";

//        echo $qry;
//     $oci_qry = "select * from sapabap1.ZMM003_EMP_CLASE where USUARIO = '{$usr}'";
//    $cc = 0;
$r1 = $db->query($qry);
$response['materiales'][0]['material'] = 'Todo';
$response['materiales'][0]['extra'] = '';
$response['materiales'][0]['ubicacion'] = '';
$response['materiales'][0]['cod_almacen'] = '';

$contador = 1;
while ($row = $r1->fetch_assoc()) {
    $sql = "select distinct * from artubiref where artrefer = '{$row['artrefer']}' AND cod_alma='$cod_almacen'";
    $rx = $db->query($sql);
    while ($ax = $rx->fetch_assoc()) {
        if (!in_array($ax['ubirefer'], $response['ubicaciones'][$row['artrefer']][$ax['ubitipo']])) {
            $response['ubicaciones'][$row['artrefer']][$ax['ubitipo']] = separateLocation($ax['ubirefer']);
        }
    }
    $response['materiales'][$contador]['material'] = $row['artrefer'] . ' - ' . $row['artdesc'];
    $response['materiales'][$contador]['ubicacion'] = $row['artrefer'];
    $response['materiales'][$contador]['cod_almacen'] = $row['cod_alma'];
    //$response['materiales'][$contador]['extra'][] = 'asdddddg';
    //new query para los datos adicionales
    $sql = "SELECT artubiref.ubirefer, COALESCE(SUM(canti),0) as canti,ubimapa.ubitipo
        FROM artubiref 
        LEFT JOIN ubimapa on artubiref.ubirefer=ubimapa.ubirefer 
        LEFT JOIN stockubi on ubimapa.ubirefer=stockubi.ubirefer 
        WHERE ubimapa.ubitipo <>'MR' 
        AND artubiref.cod_alma = '$cod_almacen' 
        and artubiref.artrefer='{$row['artrefer']}' 
        GROUP BY ubirefer,ubitipo  
        ORDER BY stockubi.fecingre ASC";
    //print_r($sql);
    $rx = $db->query($sql);
    while ($ax = $rx->fetch_assoc()) {
        /*if(!in_array($ax['ubirefer'], $response['extra'][$row['artrefer']]['tipo'])){                
        $response['extra'][$row['artrefer']]['tipo'] = $ax['ubitipo']; 
        }
        if(!in_array($ax['ubirefer'], $response['extra'][$row['artrefer']]['ubicacion'])){                
        $response['extra'][$row['artrefer']]['ubicacion'] = $ax['ubirefer']; 
        }
        if(!in_array($ax['ubirefer'], $response['extra'][$row['artrefer']]['canti'])){                
        $response['extra'][$row['artrefer']]['cantidad'] = $ax['canti']; 
        }*/
        //if(!in_array($ax['ubirefer'], $response['extra'][$row['artrefer']]['detalles'][$ax['ubitipo']])){                
        $response['materiales'][$contador]['extra'][] = "Ubi: " . $ax['ubirefer'] . " | Cant: " . $ax['canti'] . " | Tipo: " . $ax['ubitipo'];
        //}

    }
    $contador++;
}
if (isset($response['materiales']) && count($response['materiales']) > 1) {
    $response["error"] = FALSE;
    $response['mensaje'] = '';
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "Ningun Material ubicado en " . $lblb;
}

//$response["error"] = TRUE;
//$response['mensaje'] = "Ningun Material ubicado en muelle. modo ".$modo.", valor:".$muelle." qru ".$qry;
echo json_encode($response);
include '/var/www/html/closeconn.php';