<?php
//ini_set('display_errors', '1');

require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../utils/sanitizador.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../index.php");

define('ALMACEN', $_ENV['GENERAL']['ALMACEN']);
define('FUENTE_REGISTRO', 'aplicacion');

// Log the incoming request data
guardar_log_archivo("EAN recibido", $_POST);

// Define the required fields
$requiredFields = [
    'ean' => 'EAN',
    'usuario' => 'Usuario',
    'material' => 'Material',
    'unimed' => 'Unidad de Medida'
];

// Validate and sanitize inputs
$validatedInput = validarCamposFaltantes($_POST, $requiredFields);
$eanLeido = strtoupper($validatedInput['ean']);
$usuario = $validatedInput['usuario'];
$material = strtoupper($validatedInput['material']);
$unimed = $validatedInput['unimed'];

// Handle material formatting
if (is_numeric($material)) {
    $material = str_pad($material, 18, "0", STR_PAD_LEFT);
}

// Determine EAN type
$tipo = calcularTipoEan($eanLeido);

// Database connection and transaction handling
$db = MysqlDB::obtenerInstancia();
$sqlbuilder = new MySqlQuery($db);

try {
    $db->begin_transaction();

    $parametros = [
        'ean' => $eanLeido,
        'artrefer' => $material,
        'usuario' => $usuario,
        'fecaut' => date("Y-m-d H:i:s"),
        'cod_alma' => ALMACEN,
        'tipo' => $tipo,
        'fuente' => FUENTE_REGISTRO,
        'unidad_medida' => formatUnidadMedida($unimed)
    ];

    $res = $sqlbuilder->table('artean')->buildInsert($parametros)->execute();

    if ($res->errno) {
        throw new Exception("Error en la inserción del EAN: " . $res->error);
    }

    guardar_log_archivo("EAN guardado exitosamente", $parametros);
    $db->commit();
    $db->close();
    enviarRespuestaSuccess("EAN registrado correctamente");
} catch (Exception $e) {
    $db->rollback();
    guardar_log_archivo($e->getMessage(), $parametros, LOGGER_ERROR);
    $db->close();
    enviarRespuestaError("No se pudo registrar el EAN. Error: " . $e->getMessage());
}

/**
 * Validate required fields and sanitize input data.
 *
 * @param array $inputData
 * @param array $requiredFields
 * @return array
 */
function validarCamposFaltantes(array $inputData, array $requiredFields): array
{
    $sanitizedData = [];

    foreach ($requiredFields as $field => $displayName) {
        $sanitizedData[$field] = sanitizar($inputData[$field] ?? '');

        if (empty($sanitizedData[$field])) {
            $mensaje = "El campo '$displayName' es obligatorio.";
            guardar_log_archivo($mensaje, [], LOGGER_ERROR);
            enviarRespuestaError($mensaje);
            exit;
        }
    }

    return $sanitizedData;
}

/**
 * Calculate the EAN type based on the length and content of the EAN
 *
 * @param string $eanLeido
 * @return string
 */
function calcularTipoEan(string $eanLeido): string
{
    $longitud = strlen($eanLeido);
    if ($eanLeido[0] === '0' && ($longitud < 8 || $longitud > 18)) {
        $mensaje = "EAN con longitud incorrecta";
        guardar_log_archivo($mensaje, [$longitud], LOGGER_ERROR);
        enviarRespuestaError($mensaje);
        exit;
    }

    return ($eanLeido[0] === '0') ? str_pad($longitud, 2, "0", STR_PAD_LEFT) : 'C3';
}

/**
 * Format the unit of measure according to the business rules
 *
 * @param string $unimed
 * @return string
 */
function formatUnidadMedida(string $unimed): string
{
    return $unimed === "UN" ? "ST" : $unimed;
}

/**
 * Send a success response in JSON format
 *
 * @param string $mensaje
 * @return void
 */
function enviarRespuestaSuccess(string $mensaje): void
{
    retorna_resultado(200, ['mensaje' => $mensaje, 'error' => false]);
}

/**
 * Send an error response in JSON format
 *
 * @param string $mensaje
 * @return void
 */
function enviarRespuestaError(string $mensaje): void
{
    retorna_resultado(200, ['mensaje' => $mensaje, 'error' => true]);
    exit;
}
