<?php
require_once(__DIR__ . "/../../conect.php");
require_once(__DIR__ . "/../../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$terminal = $db->real_escape_string($_GET['terminal']);
$usuario = $db->real_escape_string($_GET['usuario']);

/* Obtenemos la terminal con el pedido de inventario asignado que no esten finalizados, procesamos 1 solo pedido a la vez */
$sql = "SELECT assig_ped.pedido 
        FROM assig_ped 
        INNER join termi ON assig_ped.tercod = termi.tercod 
        WHERE termi.tipac = 'INVE' AND termi.tercod='$terminal' AND assig_ped.st <> 1
        order by assig_ped.fecasig ASC LIMIT 1 ";
$res = $db->query($sql);

if (!$res) {
    echo json_encode(['mensaje' => "Error al ejecutar query $db->error"], 500);
    exit;
}

if ($res->num_rows == 0) {
    echo json_encode(['habilitar_inventario' => false, 'mensaje' => "Ningun pedido encontrado"], 200);
    exit;
}

$dato_asignacion = $res->fetch_object();

$sql = "SELECT control1, control2, control3 FROM pedinvecab WHERE pedinve='$dato_asignacion->pedido'";
$res = $db->query($sql);

if (!$res) {
    echo json_encode(['mensaje' => "Error al ejecutar $sql"], 500);
    exit;
}
$nivel_control_pedido = $res->fetch_object();
$nivel_control = 1;
if ($nivel_control_pedido->control2) {
    $nivel_control = 3;
} else if ($nivel_control_pedido->control1) {
    $nivel_control = 2;
}


/*Obtenemos los articulos a controlar que no esten con proceso finalizado es decir con materiales faltantes */
$sql = obtener_query_articulos_pendientes($dato_asignacion->pedido, $nivel_control);

$res = $db->query($sql);

if (!$res) {
    echo json_encode(['mensaje' => "Error al ejecutar query $sql"], 500);
    exit;
}

$articulos_a_controlar = [];
while ($fila = $res->fetch_object()) {
    $articulo = new stdClass();
    $articulo->cod_articulo = $fila->artrefer;
    $articulo->descripcion = $fila->artdesc;
    $articulo->ubicacion = $fila->ubirefer;
    $articulo->pedido = $fila->pedinve;
    $articulo->posicion = $fila->posdoc;
    $articulo->nivel_control = $nivel_control;
    $articulos_a_controlar[] = $articulo;
}
header('Content-type: application/json; charset=utf-8');
$totales = obtener_totales($db, $dato_asignacion->pedido, $nivel_control);
echo json_encode([
    'articulos' => $articulos_a_controlar,
    'totales' => ['pendientes' => $totales[1], 'total' => $totales[0]],
    'habilitar_inventario' => true
]);
exit;



function obtener_query_articulos_pendientes($pedido, $nivel_control)
{
    $condicion = "";
    if ($nivel_control > 1) {
        $nivel_anterior = $nivel_control - 1;
        $condicion = " AND COALESCE(diferencia{$nivel_anterior},1) <> 0";
    }
    return "SELECT * FROM pedinvedet 
    INNER JOIN pedinvecab ON pedinvedet.pedinve = pedinvecab.pedinve 
    WHERE pedinvedet.pedinve = '$pedido' AND pedinvecab.situinve = 'PD' and pedinvedet.expst{$nivel_control} <> 1 $condicion";
}

function obtener_totales($db, $pedido, $nivel)
{
    $total_articulos = 0;
    $pendiente = 0;
   

    $sql = "SELECT * FROM pedinvedet 
    INNER JOIN pedinvecab ON pedinvedet.pedinve = pedinvecab.pedinve 
    WHERE pedinvedet.pedinve = '$pedido' AND pedinvecab.situinve = 'PD'";
    $res = $db->query($sql);

    if (!$res) {
        echo json_encode(['mensaje' => "Error al ejecutar query $db->error"], 500);
        exit;
    }
    while ($fila = $res->fetch_object()) {
        if (is_null($fila->{"diferencia{$nivel}"})) {
            $pendiente += 1;
        }
        $total_articulos += 1;
    }
    return [$total_articulos, $pendiente];
}
