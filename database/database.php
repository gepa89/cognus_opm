<?php

require_once(__DIR__ . '/../index.php');
final class MysqlDB
{
    private static $instance;

    public static function obtenerInstancia()
    {
        $SERVER = $_ENV['DATABASE']['SERVER'];
        $USER = $_ENV['DATABASE']['USER'];
        $PASS = $_ENV['DATABASE']['PASSWORD'];
        $DB = $_ENV['DATABASE']['INSTANCE'];
        try {
            self::$instance = new mysqli($SERVER, $USER, $PASS, $DB);
        } catch (\Throwable $th) {
            throw $th;
        }
        return self::$instance;
    }
    public function cerrarConexion()
    {
        self::$instance->close();
    }
}