<?php

$header = apache_request_headers();
/*if (isset($header['Authorization'])) {
    $authorizationHeader = $header['Authorization'];
    if (strpos($authorizationHeader, 'Bearer ') === 0) {
        $bearerToken = substr($authorizationHeader, 7);

        if (trim($bearerToken) != "101850") {
            echo $bearerToken;
            header('Content-type: application/json; charset=utf-8');
            echo json_encode("No valid Authorization header.");
            exit;
        }
    }else{
        header('Content-type: application/json; charset=utf-8');
        echo json_encode("No valid Authorization header.");
        exit;
    }
} else {
    header('Content-type: application/json; charset=utf-8');
    echo json_encode("No Authorization header found.");
    exit;
}*/

require_once(__DIR__ . '/../../conect.php');
require_once(__DIR__ . '/../../logger/logger.php');
header('Content-type: application/json; charset=utf-8');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$hoy = date('Y-m-d');
/*$sql = "SELECT
            DISTINCT pedido,
            situacion,
            fecha,
            cliente
        FROM
        (
            SELECT
                DISTINCT a.pedexentre as pedido,
                d.situdesref as situacion,
                c.clinom as cliente,
                b.fechcie as fecha,
                b.horcie as hora
            FROM
                pedexcab a
            INNER JOIN pedexdet b on
                a.pedexentre = b.pedexentre
            INNER JOIN clientes c on
                a.clirefer = c.clirefer
            INNER JOIN situped d on
                a.siturefe = d.siturefe
            WHERE
                a.siturefe in ('CE','PP')
                and a.codenv IN ('08','09')
                AND a.pedclase IN ('ZCOS', 'ZCRE')
                and b.fechcie = '{$hoy}'
                 
                and a.entregado is NULL
            ORDER BY
                hora DESC
        ) 
        AS datos LIMIT 7";
$res = $db->query($sql);
if (!$res) {
    print_r($db->error);
}
$datos = $res->fetch_all();
$sql = "SELECT
        DISTINCT pedido,
        situacion,
        cliente
        from
        (
        SELECT
            DISTINCT a.pedexentre as pedido,
            d.situdesref as situacion,
            c.clinom as cliente
        FROM
            pedexcab a
        INNER JOIN pedexdet b on
            a.pedexentre = b.pedexentre
        INNER JOIN clientes c on
            a.clirefer = c.clirefer
        INNER JOIN situped d on
            a.siturefe = d.siturefe
        INNER JOIN assig_ped e on
            a.pedexentre = e.pedido
        WHERE
            e.st = '0'
            AND a.pedclase IN ('ZCOS', 'ZCRE')
            and a.codenv IN ('08','09')
        ORDER BY
            pedido DESC) as datos";
$res = $db->query($sql);
$en_preparacion = $res->fetch_all();*/
$sql = "SELECT
        DISTINCT pedido,
        situacion,
        cliente,
        fecha
        FROM
        (
        SELECT
            DISTINCT a.pedexentre as pedido,
            d.situdesen as situacion,
            c.clinom as cliente,
            b.fechcie as fecha
        FROM
            pedexcab a
        INNER JOIN pedexdet b on
            a.pedexentre = b.pedexentre
        INNER JOIN clientes c on
            a.clirefer = c.clirefer
        INNER JOIN situped d on
            a.siturefe = d.siturefe
        WHERE
            a.siturefe = 'CE'
            AND a.pedclase IN ('ZCOS', 'ZCRE')
            and b.fechcie = '{$hoy}'
            and a.entregado = '1'
        order by
            b.horcie desc) AS datos LIMIT 7";
$res = $db->query($sql);
$entregado = $res->fetch_all();
$db->close();
echo json_encode([
    /*'preparados' => $datos,
    'preparacion' => $en_preparacion,*/
    'entregados' => $entregado
]);
