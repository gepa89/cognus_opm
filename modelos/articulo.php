<?php

require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../database/database.php");

class Articulo
{

    static function esTipoPS($articulo)
    {
        $sql = "SELECT * FROM capaubi WHERE artrefer = ?";
        $params = [$articulo];
        $db = MysqlDB::obtenerInstancia();
        $sqlbuilder = new MySqlQuery($db);
        $resultado = $sqlbuilder->rawQuery($sql, $params)->getOne();
        $db->close();
        return $resultado != null;
    }
    static function obtenerUbicacionDeArticuloPS($articulo, $ubicacion)
    {
        $sql = "SELECT * FROM capaubi inner join ubimapa on capaubi.dimension = ubimapa.dimension WHERE artrefer = ? and ubimapa.ubirefer = ?";
        $params = [$articulo, $ubicacion];
        $db = MysqlDB::obtenerInstancia();
        $sqlbuilder = new MySqlQuery($db);
        $resultado = $sqlbuilder->rawQuery($sql, $params)->getOne();
        $db->close();
        return $resultado;
    }
}
