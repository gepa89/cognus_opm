<?php
/*
 * @file stock_fecha.php
 * @creator edgonzalez
 * @date 2024-08-29
 */
require_once(__DIR__ . "/../index.php");
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");

define('ALMACEN', $_ENV['GENERAL']['ALMACEN']);


class StockDiarioJob
{

    private $db;
    private $sqlbuilder;

    public function __construct()
    {
        $this->db = MysqlDB::obtenerInstancia();
        $this->sqlbuilder = new MySqlQuery($this->db);
    }

    public function ejecutar(): void
    {
        $sql = "SELECT 
                    art.artrefer AS articulo,
                    art.artdesc AS descripcion,
                    stock.ubirefer AS ubicacion,
                    stock.cod_alma AS almacen,
                    map.ubitipo AS tipo_ubicacion,
                    SUM(stock.canti) AS cantidad_total
                FROM 
                    stockubi AS stock
                INNER JOIN 
                    arti AS art ON stock.artrefer = art.artrefer
                INNER JOIN 
                    ubimapa AS map ON map.ubirefer = stock.ubirefer 
                                    AND map.cod_alma = stock.cod_alma
                WHERE 
                    stock.cod_alma = ?
                GROUP BY 
                    stock.ubirefer, 
                    stock.artrefer, 
                    stock.cod_alma;
                ";
        $stmt = $this->sqlbuilder->rawQuery($sql, [ALMACEN])->getSTMTObject();
        $this->db->begin_transaction();
        while ($fila = $stmt->fetch_object()) {
            $this->ingresarStockDiario($fila);
        }
        $this->db->close();
    }

    private function ingresarStockDiario($datos): void
    {
        $fecha = date("Y-m-d");
        try {
            if ($this->existeRegistroStockDiarioDelDia($datos->articulo, $datos->ubicacion, $fecha)) {
                $this->actualizarStockDiario(
                    $datos->articulo,
                    $datos->descripcion,    
                    $datos->ubicacion,
                    $datos->almacen,
                    $datos->tipo_ubicacion,    
                    $datos->cantidad_total,
                    $datos->cantidad_total,
                    $fecha
                );
            } else {
                $this->insertarStockDiario(
                    $datos->articulo,
                    $datos->descripcion,      
                    $datos->ubicacion,
                    $datos->almacen,
                    $datos->tipo_ubicacion,     
                    $datos->cantidad_total,
                    $datos->cantidad_total,
                    $fecha
                );
            }
            $this->db->commit();
        } catch (\Throwable $th) {
            guardar_log_archivo($th->getMessage(), $datos, LOGGER_ERROR);
        }
    }

    private function insertarStockDiario($articulo,  $descripcion, $ubicacion, $almacen, $tipo_ubicacion,  $totalArticulo, $totalUbicacion, $fecha): void
    {
        guardar_log_archivo("Insertando stock diario para el articulo $articulo en la ubicacion $ubicacion", [], LOGGER_INFO);
        $this->sqlbuilder->reset();
        $parametros = [
            "artrefer" => $articulo,
            "artdesc" => $descripcion,
            "ubirefer" => $ubicacion,
            "cod_alma" => $almacen,
            "ubitipo" => $tipo_ubicacion,
            "totalarti" => $totalArticulo,
            "totalubi" => $totalUbicacion,
            "fecha" => $fecha,
            "hora" => date("H:i:s")
        ];
        $res = $this->sqlbuilder->table('stock_diario')->buildInsert($parametros)->execute();
    }
    private function existeRegistroStockDiarioDelDia($articulo, $ubicacion, $fecha)
    {
        guardar_log_archivo("Verificando si existe registro de stock diario para el articulo $articulo en la ubicacion $ubicacion", [$articulo, $ubicacion, $fecha], LOGGER_INFO);

        $this->sqlbuilder->reset();
        $sql = "SELECT * FROM stock_diario WHERE artrefer = ? AND ubirefer = ? AND fecha = ?";
        return $this->sqlbuilder->rawQuery($sql, [$articulo, $ubicacion, $fecha])->getOne() != null;
    }
    private function actualizarStockDiario($articulo, $descripcion, $ubicacion, $almacen, $tipo_ubicacion, $totalArticulo, $totalUbicacion, $fecha): void
    {
        guardar_log_archivo("Actualizando stock diario para el articulo $articulo en la ubicacion $ubicacion", [$totalArticulo, $totalUbicacion], LOGGER_INFO);

        $this->sqlbuilder->reset();
        $parametros = [
            "totalarti" => $totalArticulo,
            "totalubi" => $totalUbicacion,
            "hora" => date("H:i:s")
        ];
        $where = [
            "artrefer" => $articulo,
            "artdesc" => $descripcion,
            "ubirefer" => $ubicacion,
            "cod_alma" => $almacen,
            "ubitipo" => $tipo_ubicacion,
            "fecha" => $fecha
        ];
        $res = $this->sqlbuilder->table('stock_diario')->buildUpdate($parametros)->where($where)->executeUpdate();
    }
}
$job = new StockDiarioJob();
$job->ejecutar();
