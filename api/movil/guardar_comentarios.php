<?php

require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
require_once(__DIR__ . "/../../utils/conversores.php");
require_once(__DIR__ . "/../../utils/sanitizador.php");
require_once(__DIR__ . "/../../constantes/constantes.php");
require_once(__DIR__ . "/../../logger/logger.php");


$pedido = sanitizar($_POST['pedido']);
$tipo_error = sanitizar($_POST['tipo_error']);
$comentario = sanitizar($_POST['comentario']);
$usuario = sanitizar($_POST['usuario']);


switch ($tipo_error) {
    case Constantes::ERROR_EAN_INCORRECTO:
        manejarErrorEanIncorrecto($pedido, $comentario, $usuario);
        break;
}
$respuesta = ['mensaje' => "Comentario guardado correctamente"];
retorna_resultado(200, $respuesta);


function manejarErrorEanIncorrecto($pedido, $comentario, $usuario)
{
    $ean = sanitizar($_POST['ean']);
    $articulo = sanitizar($_POST['articulo']);
    $pallet = sanitizar($_POST['pallet']);

    $conn = MysqlDB::obtenerInstancia();
    try {
        $parametros = [
            'codigo_pedido' => $pedido,
            'comentario' => $comentario,
            'usuario' => $usuario,
            'ean_incorrecto' => $ean,
            'codigo_articulo' => $articulo,
            'codigo_comentario' => Constantes::ERROR_EAN_INCORRECTO,
            'fecha_creacion' => date("Y-m-d H:i:s"),
            'numero_pallet' => $pallet
        ];
        $sqlBuilder = new MySqlQuery($conn);
        $sqlBuilder->table('observaciones_recepcion')->buildInsert($parametros)->execute();
    } catch (\Throwable $th) {
        $respuesta = ['mensaje' => "Ocurrio un error"];
        guardar_error_log(__FILE__, "Error al guardar comentario: " . $th->getMessage());
        retorna_resultado(500, $respuesta);
    }
    $conn->close();
}
