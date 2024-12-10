<?php
require_once("../helpers/guardar_trabajo_maquinista.php");
require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../database/database.php");

$db = MysqlDB::obtenerInstancia();
$terminal = strtoupper($_POST['terminal']);
$multireferencia = strtoupper($_POST['multirefencia']);
$ubicacion_pedido = strtoupper($_POST['pedubicod']);
$respuesta = array("estado" => "exito", "mensaje" => "");
guardar_info_log("asignar_trabajo_maquinista_recepcion", json_encode($_POST));
try {
    $respuesta = asignar_trabajo_maquinista($db, $terminal, $multireferencia, $ubicacion_pedido);
} catch (\Throwable $th) {
    //throw $th;
    guardar_error_log("asignar_trabajo_maquinista_recepcion", $th->getMessage());
    $respuesta['estado'] = "falla";
    $respuesta['mensaje'] = $th->getMessage();
}
echo json_encode($respuesta);
