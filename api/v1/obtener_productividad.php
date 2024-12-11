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

$filtros_salida = "";

$entrada  = obtenerDatosEntrada($db, $fecha_inicio, $fecha_fin);
$salida = obtenerDatosSalida($db, $fecha_inicio, $fecha_fin);
$entrada_horas = obtenerPorHoraEntrada($db, $fecha_inicio, $fecha_fin);
$salida_horas = obtenerPorHoraSalida($db, $fecha_inicio, $fecha_fin);

// Output the result
echo json_encode(
    array(
        "entrada" => convertirDatos($entrada),
        "salida" => convertirDatos($salida),
        "entrada_horas" => convertirDatosHora($entrada_horas),
        "salida_horas" => convertirDatosHora($salida_horas)
    )
);
exit;

//todo:ajustar por mes

//todo:ver por horas

#por salidas

$sql = "SELECT * from (
    SELECT
    count(*) as cantidad ,
    pedexdet.fechcie as fecha,
    'salidas' as fuente 
    FROM pedexcab
    INNER JOIN pedexdet on pedexcab.pedexentre=pedexdet.pedexentre
    INNER JOIN artmov on pedexcab.movref=artmov.movref
    LEFT JOIN assig_ped on pedexcab.pedexentre=assig_ped.pedido
    WHERE pedexcab.siturefe IN ('CE','PP')
    and pedexdet.canprepa <> '0'
    GROUP BY pedexdet.fechcie 
    
    UNION ALL
    
    select 
    count(*) as cantidad,
    pedrecab.pedrecie as fecha,
    'entradas' as fuente 
    
    FROM pedrecab
    INNER JOIN pedredet on pedrecab.pedrefer=pedredet.pedrefer
    INNER JOIN arti on pedredet.artrefer=arti.artrefer
    INNER JOIN artmov on pedrecab.movref=artmov.movref
    LEFT JOIN pallet_mat on pedrecab.pedrefer=pallet_mat.ap_pedido
    LEFT JOIN assig_ped on pedrecab.pedrefer=assig_ped.pedido
    
    WHERE pedrecab.pedresitu in ('CE','UB','PP')
    and pedredet.canprepa <> '0'
    GROUP BY pedrecab.pedrecie
    
    
    UNION ALL
    
    select
        count(*) as cantidad,
        pedubidet.fecha as fecha, 
        'maquinista' as fuente 
    
    from
        pedubicab
    INNER JOIN pedubidet on
        pedubicab.pedubicod = pedubidet.pedubicod
    INNER JOIN artmov on
        pedubicab.pedclase = artmov.pedclase
    LEFT JOIN assig_ped on
        pedubicab.pedubicod = assig_ped.pedido
    WHERE
        pedubidet.expst = '1'
    GROUP BY pedubidet.fecha  
    
    ) as fulldata
    where
        1 = 1";

#por suario
$sql = "SELECT * from (
    SELECT
    count(*) as cantidad ,
    pedexdet.usuario as usuario,
    'salidas' as fuente 
    FROM pedexcab
    INNER JOIN pedexdet on pedexcab.pedexentre=pedexdet.pedexentre
    INNER JOIN artmov on pedexcab.movref=artmov.movref
    LEFT JOIN assig_ped on pedexcab.pedexentre=assig_ped.pedido
    WHERE pedexcab.siturefe IN ('CE','PP')
    and pedexdet.canprepa <> '0'
    GROUP BY pedexdet.usuario 
    
    UNION ALL
    
    select 
    count(*) as cantidad,
    pallet_mat.ap_usr as usuario,
    'entradas' as fuente 
    
    FROM pedrecab
    INNER JOIN pedredet on pedrecab.pedrefer=pedredet.pedrefer
    INNER JOIN arti on pedredet.artrefer=arti.artrefer
    INNER JOIN artmov on pedrecab.movref=artmov.movref
    LEFT JOIN pallet_mat on pedrecab.pedrefer=pallet_mat.ap_pedido
    LEFT JOIN assig_ped on pedrecab.pedrefer=assig_ped.pedido
    
    WHERE pedrecab.pedresitu in ('CE','UB','PP')
    and pedredet.canprepa <> '0'
    GROUP BY pallet_mat.ap_usr
    
    
    UNION ALL
    
    select
        count(*) as cantidad,
        pedubidet.usuario as usuario, 
        'maquinista' as fuente 
    
    from
        pedubicab
    INNER JOIN pedubidet on
        pedubicab.pedubicod = pedubidet.pedubicod
    INNER JOIN artmov on
        pedubicab.pedclase = artmov.pedclase
    LEFT JOIN assig_ped on
        pedubicab.pedubicod = assig_ped.pedido
    WHERE
        pedubidet.expst = '1'
    GROUP BY 	
        pedubidet.usuario
    ) as fulldata
    where
        1 = 1";

