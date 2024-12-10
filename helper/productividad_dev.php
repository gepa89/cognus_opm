<?php

function obtener_sql_expedicion($fecha_inicio, $fecha_fin, $limite, $offset)
{
    $condicion_fecha = sprintf(" AND pedexdet.fechcie BETWEEN '%s' AND '%s'", $fecha_inicio, $fecha_fin);
    $sql = "SELECT
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
            INNER JOIN artmov on pedexcab.movref=artmov.movref

            WHERE
                pedexcab.siturefe IN ('CE', 'PP')
                AND pedexdet.canprepa <> '0' 
                {$condicion_fecha} LIMIT $limite OFFSET $offset
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

function obtener_sql_recepcion($fecha_inicio, $fecha_fin, $limite, $offset)
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
                AND pedredet.canprepa <> '0' {$condicion_fecha} LIMIT $limite OFFSET $offset
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

function obtener_sql_ubicacion($fecha_inicio, $fecha_fin, $limite, $offset)
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
                pedubidet.expst = '1' LIMIT $limite OFFSET $offset";

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
