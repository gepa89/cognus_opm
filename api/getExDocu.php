<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once('../conect.php');
require_once(__DIR__ . '/../logger/logger.php');

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);
$tiene_presentacion = $_POST['tiene_presentacion'];
$tiene_presentacion = $tiene_presentacion === "true";
$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$zona = $rszz['terzonpre'];
$pedidos = obtenerPedidos($db, $terminal, $zona, $tiene_presentacion);
/*$pedidos = obtenerPedidos($db, $terminal, $zona, false);
if ($pedidos['error']) {
    $pedidos = obtenerPedidos($db, $terminal, $zona, true);
}*/
echo json_encode($pedidos);
include '/var/www/html/closeconn.php';
exit;
function obtenerPedidos($db, $terminal, $zona, $tiene_presentacion)
{
    $tabla_detalles = "pedexdet";
    $tabla_cabecera = "pedexcab";
    $filtro_ped_cajas = " AND f.pedcajas = 0";
    if ($tiene_presentacion) {
        $tabla_detalles = "pedexdetcajas";
        $tabla_cabecera = "pedexcabcajas";
        $filtro_ped_cajas = " AND f.pedcajas = 1";
    }
    $response = array();
    $cantidad_asignados = 0;
    $cantidad_pendientes = 0;
    $response['data'][0]['asignados'] = 0;
    $response['data'][0]['pendientes'] = 0;
    $sq = "select DISTINCT
            a.pedido, 
            a.st,
            a.ex_st, 
            d.zoncodpre,
            (select count(artrefer) as tsku from $tabla_detalles b where pedexentre = pedido) as tsku,
            (select pedclase as pclase from $tabla_cabecera c where pedexentre = pedido) as clase
            from assig_ped a 
            inner join $tabla_detalles b on pedexentre = pedido
            inner join stockubi c on b.artrefer = c.artrefer
            inner join ubimapa d on c.ubirefer = d.ubirefer
            inner join termi e on a.tercod = e.tercod
            where 
            a.tercod = '$terminal'
            AND e.terzonpre = '$zona' 
            AND d.zoncodpre = '$zona' 
            AND st = 0  
            AND ex_st = 0";
    $r1 = $db->query($sq);
    while ($row = $r1->fetch_assoc()) {
        $response['pedidos'][] = $row;
    }

    $sq = "select DISTINCT
            a.pedido, 
            a.st,
            a.ex_st, 
            d.zoncodpre,
						f.multiref,
            (select count(artrefer) as tsku from $tabla_detalles b where pedexentre = a.pedido) as tsku,
            (select pedclase as pclase from $tabla_cabecera c where pedexentre = a.pedido) as clase
            from assig_ped a 
            inner join $tabla_detalles b on pedexentre = pedido
            inner join stockubi c on b.artrefer = c.artrefer
            inner join ubimapa d on c.ubirefer = d.ubirefer
            inner join termi e on a.tercod = e.tercod
            left join ped_multiref f on a.pedido = f.pedido and a.zona = f.zona 
            where 
            a.tercod = '$terminal' 
            AND e.terzonpre = '$zona' 
            AND d.zoncodpre = '$zona' 
            AND st = 0  
            {$filtro_ped_cajas}
            ";
    $r1 = $db->query($sq);

    while ($row = $r1->fetch_assoc()) {
        if ($row['multiref'] == '') {
            $cantidad_pendientes++;
        } else {
            $cantidad_asignados++;
        }
    }
    $response['data'][0]['pendientes'] = $cantidad_pendientes;
    $response['data'][0]['asignados'] = $cantidad_asignados;
    if (isset($response['pedidos'])) {
        $response["error"] = FALSE;
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "Sin pedidos pendientes de relacion MR.";
    }
    return $response;
}