#por usuario y dia


function obtenerDatosSalida($db, $fecha_inicio, $fecha_fin)
{
    $sq = "SELECT
        *
        from
        (
        SELECT
            count(*) as cantidad ,
            pedexdet.fechcie as fecha,
            'salidas' as fuente
            FROM
                pedexcab
            INNER JOIN pedexdet on
                pedexcab.pedexentre = pedexdet.pedexentre
            INNER JOIN artmov on
                pedexcab.movref = artmov.movref
            LEFT JOIN assig_ped on
                pedexcab.pedexentre = assig_ped.pedido
            WHERE
                pedexcab.siturefe IN ('CE', 'PP')
                and pedexdet.fechcie BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedexdet.fechcie
        UNION ALL
        select
            count(*) as cantidad,
            pedrecab.pedrecie as fecha,
            'entradas' as fuente
            FROM
                pedrecab
            INNER JOIN pedredet on
                pedrecab.pedrefer = pedredet.pedrefer
            WHERE
                pedrecab.pedresitu in ('CE', 'UB', 'PP','PD')
                and pedrecab.pedrecie BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedrecab.pedrecie
        UNION ALL
        select
            count(*) as cantidad,
            pedubidet.fecha as fecha,
            'maquinista' as fuente
            from
                pedubicab
            INNER JOIN pedubidet on
                pedubicab.pedubicod = pedubidet.pedubicod
            INNER JOIN artmov on
                pedubicab.pedclase = artmov.pedclase
            WHERE
                pedubidet.expst = '1'
                and pedubidet.fecha BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedubidet.fecha ) as fulldata
    ";

    $sql = "SELECT SUM(cantidad) as cantidad, fuente, fecha FROM ($sq) as datos GROUP BY fuente,fecha order by fecha ASC";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

function obtenerDatosEntrada($db, $fecha_inicio, $fecha_fin)
{
    $sq = "SELECT
        *
        from
        (
        SELECT
            count(*) as cantidad ,
            pedexcab.fecllegada as fecha,
            'salidas' as fuente
            FROM
                pedexcab
            INNER JOIN pedexdet on
                pedexcab.pedexentre = pedexdet.pedexentre
            INNER JOIN artmov on
                pedexcab.movref = artmov.movref
            LEFT JOIN assig_ped on
                pedexcab.pedexentre = assig_ped.pedido
            WHERE
                pedexcab.siturefe IN ('CE', 'PP','PD')
                and pedexcab.fecllegada BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedexcab.fecllegada
        UNION ALL
        select
            count(*) as cantidad,
            pedrecab.pedrefec as fecha,
            'entradas' as fuente
            FROM
                pedrecab
            INNER JOIN pedredet on
                pedrecab.pedrefer = pedredet.pedrefer
            WHERE
                pedrecab.pedresitu in ('CE', 'UB', 'PP','PD')
                and pedrecab.pedrefec BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedrecab.pedrefec
        UNION ALL
        select
            count(*) as cantidad,
            pedubicab.fecubi as fecha,
            'maquinista' as fuente
            from
                pedubicab
            INNER JOIN pedubidet on
                pedubicab.pedubicod = pedubidet.pedubicod
            INNER JOIN artmov on
                pedubicab.pedclase = artmov.pedclase
            WHERE
                pedubidet.expst = '1'
                and pedubicab.fecubi BETWEEN '$fecha_inicio' and '$fecha_fin'
            GROUP BY
                pedubicab.fecubi ) as fulldata
    ";

    $sql = "SELECT SUM(cantidad) as cantidad, fuente, fecha FROM ($sq) as datos GROUP BY fuente,fecha order by fecha ASC";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

function convertirDatos($datos)
{
    $result = [];
    foreach ($datos as $entry) {
        $entry = (object) $entry;
        $fecha = $entry->fecha;
        $cantidad = (int)$entry->cantidad;

        // Initialize the result array for the given date
        if (!isset($result[$fecha])) {
            $result[$fecha] = [
                'fecha' => $fecha,
                'entrada' => 0,
                'salida' => 0,
                'maquinista' => 0,
            ];
        }

        // Update the result array based on the "fuente"
        if ($entry->fuente === 'entradas') {
            $result[$fecha]['entrada'] += $cantidad;
        } elseif ($entry->fuente === 'salidas') {
            $result[$fecha]['salida'] += $cantidad;
        } elseif ($entry->fuente === 'maquinista') {
            $result[$fecha]['maquinista'] += $cantidad;
        }
    }
    // Convert the result associative array to a plain array
    return array_values($result);
}

function convertirDatosHora($datos)
{
    $filteredResult = array();

    $result = [];
    foreach ($datos as $entry) {
        $entry = (object) $entry;
        $hora = $entry->hora;
        $cantidad = (int)$entry->cantidad;

        // Initialize the result array for the given date
        if (!isset($result[$hora])) {
            $result[$hora] = [
                'hora' => $hora,
                'entrada' => 0,
                'salida' => 0,
                'maquinista' => 0,
            ];
        }

        // Update the result array based on the "fuente"
        if ($entry->fuente === 'entradas') {
            $result[$hora]['entrada'] += $cantidad;
        } elseif ($entry->fuente === 'salidas') {
            $result[$hora]['salida'] += $cantidad;
        } elseif ($entry->fuente === 'maquinista') {
            $result[$hora]['maquinista'] += $cantidad;
        }
    }
    return array_values($result);
}
function obtenerPorHoraEntrada($db, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT
                *
            FROM
                (
                SELECT
                    SUM(cantidad) as cantidad,
                    hora,
                    fuente
                FROM
                    (
                    SELECT
                        COUNT(*) as cantidad,
                        DATE(pedexcab.fecllegada) as fecha,
                        CONCAT(
                                LPAD(FLOOR(HOUR(pedexcab.horllegada) / 1) * 1 + 1, 2, '00'), ':00'
                            ) as hora,
                        'salidas' as fuente
                    FROM
                        pedexcab
                    INNER JOIN pedexdet ON
                        pedexcab.pedexentre = pedexdet.pedexentre
                    INNER JOIN artmov ON
                        pedexcab.movref = artmov.movref
                    LEFT JOIN assig_ped ON
                        pedexcab.pedexentre = assig_ped.pedido
                    WHERE
                        pedexcab.siturefe IN ('CE', 'PP', 'PD')
                        and pedexcab.fecllegada BETWEEN '$fecha_inicio' and '$fecha_fin'
                    GROUP BY
                        fecha,
                        hora,
                        fuente) as datos
                GROUP BY
                    hora
            UNION ALL
                SELECT
                    SUM(cantidad) as cantidad,
                    hora,
                    fuente
                FROM
                    (
                    select
                        count(*) as cantidad,
                        'entradas' as fuente,
                        pedrecab.pedrefec as fecha,
                        CONCAT(LPAD(FLOOR(HOUR(pedrecab.pedrehor) / 1) * 1 + 1, 2, '00'), ':00') as hora
                    FROM
                        pedrecab
                    INNER JOIN pedredet on
                        pedrecab.pedrefer = pedredet.pedrefer
                    WHERE
                        pedrecab.pedresitu in ('CE', 'UB', 'PP', 'PD')
                        and pedrecab.pedrefec BETWEEN '$fecha_inicio' and '$fecha_fin'
                    GROUP BY
                        pedrecab.pedrefec,
                        hora) as data
                GROUP by
                    hora
            UNION ALL
                SELECT
                    SUM(cantidad) as cantidad,
                    hora,
                    fuente
                FROM
                    (
                    select
                        count(*) as cantidad,
                        pedubicab.fecubi as fecha,
                        'maquinista' as fuente,
                        CONCAT(LPAD(FLOOR(HOUR(pedubicab.horubi) / 1) * 1 + 1, 2, '00'), ':00') as hora
                    from
                        pedubicab
                    INNER JOIN pedubidet on
                        pedubicab.pedubicod = pedubidet.pedubicod
                    INNER JOIN artmov on
                        pedubicab.pedclase = artmov.pedclase
                    WHERE
                        pedubidet.expst = '1'
                        and pedubicab.fecubi BETWEEN '$fecha_inicio' and '$fecha_fin'
                    GROUP BY
                        pedubicab.fecubi) as data2
                GROUP by
                    hora
                    ) as datos order by hora ASC
    ";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

function obtenerPorHoraSalida($db, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT
                *
            from
                (
                SELECT
                    count(*) as cantidad ,
                    CONCAT(LPAD(FLOOR(HOUR(pedexdet.horcie) / 1) * 1 + 1, 2, '00'), ':00'
                                                                        ) as hora,
                    'salidas' as fuente
                FROM
                    pedexcab
                INNER JOIN pedexdet on
                    pedexcab.pedexentre = pedexdet.pedexentre
                INNER JOIN artmov on
                    pedexcab.movref = artmov.movref
                LEFT JOIN assig_ped on
                    pedexcab.pedexentre = assig_ped.pedido
                WHERE
                    pedexcab.siturefe IN ('CE', 'PP') 
                    and pedexdet.fechcie BETWEEN '$fecha_inicio' and '$fecha_fin'
                GROUP BY
                    hora
            UNION ALL
                select
                    count(*) as cantidad,
                    CONCAT(LPAD(FLOOR(HOUR(pedrecab.pedrehorcie) / 1) * 1 + 1, 2, '00'), ':00') as hora,
                    'entradas' as fuente
                FROM
                    pedrecab
                INNER JOIN pedredet on
                    pedrecab.pedrefer = pedredet.pedrefer
                WHERE
                    pedrecab.pedresitu in ('CE', 'UB', 'PP') 
                    and pedrecab.pedrecie BETWEEN '$fecha_inicio' and '$fecha_fin'
                GROUP BY
                    hora
            UNION ALL
                SELECT
                    cantidad,
                    hora_calculada as hora,
                    fuente
                FROM
                    (
                    select
                        count(*) as cantidad,
                        CONCAT(LPAD(FLOOR(HOUR(pedubidet.hora) / 1) * 1 + 1, 2, '00'), ':00') as hora_calculada,
                        'maquinista' as fuente
                    from
                        pedubicab
                    INNER JOIN pedubidet on
                        pedubicab.pedubicod = pedubidet.pedubicod
                    INNER JOIN artmov on
                        pedubicab.pedclase = artmov.pedclase
                    WHERE
                        pedubidet.expst = '1' 
                        and pedubidet.fecha BETWEEN '$fecha_inicio' and '$fecha_fin'
                    GROUP by
                        hora) as datos) as resultados
            order by
                hora ASC
    ";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}