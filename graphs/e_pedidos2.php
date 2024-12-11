<?php
require('../conect.php');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$tt = 0;
$t_proc = 0;
$procesado = 0;
$filtro_almacen = "";
if (!empty($_GET['almacen'])) {
    $filtro_almacen = " AND almrefer =  '" . $_GET['almacen'] . "'";
}

$sq = "SELECT COUNT(pedexcab.siturefe)as TotalPedVentas, siturefe
        FROM pedexcab
        WHERE pedclase in ('ZCON','ZCRE','ZCOS') 
        AND pedexfec =CURDATE()/*fecha del dia*/
        $filtro_almacen
        GROUP BY siturefe";

$rs = $db->query($sq);
$data = array();
$datax = array();
$series = "";
while ($ax = $rs->fetch_assoc()) {
    //    var_dump
    $tt += $ax['TotalPedVentas'];
    $data['name'] = getLabel($ax['siturefe']) . ': ' . $ax['TotalPedVentas'];
    $data['y'] = $ax['TotalPedVentas'];
    //    array_push($datax,$data);
    $datax[getLabel($ax['siturefe'])] = $ax['TotalPedVentas'];
    if ($series != '') {
        $series .= ",{name:'" . getLabel($ax['siturefe']) . ": " . $ax['TotalPedVentas'] . "', y: " . $ax['TotalPedVentas'] . "}";
    } else {
        $series = "{name:'" . getLabel($ax['siturefe']) . ": " . $ax['TotalPedVentas'] . "', y: " . $ax['TotalPedVentas'] . "}";
    }

}

echo json_encode(array('total' => $tt, 'dat' => $datax, 'ser' => $series));