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

$datos_dia_preparar  = obtener_datos_dia($db, $fecha_inicio, $fecha_fin, 'PR');
$datos_dia_cerrados  = obtener_datos_dia($db, $fecha_inicio, $fecha_fin, 'CE');

// Output the result
echo json_encode(
    [
        "totales_a_preparar" => procesar_datos($datos_dia_preparar),
        "totales_cerrados" => procesar_datos($datos_dia_cerrados)
    ]
);
exit;

function procesar_datos($datos)
{
    $result = [];
    foreach ($datos as $dato) {
        $fecha = $dato['fecha'];
        $cantidad = $dato['cantidad'];
        $fuente = $dato['fuente'];
        if (!isset($result[$fecha])) {
            $result[$fecha] = [];
        }
        $result[$fecha][$fuente] = $cantidad;
        $result[$fecha]['fecha'] = $fecha;
    }
    return array_values($result);
}

function obtener_datos_dia($db, $fecha_inicio, $fecha_fin, $estado)
{
    $sql = "SELECT 
				(select count(pedrefer) from oc_finiquitada where pedrefer=calendario.pedrefer) as cantidad,
				calendario.fec_descarga as fecha,
				'total_ocs' as fuente
				FROM
						calendario
				INNER JOIN oc_finiquitada on
						calendario.pedrefer = oc_finiquitada.pedrefer
				WHERE
						oc_finiquitada.pedresitu ='$estado'
						and calendario.fec_descarga BETWEEN '$fecha_inicio' and '$fecha_fin'
				GROUP BY
						calendario.fec_descarga
                UNION ALL
                SELECT 
				SUM(can_bultos) as cantidad ,
				calendario.fec_descarga as fecha,
				'total_bultos' as fuente
				FROM
						calendario
				INNER JOIN oc_finiquitada on
						calendario.pedrefer = oc_finiquitada.pedrefer
				WHERE
						oc_finiquitada.pedresitu ='$estado'
						and calendario.fec_descarga BETWEEN '$fecha_inicio' and '$fecha_fin'
				GROUP BY
						calendario.fec_descarga
                UNION ALL
                SELECT 
                count(*) as cantidad ,
                calendario.fec_descarga as fecha,
                'total_contenedor' as fuente
                FROM
                        calendario
                INNER JOIN oc_finiquitada on
                        calendario.pedrefer = oc_finiquitada.pedrefer
                WHERE
                        oc_finiquitada.pedresitu ='$estado'
                        and calendario.fec_descarga BETWEEN '$fecha_inicio' and '$fecha_fin'
                GROUP BY
                        calendario.fec_descarga";
    $sql = "SELECT * from ($sql) as resultado ORDER BY fecha ASC";
    $res = $db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
