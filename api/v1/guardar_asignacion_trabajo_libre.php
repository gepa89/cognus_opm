<?php
require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../modelos/UbicacionArticulo.php");
require_once(__DIR__ . "/../../modelos/Ubicacion.php");

date_default_timezone_set('America/Asuncion');

define('ALMACEN', $_ENV['GENERAL']['ALMACEN']);

$datos = [];
$datos['usuario'] = htmlspecialchars($_POST['usuario'], ENT_QUOTES, 'UTF-8');
$datos['codigo_articulo'] = htmlspecialchars($_POST['codigo_articulo'], ENT_QUOTES, 'UTF-8');
$datos['ubicacion_origen'] = htmlspecialchars($_POST['ubicacion_origen'], ENT_QUOTES, 'UTF-8');
$datos['cantidad'] = (float) htmlspecialchars($_POST['cantidad'], ENT_QUOTES, 'UTF-8');
$datos['terminal'] = htmlspecialchars($_POST['terminal'], ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if required fields are present


    $actualizar = isset($_POST['id_trabajo_libre']) && !empty($_POST['id_trabajo_libre']);

    if (empty($datos['usuario']) || empty($datos['codigo_articulo']) || empty($datos['ubicacion_origen']) || empty($datos['cantidad']) || empty($datos['terminal'])) {
        $response = array('exito' => false, 'mensaje' => 'Required fields are missing');
        retorna_resultado(200, $response);
    }

    if (!$actualizar) {
        guardar_registro($datos);
    } else {
        actualizar_registro($datos);
    }
}

function guardar_registro($datos)
{
    $db = MysqlDB::obtenerInstancia();
    $db->begin_transaction();
    $sqlbuilder = new MySqlQuery($db);
    $datos['fecha_creacion'] = date('Y-m-d H:i:s');
    $datos['estado'] = 'pendiente';
    $stmt = $sqlbuilder->table('asignacion_trabajo_libre')
        ->buildInsert($datos)->execute();
    if ($stmt->errno) {
        $db->rollback();
        $db->close();
        $response = array('exito' => false, 'mensaje' => 'Error al guardar movimiento de articulos');
        retorna_resultado(200, $response);
    }
    $id_asignacion = $stmt->insert_id;
    $datos = [
        "cod_alma" => ALMACEN,
        "ubirefer" => $datos["ubicacion_origen"],
        "artrefer" => $datos["codigo_articulo"],
        "canti" => $datos["cantidad"] * (-1),
        "etnum" => "salida",
        "fecingre" => date('Y-m-d')
    ];
    $sqlbuilder = new MySqlQuery($db);
    $stmt = $sqlbuilder->table('stockubi')
        ->buildInsert($datos)->execute();
    if ($stmt->errno) {
        $db->rollback();
        $db->close();
        $response = array('exito' => false, 'mensaje' => 'Error al guardar stock ubicacion origen');
        retorna_resultado(200, $response);
    }
    $db->commit();
    $db->close();
    $response = array('exito' => true, 'mensaje' => 'Datos guardados', 'detalles' => ["id_trabajo_libre" => $id_asignacion]);
    retorna_resultado(200, $response);
}
function actualizar_registro($datos)
{
    $datos['id_trabajo_libre'] = htmlspecialchars($_POST['id_trabajo_libre'] ?? "", ENT_QUOTES, 'UTF-8');
    $datos['ubicacion_destino'] = htmlspecialchars($_POST['ubicacion_destino'] ?? "", ENT_QUOTES, 'UTF-8');

    $db = MysqlDB::obtenerInstancia();
    $db->begin_transaction();
    $sqlbuilder = new MySqlQuery($db);
    $condicion = ['id' => $datos['id_trabajo_libre']];
    $parametros = [
        'estado' => 'completado',
        'fecha_finalizacion' => date('Y-m-d H:i:s'),
        'ubicacion_destino' => $datos['ubicacion_destino']
    ];
    $stmt = $sqlbuilder->table('asignacion_trabajo_libre')
        ->buildUpdate($parametros)->where($condicion)->executeUpdate();
    if ($stmt->errno) {
        $db->rollback();
        $db->close();
        $response = array('exito' => false, 'mensaje' => 'Error al actualizar movimiento de articulos');
        retorna_resultado(200, $response);
    }

    $stock_ubicacion = [
        "cod_alma" => ALMACEN,
        "ubirefer" => $datos["ubicacion_destino"],
        "artrefer" => $datos["codigo_articulo"],
        "canti" => $datos["cantidad"],
        "etnum" => "entrada",
        "fecingre" => date('Y-m-d')
    ];

    $sqlbuilder = new MySqlQuery($db);
    $stmt = $sqlbuilder->table('stockubi')
        ->buildInsert($stock_ubicacion)->execute();
    if ($stmt->errno) {
        $db->rollback();
        $db->close();
        $response = array('exito' => false, 'mensaje' => 'Error al guardar stock ubicacion origen');
        retorna_resultado(200, $response);
    }
    $ubicacion = Ubicacion::obtener_ubicacion($datos["ubicacion_destino"]);
    
    $ubicacion_articulo = UbicacionArticulo::obtener_ubicacion($datos["codigo_articulo"], $datos["ubicacion_destino"], $ubicacion->ubitipo);

    if (!$ubicacion_articulo) {
        $ubicacionArticulo = new UbicacionArticulo($db);

        try {
            $ubicacionArticulo->crear_ubicacion($datos["codigo_articulo"], $datos["ubicacion_destino"], $ubicacion->ubitipo, ALMACEN);
        } catch (\Throwable $th) {
            $db->rollback();
            $db->close();
            $response = array('exito' => false, 'mensaje' => 'No se pudo crear ubicacion de referencia');
            retorna_resultado(200, $response);
        }
    }
    $db->commit();
    $db->close();
    $response = array('exito' => true, 'mensaje' => 'Transpaso Realizado');
    retorna_resultado(200, $response);
}
