<?php
require(__DIR__ . '/../../conect.php');
require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../modelos/articulo.php");


$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
//$db->begin_transaction();

$db->set_charset("utf8");
$ubicacion = strtoupper(trim($_POST['ubicacion_ref']));
$articulo = strtoupper(trim($_POST['articulo']));

$es_articulo_tipo_ps = Articulo::esTipoPS($articulo);
if ($es_articulo_tipo_ps) {
    $ubicacion_articulo = Articulo::obtenerUbicacionDeArticuloPS($articulo, $ubicacion);
    $mensaje = $ubicacion_articulo ? "" : "Ubicacion no encontrada";
    retorna_resultado(200, ['mensaje' => $mensaje, 'existe_ubicacion' => $ubicacion_articulo != null, "detalles" => $ubicacion_articulo]);
}

$sql = "SELECT * FROM ubimapa WHERE ubirefer = '$ubicacion' and ubitipo in ('PI','RE')";
try {
    guardar_sql_log($sql);
} catch (\Throwable $th) {
    //throw $th;
}
$res = $db->query($sql);
if (!$res) {
    retorna_resultado(500, ["mensaje" => "error al obtener ubicacion"]);
}
$mensaje = "";
if ($res->num_rows == 0) {
    $mensaje = "Ubicacion no encontrada";
}
retorna_resultado(200, ['mensaje' => $mensaje, 'existe_ubicacion' => $res->num_rows > 0, "detalles" => $res->fetch_object()]);
