<?php
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");

class UbicacionArticulo
{
    private $db = null;
    public function __construct($db = null)
    {
        $db == null ? $this->db = MysqlDB::obtenerInstancia() : $this->db = $db;
    }

    public static function obtener_ubicacion($material, $ubicacion, $tipo)
    {
        $parametros = [
            'ubirefer' => $ubicacion,
            'artrefer' => $material,
            'ubitipo' => $tipo
        ];
        $sqlBuilder = new MySqlQuery();
        $ubicacion_articulo = $sqlBuilder->table('artubiref')->select('*')->where($parametros)
            ->executeSelect()
            ->getOne();
        return $ubicacion_articulo;
    }
    public function crear_ubicacion($material, $ubicacion, $tipo, $cod_almacen)
    {

        $sqlBuilder = new MySqlQuery($this->db);
        $datos = [
            'ubirefer' => $ubicacion,
            'ubitipo' => $tipo,
            'artrefer' => $material,
            'cod_alma' => $cod_almacen,
            'ubilibre' => 0
        ];
        try {
            $stmt = $sqlBuilder->table('artubiref')->buildInsert($datos)->execute();
        } catch (\Throwable $th) {
            throw new Exception("No se pudo crear ubicacion #1", 1);
        }
        if ($stmt->errno) {
            throw new Exception("No se pudo crear ubicacion #2", 1);
        }
        return $stmt->insert_id;
    }
    public static function obtenerCapacidadUbicacionTipoPS($articulo, $ubicacion)
    {
        $db = MysqlDB::obtenerInstancia();
        $sql = "SELECT
                    capacidad,
                    Coalesce(sum(stockubi.canti), 0) as total_ocupado
                from
                    capaubi
                    inner join ubimapa ON capaubi.dimension = ubimapa.dimension
                    left join stockubi ON stockubi.ubirefer = ubimapa.ubirefer and stockubi.artrefer = capaubi.artrefer
                where
                    ubimapa.ubitipo = 'PS' and capaubi.artrefer = ? and ubimapa.ubirefer = ?
                GROUP BY stockubi.ubirefer, stockubi.artrefer
                ";
        $params = [$articulo, $ubicacion];
        $sqbuilder = new MySqlQuery($db);
        $resultado = $sqbuilder->rawQuery($sql, $params)->getOne();
        $db->close();
        return $resultado;
    }
}
