<?php

require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once("../utils/conversores.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);



$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

$fecha_inicio = $fecha_inicio . " 00:00:00";
$fecha_fin = $fecha_fin . " 23:59:59";


$sql_1 = obtener_sql_recepcion($fecha_inicio, $fecha_fin)[0];
$sql_2 = obtener_sql_expedicion($fecha_inicio, $fecha_fin)[0];
$sql_3 = obtener_sql_ubicacion($fecha_inicio, $fecha_fin)[0];
$sql_1_cantidad = obtener_sql_recepcion($fecha_inicio, $fecha_fin)[1];
$sql_2_cantidad = obtener_sql_expedicion($fecha_inicio, $fecha_fin)[1];
$sql_3_cantidad = obtener_sql_ubicacion($fecha_inicio, $fecha_fin)[1];

$sql = "$sql_2";
$rs = $db->query($sql);
$datos = array();
while ($row = $rs->fetch_assoc()) {
    $dato = array(
        'pedido' => $row['pedido'],
        'arti' => $row['arti'],
        'artides' => $row['artides'],
        'canpedida' => formatear_numero($row['canpedida']),
        'canprepara' => formatear_numero($row['canprepara']),
        'usuario' => $row['usuario'],
        'fecasig' => $row['fecasig'],
        'horasig' => $row['horasig'],
        'fecierre' => $row['fecierre'],
        'horcierre' => $row['horcierre'],
        'descri' => $row['descri'],
        'codalma' => $row['codalma']
    );
    try {
        $hora_inicio = new DateTime($row['horasig']); // Replace with your start time
        $hora_fin = new DateTime($row['horcierre']);   // Replace with your end time

        // Calculate the difference
        $interval = $hora_inicio->diff($hora_fin);

        // Get the difference in minutes and seconds
        $minutos = $interval->format('%i');
        $segundos = $interval->format('%s');
        $segundos = strlen($segundos) > 1 ? $segundos : "0$segundos";
        $minutos = strlen($minutos) > 1 ? $minutos : "0$minutos";
        $tiempo_respuesta = $minutos . ":" . $segundos;
        if (!$row['horasig']) {
            $tiempo_respuesta = "--:--";
        }
        $dato['tiempo_respuesta'] = $tiempo_respuesta;
        $datos[] = $dato;
    } catch (\Throwable $th) {
        print_r($row);
        print_r($dato);
        print_r($th->getMessage());
        exit;
    }
}

$sql_cantidad = "SELECT sum(cantidad) as cantidad FROM ($sql_1_cantidad UNION ALL $sql_2_cantidad UNION ALL $sql_3_cantidad) as datos";
$rs = $db->query($sql_cantidad);
$cantidad = $rs->fetch_assoc()['cantidad'];
echo json_encode(["datos" => $datos, "cantidad" => $cantidad]);

function obtener_sql_expedicion($fecha_inicio, $fecha_fin)
{
    $condicion_fecha = sprintf(" AND pedexdet.fechcie BETWEEN '%s' AND '%s'", $fecha_inicio, $fecha_fin);
    $sql = "SELECT * from(SELECT
                pedexcab.pedexentre AS pedido,
                pedexdet.artrefer AS arti,
                pedexdet.artdesc AS artides,
                pedexdet.canpedi AS canpedida,
                pedexdet.canprepa AS canprepara,
                pedexdet.usuario AS usuario,
                pedexdet.fechcie AS fecierre,
                pedexdet.horcie AS horcierre,
                artmov.descsen as descri,
                pedexcab.almrefer AS codalma
            FROM
                pedexdet
            INNER JOIN pedexcab ON
                pedexcab.pedexentre = pedexdet.pedexentre
            INNER JOIN artmov on
                pedexcab.movref = artmov.movref
            INNER JOIN assig_ped on
                assig_ped.pedido = pedexcab.pedexentre
            WHERE
                pedexcab.siturefe IN ('CE', 'PP')
                AND pedexdet.canprepa <> '0'
                AND pedexdet.fechcie BETWEEN '2023-11-01 00:00:00' AND '2023-11-23 23:59:59'
            LIMIT 3000) as res 
            GROUP by pedido
                        ";

    $sql_cantidad = "SELECT
                        count(*) AS cantidad
                    FROM
                        pedexdet
                        INNER JOIN pedexcab ON pedexcab.pedexentre = pedexdet.pedexentre
                        INNER JOIN artmov on pedexcab.movref=artmov.movref
                    WHERE
                        pedexcab.siturefe IN ('CE', 'PP')
                        AND pedexdet.canprepa <> '0' 
                        {$condicion_fecha}";
    return [$sql, $sql_cantidad];
}

