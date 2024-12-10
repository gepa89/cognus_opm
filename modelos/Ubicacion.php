<?php
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");

class Ubicacion
{
    public function __construct()
    {
    }

    public static function obtener_ubicacion($ubicacion)
    {
        $parametros = [
            'ubirefer' => $ubicacion,
        ];
        $sqlBuilder = new MySqlQuery();
        $ubicacion = $sqlBuilder->table('ubimapa')->select('*')->where($parametros)
            ->executeSelect()
            ->getOne();
        return $ubicacion;
    }
}
