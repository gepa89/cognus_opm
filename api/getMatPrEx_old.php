<?php
require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . '/../utils/respuesta.php');
require_once(__DIR__ . '/../logger/logger.php');

$db = MysqlDB::obtenerInstancia();
$GLOBALS['db'] = $db;
$terminal = $db->real_escape_string(strtoupper($_GET['terminal']));
$response = array();

$pedido = obtenerPedidosAsignados($terminal);
$zona_terminal = obtenerZonaDeTerminal($terminal);



$db->close();
function obtenerPedidosAsignados($terminal)
{
    guardar_info_log(__FILE__, "obtener pedidos");
    $db = $GLOBALS['db'];
    $sq = "SELECT * FROM ped_multiref WHERE terminal = '$terminal' and codst = 0 limit 1";
    $query = $db->query($sq);
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        $respuesta = [
            "error" => true,
            "mensaje" => "Error de query",
            "pedidos_con_reposicion" => []
        ];

        retorna_resultado(500, $respuesta);
    }
    if ($query->num_rows == 0) {
        $respuesta = [
            "error" => true,
            "mensaje" => "No existe pedidos asignados",
            "pedidos_con_reposicion" => []
        ];
        retorna_resultado(200, $respuesta);
    }
    return $query->fetch_object();
}

function obtenerZonaDeTerminal($terminal)
{
    guardar_info_log(__FILE__, "obtener zona terminal");
    $db = $GLOBALS['db'];
    $sq = "SELECT terzonpre from termi where tercod = '{$terminal}' limit 1";
    $query = $db->query($sq);
    if (!$query) {
        guardar_error_log(__FILE__, $db->error);
        $db->close();
        $respuesta = [
            "error" => true,
            "mensaje" => "Error de query",
            "pedidos_con_reposicion" => []
        ];

        retorna_resultado(500, $respuesta);
    }
    if ($query->num_rows == 0) {
        guardar_error_log(__FILE__, "zona de terminal no encontrada - " . $sq);

        $respuesta = [
            "error" => true,
            "mensaje" => "Ocurrio un error",
            "pedidos_con_reposicion" => []
        ];
        retorna_resultado(200, $respuesta);
    }
    return $query->fetch_object();
}





function SepararUbicacion($ubi)
{
    //$ubi = filter_var($ubi, FILTER_SANITIZE_NUMBER_INT); 
    $separador = "-";
    $separada = explode($separador, $ubi);
    $separada[2] = substr($separada[2], 0, 2);
    return $separada;
}

function OrdenarArray($data, $columna)
{
    $orden1 = array();
    foreach ($data as $clave => $fila) {
        $orden1[$clave] = $fila[$columna];
    }
    return $orden1;
}

function obtenerPedidosAsignadosConReposicionPendientes($db, $pedidoActual)
{
    $sql = "SELECT * FROM pedexdet 
    WHERE  pedexentre = '$pedidoActual' 
    AND repocurso = true 
    AND expst=0 ";
    $resultado = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}