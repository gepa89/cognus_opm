<?php
/*
 * @file sincronizacion_eanes.php
 * @creator edgonzalez
 * @date 2024-08-23
 */
require_once(__DIR__ . "/../index.php");
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../sap_rfc/SapRFC.php");

define('ALMACEN', $_ENV['GENERAL']['ALMACEN']);

class SincronizacionEanes
{
    private mysqli $db;
    private MySqlQuery $queryBuilder;

    public function __construct() {}

    public function iniciarSincronizacion(): void
    {
        $eanes = $this->obtenerEanes();
        foreach ($eanes as $eanData) {
            $this->enviarEanASap($eanData);
        }
    }

    private function obtenerEanes(): array
    {
        $fuenteRegistro = "aplicacion";
        $queryBuilder = new MySqlQuery();
        $sql = "SELECT * FROM artean WHERE fuente = ? AND cod_alma = ? AND sincronizado_sap = false";
        $resultados = $queryBuilder->rawQuery($sql, [$fuenteRegistro, ALMACEN])->getAll();
        $queryBuilder->closeConnection();
        return $resultados;
    }

    private function enviarEanASap(array $eanData): void
    {
        $this->db = MysqlDB::obtenerInstancia();
        $this->db->begin_transaction();

        $this->queryBuilder = new MySqlQuery($this->db);
        $sapRFC = SapRFCConnection::obtenerInstancia();

        $sapFunction = $sapRFC->getFunction('BAPI_MATERIAL_SAVEDATA');
        $sapParams = [
            'HEADDATA' => ['MATERIAL' => $eanData['artrefer']],
            'UNITSOFMEASURE' => [['ALT_UNIT' => $eanData['unidad_medida'], 'EAN_UPC' => $eanData['ean'], 'EAN_CAT' => $eanData['tipo']]],
            'UNITSOFMEASUREX' => [['ALT_UNIT' => $eanData['unidad_medida'], 'EAN_UPC' => 'X', 'EAN_CAT' => 'X']],
        ];
        guardar_log_archivo("Enviando EAN a SAP", $sapParams);
        try {
            $this->actualizarEstadoSincronizacion($eanData);
            $sapResponse = $sapFunction->invoke($sapParams, []);
        } catch (\Throwable $th) {
            guardar_log_archivo($th->getMessage(), $eanData, LOGGER_ERROR);
            $this->db->rollback();
            $this->db->close();
            $sapRFC->close();
            return;
        }
        $sapRFC->close();
        guardar_log_archivo("Respuesta SAP", $sapResponse);

        if ($sapResponse["RETURN"]['TYPE'] === 'S') {
            guardar_log_archivo($sapResponse["RETURN"]["MESSAGE"], $eanData);
            $this->db->commit();
        } else {
            guardar_log_archivo("No se pudo actualizar el EAN", $eanData, LOGGER_ERROR);
            $this->db->rollback();
        }

        $this->db->close();
    }

    private function actualizarEstadoSincronizacion(array $eanData): void
    {
        guardar_log_archivo("Sincronizando EAN", $eanData);

        $this->queryBuilder->reset();
        $updateParams = [
            'sincronizado_sap' => true,
        ];
        $whereConditions = [
            'id' => $eanData['id'],
        ];
        $stmt = $this->queryBuilder->table('artean')
            ->buildUpdate($updateParams)
            ->where($whereConditions)
            ->executeUpdate();

        if ($stmt->errno) {
            throw new Exception($stmt->error, 1);
        }
    }
}

// Start the synchronization process
$sincronizacion = new SincronizacionEanes();
$sincronizacion->iniciarSincronizacion();
