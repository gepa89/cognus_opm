<?php
require_once(__DIR__ . "/../index.php");

class SapRFCConnection
{
    private static $instance;

    public static function obtenerInstancia()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }
        $config = [
            'ashost' => $_ENV['SAP_RFC_PRD']['HOST'],
            'sysnr' => $_ENV['SAP_RFC_PRD']['SYSNR'],
            'client' => $_ENV['SAP_RFC_PRD']['CLIENT'],
            'user' => $_ENV['SAP_RFC_PRD']['USER'],
            'passwd' => $_ENV['SAP_RFC_PRD']['PASSWORD'],
            'trace' => SAPNWRFC\Connection::TRACE_LEVEL_OFF,
        ];
        self::$instance = new SAPNWRFC\Connection($config);
        return self::$instance;
    }
    public static function cerrarConexion()
    {
        self::$instance->close();
    }
}
