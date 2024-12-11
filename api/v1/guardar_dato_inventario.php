<?php
require_once(__DIR__ . "/../../conect.php");
require_once(__DIR__ . "/../../logger/logger.php");
header('Content-type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit;
}
$usuario = htmlspecialchars($_POST['usuario'], ENT_QUOTES, 'UTF-8');
$pedido = htmlspecialchars($_POST['pedido'], ENT_QUOTES, 'UTF-8');
$codigo_articulo = htmlspecialchars($_POST['codigo_articulo'], ENT_QUOTES, 'UTF-8');
$cantidad = (float) htmlspecialchars($_POST['cantidad'], ENT_QUOTES, 'UTF-8');
$ubicacion = htmlspecialchars($_POST['ubicacion'], ENT_QUOTES, 'UTF-8');
$posicion = htmlspecialchars($_POST['posicion'], ENT_QUOTES, 'UTF-8');
$nivel_control = (int) htmlspecialchars($_POST['nivel_control'], ENT_QUOTES, 'UTF-8');
$terminal = htmlspecialchars($_POST['terminal'], ENT_QUOTES, 'UTF-8');

$total_actual_ubicacion = obtener_cantidad_articulo($db, $codigo_articulo, $ubicacion);

$diferencia = $cantidad - $total_actual_ubicacion;
$campo_dif_siguiente = "";
$campo_expst_siguiente = "";
$campo_fecha_sig = "";
$campo_hora_sig = "";
$fecha = date('Y-m-d');
$hora = date(' H:i:s');
if ($diferencia == 0) {
    if ($nivel_control == 1) {
        $campo_dif_siguiente = "
        diferencia2='0',
        expst2=true,
        fecontrol2='{$fecha}',
        horcontrol2='{$hora}',
        usuario2='{$usuario}',
        termi2='{$terminal}',
        diferencia3='0',
        expst3=true,
        fecontrol3='{$fecha}',
        horcontrol3='{$hora}',
        usuario3='{$usuario}',
        termi3='{$terminal}',
        ";
    } else if ($nivel_control == 2) {
        $nivel_superior = $nivel_control + 1;
        $campo_dif_siguiente = "
        diferencia3='0',
        expst3=true,
        fecontrol3='{$fecha}',
        horcontrol3='{$hora}',
        usuario3='{$usuario}',
        termi3='{$terminal}',
        ";
    }
}

$sql = "UPDATE pedinvedet 
        SET canubi{$nivel_control}=\"$total_actual_ubicacion\", 
            diferencia{$nivel_control}=\"$diferencia\", 
            $campo_dif_siguiente
            canfisi{$nivel_control}=\"$cantidad\",
            fecontrol{$nivel_control}=\"$fecha\",
            horcontrol{$nivel_control}=\"$hora\",
            usuario{$nivel_control}=\"$usuario\",
            termi{$nivel_control}=\"$terminal\",
            expst{$nivel_control}=true
        WHERE pedinve=\"$pedido\" AND artrefer=\"$codigo_articulo\" AND posdoc=\"$posicion\"";

$res = $db->query($sql);

if (!$res) {
    guardar_error_log(__FILE__, $sql);
    http_response_code(500);
    echo json_encode(['mensaje' => "Error al ejecutar $db->error $sql"]);
    exit;
}
$control_terminado = verificar_finalizacion_control($db, $nivel_control, $pedido, $usuario, $terminal);
if ($control_terminado) {
    $db->commit();
    echo json_encode(['mensaje' => "guardado", 'control_finalizado' => true]);
    exit;
}

echo json_encode(["mensaje" => "guardado", "control_finalizado" => false], 200);
exit;
/* Obtenemos la cantidad del material registrado en el sistema */
function obtener_cantidad_articulo($db, $codigo_articulo, $ubicacion)
{
    $sql = "SELECT
        sum(a.canti) as total
        from stockubi a 
        inner join arti b on a.artrefer = b.artrefer 
        inner join ubimapa c on c.ubirefer = a.ubirefer and c.cod_alma = a.cod_alma  
        where a.artrefer ='$codigo_articulo' AND a.ubirefer = '$ubicacion'
        group by a.ubirefer, a.artrefer
        HAVING sum(a.canti) <> 0";
    $res = $db->query($sql);

    if (!$res) {
        echo json_encode(['mensaje' => "Error al ejecutar $sql"], 500);
        exit;
    }

    return $res->fetch_object()->total ?? 0;
}

function verificar_finalizacion_control($db, $nivel_control, $pedido, $usuario, $terminal)
{

    $sql = "SELECT * FROM pedinvedet 
    INNER JOIN pedinvecab ON pedinvedet.pedinve = pedinvecab.pedinve 
    WHERE pedinvedet.pedinve = '$pedido' AND pedinvecab.situinve = 'PD' ";

    $res = $db->query($sql);

    if (!$res) {
        echo json_encode(['mensaje' => "Error al ejecutar query $sql $db->error"], 500);
        exit;
    }
    $completado = true;
    $hay_diferencia = false;
    while ($fila = $res->fetch_object()) {

        if ($fila->{"expst{$nivel_control}"} == 0 && $completado = true) {
            $completado = false;
        }
        if ($fila->{"diferencia{$nivel_control}"} != 0) {
            $hay_diferencia = true || $hay_diferencia;
        }
    }
    if (!$completado) {
        return false;
    }

    $estado = "";
    $diferencia = $hay_diferencia ? "1" : "0";
    if (!$hay_diferencia || $nivel_control == 3) {
        $estado = ",situinve = 'CE'";
    }

    $fecha = date('Y-m-d');
    $hora = date(' H:i:s');


    $sql = "UPDATE pedinvecab 
            SET control{$nivel_control}=true,
            dife{$nivel_control}='$diferencia',
            fecontrol{$nivel_control}='$fecha',
            horcontrol{$nivel_control}='$hora',
            usercontrol{$nivel_control}='$usuario'
            {$estado}
            WHERE pedinve='$pedido'";

    $res = $db->query($sql);

    if (!$res) {
        echo json_encode(['mensaje' => "Error al ejecutar query pedinvedet"], 500);
        exit;
    }

    $sql = "DELETE FROM assig_ped WHERE pedido='$pedido' AND tercod='$terminal'";
    $res = $db->query($sql);
    if (!$res) {
        echo json_encode(['mensaje' => "Error al ejecutar query $sql"], 500);
        exit;
    }

    return true;
    // ya no existen articulos pendientes de control para el nivel seleccionado


}
