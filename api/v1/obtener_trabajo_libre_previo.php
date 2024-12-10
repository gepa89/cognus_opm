<?php
require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../modelos/UbicacionArticulo.php");
require_once(__DIR__ . "/../../modelos/Ubicacion.php");

date_default_timezone_set('America/Asuncion');
define('ALMACEN', 'LDAL');
$datos = [];
$datos['terminal'] = htmlspecialchars($_POST['terminal'] ?? "", ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if required fields are present

    if (empty($datos['terminal'])) {
        $response = array('exito' => false, 'mensaje' => 'Required fields are missing');
        retorna_resultado(200, $response);
    }
    $db = MysqlDB::obtenerInstancia();
    $sqlbuilder = new MySqlQuery($db);
    $resultado = $sqlbuilder->table('asignacion_trabajo_libre')
        ->select('*')
        ->where(['terminal' => $datos['terminal'], 'estado' => 'pendiente'])
        ->executeSelect()
        ->getOne();

    if (!$resultado) {
        $response = array('exito' => true, 'mensaje' => 'No se encontraron trabajos pendientes');
        $db->close();
        retorna_resultado(200, $response);
    }
    $sql = "SELECT SUM(stockubi.canti) as cantidad, stockubi.ubirefer as ubicacion,stockubi.artrefer as articulo, arti.artdesc as descripcion
        FROM stockubi 
        INNER JOIN arti on arti.artrefer = stockubi.artrefer 
        WHERE arti.artrefer = '$resultado->codigo_articulo' and stockubi.ubirefer='$resultado->ubicacion_origen' 
        GROUP BY stockubi.artrefer,stockubi.artrefer ";
    $res = $db->query($sql);
    if (!$res) {
        $error = $db->error;
        $db->close();
        $response = array('exito' => false, 'mensaje' => 'Error al obtener cantidad articulo');
        $db->close();
        retorna_resultado(200, $response);
        exit;
    }

    $detalles = $res->fetch_object();
    if (!$detalles) {
        $response = array('exito' => false, 'mensaje' => 'No se encontraron detalles del stock de la ubicacion de origen');
        retorna_resultado(200, $respuesta);
    }
    $resultado->cantidad_disponible = doubleval($detalles->cantidad) + $resultado->cantidad;
    $resultado->descripcion = $detalles->descripcion;
    $respuesta = array('exito' => true, 'detalles' => $resultado);
    $db->close();
    retorna_resultado(200, $respuesta);
} else {
    $response = array('exito' => false, 'mensaje' => 'Method not Allowed');
    retorna_resultado(200, $response);
}
