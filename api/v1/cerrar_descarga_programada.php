<?php
session_start();
require_once __DIR__ . "/../../database/database.php";
require_once __DIR__ . "/../../database/SQLBuilder.php";
require_once __DIR__ . "/../../utils/respuesta.php";
require_once __DIR__ . "/../../utils/sanitizador.php";
require_once __DIR__ . "/../../utils/auth.php";

verificarUsuarioLogueadoJSON();
$usuario = obtenerUsuario();

$conn = MysqlDB::obtenerInstancia();
$conn->begin_transaction();
$respuesta = ["exito" => true, "mensaje" => ""];
try {
    main($conn, $respuesta);
    $conn->commit();
} catch (\Throwable $th) {
    $conn->rollback();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = $th->getMessage();
} finally {
    $conn->close();
}
retorna_resultado(200, $respuesta);

function main($conn, &$respuesta)
{
    $pedido = limpiarCadena(@$_POST['pedido']);
    $sqlbuilder = new MySqlQuery($conn);
    $parametros = [
        "pedresitu" => "CE"
    ];
    $stmt = $sqlbuilder->table('oc_finiquitada')
        ->buildUpdate($parametros)->where(["pedrefer" => $pedido])->executeUpdate();
    $respuesta["mensaje"] = "Orden de compra cerrada";
}