function obtener_sql_recepcion($fecha_inicio, $fecha_fin)
{
    $condicion_fecha = sprintf(" AND pedrecab.pedrecie BETWEEN '%s' AND '%s'", $fecha_inicio, $fecha_fin);
    $sql = "SELECT
                pedrecab.pedrefer AS pedido,
                pedredet.artrefer AS arti,
                arti.artdesc AS artides,
                pedredet.canpedi AS canpedida,
                pedredet.canprepa AS canprepara,
                movimientos.usuario,
                (
                SELECT
                    MAX(fecasig)
                FROM
                    assig_ped
                WHERE
                    pedido = pedrecab.pedrefer
                ) AS fecasig,
                (
                SELECT
                    MAX(horasig)
                FROM
                    assig_ped
                WHERE
                    pedido = pedrecab.pedrefer
                ) AS horasig,
                pedrecab.pedrecie AS fecierre,
                pedrecab.pedrehorcie AS horcierre,
                artmov.descsen AS descri,
                pedrecab.almrefer AS codalma
            FROM pedredet
            INNER JOIN pedrecab ON pedrecab.pedrefer = pedredet.pedrefer
            INNER JOIN arti ON pedredet.artrefer = arti.artrefer
            INNER JOIN artmov ON pedrecab.movref = artmov.movref
            INNER JOIN movimientos ON movimientos.pedido=pedredet.pedrefer AND movimientos.artrefer=pedredet.artrefer AND clamov = 'UBMUS'
            WHERE pedrecab.pedresitu IN ('CE', 'UB', 'PP')
                AND pedredet.canprepa <> '0' {$condicion_fecha} LIMIT 100
            ";

    $sql_cantidad = "SELECT
                        count(*) AS cantidad
                        FROM pedredet
                        INNER JOIN pedrecab ON pedrecab.pedrefer = pedredet.pedrefer
                        INNER JOIN arti ON pedredet.artrefer = arti.artrefer
                        INNER JOIN artmov ON pedrecab.movref = artmov.movref
                        INNER JOIN movimientos ON movimientos.pedido=pedredet.pedrefer AND movimientos.artrefer=pedredet.artrefer AND clamov = 'UBMUS'
                        WHERE pedrecab.pedresitu IN ('CE', 'UB', 'PP')
                            AND pedredet.canprepa <> '0' {$condicion_fecha}";
    return [$sql, $sql_cantidad];
}

function obtener_sql_ubicacion($fecha_inicio, $fecha_fin)
{
    $condicion_fecha = sprintf(" AND pedubidet.fecha BETWEEN '%s' AND '%s'", $fecha_inicio, $fecha_fin);
    $sql = "SELECT
                pedubidet.pedubicod as pedido,
                pedubidet.artrefer as arti,
                pedubidet.artdesc as artides,
                pedubidet.cantiu as canpedida,
                pedubidet.canubi as canprepara,
                pedubidet.usuario as usuario,
                (
                SELECT
                    MAX(fecasig)
                FROM
                    assig_ped
                WHERE
                    pedido = pedubidet.pedubicod
                            ) AS fecasig,
                (
                SELECT
                    MAX(horasig)
                FROM
                    assig_ped
                WHERE
                    pedido = pedubidet.pedubicod) AS horasig,
                pedubidet.fecha as fecierre,
                pedubidet.hora as horcierre,
                artmov.descsen as descri,
                pedubicab.cod_alma as codalma
            FROM
                pedubidet
            INNER JOIN pedubicab ON
                pedubicab.pedubicod = pedubidet.pedubicod
            INNER JOIN artmov ON
                pedubicab.pedclase = artmov.pedclase
            WHERE
                pedubidet.expst = '1' LIMIT 1000";

    $sql_cantidad = "SELECT
                        count(*) as cantidad
                    FROM
                        pedubidet
                    INNER JOIN pedubicab ON
                        pedubicab.pedubicod = pedubidet.pedubicod
                    INNER JOIN artmov ON
                        pedubicab.pedclase = artmov.pedclase
                    WHERE
                        pedubidet.expst = '1' {$condicion_fecha}";
    return [$sql, $sql_cantidad];
}
