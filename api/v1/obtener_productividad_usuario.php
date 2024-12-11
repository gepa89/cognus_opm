<?php
require_once(__DIR__ . '/../../conect.php');
require_once(__DIR__ . '/../../logger/logger.php');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
header('Content-type: application/json; charset=utf-8');


$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;
if (!$fecha_inicio || !$fecha_fin) {
    return;
}

function convertirDatosUsuario($datos)
{
    $result = [];
    foreach ($datos as $entry) {
        $entry = (object) $entry;
        $fecha = $entry->fecha;
        $cantidad = (int)$entry->cantidad;
        $usuario = $entry->usuario;
        $fuente = $entry->fuente;
        $result[$usuario][$fuente] += $cantidad;
    }
    $output_data = [];

    foreach ($result as $key => $value) {
        $output_data[] = array(
            "nombre" => $key,
            "salidas" => $value["salidas"] ?? 0,
            "entradas" => $value["entradas"] ?? 0,
            "maquinista" => $value["maquinista"] ?? 0
        );
    }
    return $output_data;
}

function obtenerDatosUsuario($db, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT * from (
        SELECT
        count(*) as cantidad ,
        pedexdet.usuario as usuario,
        'salidas' as fuente,
        pedexdet.fechcie as fecha
        FROM pedexcab
        INNER JOIN pedexdet on pedexcab.pedexentre=pedexdet.pedexentre
        INNER JOIN artmov on pedexcab.movref=artmov.movref
        LEFT JOIN assig_ped on pedexcab.pedexentre=assig_ped.pedido and pedexcab.almrefer=assig_ped.cod_alma 
        WHERE pedexcab.siturefe IN ('CE','PP')
        and pedexdet.canprepa <> '0'
        and pedexdet.fechcie BETWEEN '$fecha_inicio' AND '$fecha_fin'
        GROUP BY pedexdet.usuario, pedexdet.fechcie
        
        UNION ALL
        
        select 
        count(*) as cantidad,
        pallet_mat.ap_usr as usuario,
        'entradas' as fuente,
        pedrecab.pedrecie as fecha
        FROM pedrecab
        INNER JOIN pedredet on pedrecab.pedrefer=pedredet.pedrefer
        INNER JOIN arti on pedredet.artrefer=arti.artrefer
        INNER JOIN artmov on pedrecab.movref=artmov.movref
        LEFT JOIN pallet_mat on pedrecab.pedrefer=pallet_mat.ap_pedido and pedredet.artrefer=pallet_mat.ap_mat
        LEFT JOIN assig_ped on pedrecab.pedrefer=assig_ped.pedido and pedrecab.almrefer=assig_ped.cod_alma 
        WHERE pedrecab.pedresitu in ('CE','UB','PP')
        and pedredet.canprepa <> '0'
        and pedrecab.pedrecie BETWEEN '$fecha_inicio' AND '$fecha_fin'
        GROUP BY pallet_mat.ap_usr, pedrecab.pedrecie
        
        UNION ALL
        
        select
            count(*) as cantidad,
            pedubidet.usuario as usuario, 
            'maquinista' as fuente,
            pedubidet.fecha as fecha
        from
            pedubicab
        INNER JOIN pedubidet on
            pedubicab.pedubicod = pedubidet.pedubicod
        INNER JOIN artmov on
            pedubicab.pedclase = artmov.pedclase
        LEFT JOIN assig_ped on
            pedubicab.pedubicod = assig_ped.pedido and pedubicab.cod_alma=assig_ped.cod_alma 
        WHERE
            pedubidet.expst = '1'
            and pedubidet.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
        GROUP BY 	
            pedubidet.usuario, pedubidet.fecha
        ) as fulldata
        where
            1 = 1";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
echo json_encode(convertirDatosUsuario(obtenerDatosUsuario($db, $fecha_inicio, $fecha_fin)));
