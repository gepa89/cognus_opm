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
    $fecha_descarga = limpiarCadena(@$_POST['fecha_descarga']);
    $sqlbuilder = new MySqlQuery($conn);
    $condicion = ["pedrefer" => $pedido];
    $existe = $sqlbuilder->table("calendario")->where($condicion)->count() > 0;
    $usuario = obtenerUsuario();
    $sqlbuilder = new MySqlQuery($conn);
    if ($existe) {
        $parametros = [
            "fec_descarga" => $fecha_descarga,
            "fec_programacion" => date("Y-m-d"),
            "hor_programacion" => date("H:i:s"),
            "usuario_modificacion" => $usuario,
        ];
        $stmt = $sqlbuilder->table('calendario')
            ->buildUpdate($parametros)->where($condicion)->executeUpdate();
        $respuesta["mensaje"] = "Fecha de descarga actualizada";
    } else {
        $parametros = [
            "pedrefer" => $pedido,
            "fec_descarga" => $fecha_descarga,
            "fec_programacion" => date("Y-m-d"),
            "hor_programacion" => date("H:i:s"),
            "user_programacion" => $usuario,
        ];
        $stmt = $sqlbuilder->table('calendario')
            ->buildInsert($parametros)->execute();
        $respuesta["mensaje"] = "Fecha de descarga guardada";
    }
    $sqlbuilder = new MySqlQuery($conn);
    $parametros = [
        "pedresitu" => "PR"
    ];
    $stmt = $sqlbuilder->table('oc_finiquitada')
        ->buildUpdate($parametros)->where($condicion)->executeUpdate();
}
