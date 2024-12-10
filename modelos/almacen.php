<?php

require_once("database/database.php");

class Almacenes
{
    public static function obtenerAlmacenes(): array
    {
        $almacenes = array();
        $db = MysqlDB::obtenerInstancia();
        $resultado = $db->query("SELECT * FROM alma");
        while ($r = $resultado->fetch_object()) {
            array_push($almacenes, $r);
        }
        $resultado->close();
        return $almacenes;
    }
}

?>