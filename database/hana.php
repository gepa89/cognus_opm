<?php
require_once(__DIR__ . '/../index.php');

class HanaDB
{
    private static $instance = null;

    public static function obtenerInstancia()
    {
        if (self::$instance != null) {
            return self::$instance;
        }
        $tipo_conexion = $_ENV['GENERAL']['DATABASE_CONECTION'];
        $SERVER = $_ENV[$tipo_conexion]['SERVER'];
        $USER = $_ENV[$tipo_conexion]['USER'];
        $PASS = $_ENV[$tipo_conexion]['PASSWORD'];
        $DRIVER = $_ENV[$tipo_conexion]['DRIVER'];
        $INSTANCE = $_ENV[$tipo_conexion]['INSTANCE'];
        $dsn = "Driver=$DRIVER;ServerNode=$SERVER;Database=$INSTANCE;LoginTimeout=5;CHAR_AS_UTF8=1;";
        self::$instance = odbc_connect($dsn, $USER, $PASS, SQL_CUR_USE_ODBC);
        if (!self::$instance) {
            throw new Exception("ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg(), 1);
        }
        return self::$instance;
    }
    public static function cerrarConexion($conexion)
    {
        odbc_close($conexion);
    }
    public static function obtenerInstanciaPRD()
    {
        if (self::$instance != null) {
            return self::$instance;
        }
        $tipo_conexion = "SAP_HANA_PRD";
        $SERVER = $_ENV[$tipo_conexion]['SERVER'];
        $USER = $_ENV[$tipo_conexion]['USER'];
        $PASS = $_ENV[$tipo_conexion]['PASSWORD'];
        $DRIVER = $_ENV[$tipo_conexion]['DRIVER'];
        $INSTANCE = $_ENV[$tipo_conexion]['INSTANCE'];
        $dsn = "Driver=$DRIVER;ServerNode=$SERVER;Database=$INSTANCE;LoginTimeout=5;CHAR_AS_UTF8=1;";
        self::$instance = odbc_connect($dsn, $USER, $PASS, SQL_CUR_USE_ODBC);
        if (!self::$instance) {
            throw new Exception("ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg(), 1);
        }
        return self::$instance;
    }

    public static function ejecutarConsulta($instancia, $consulta, $parametros)
    {
        $stmt = odbc_prepare($instancia, $consulta);
        if (!$stmt) {
            throw new Exception("ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg(), 1);
        }
        $estado = odbc_execute($stmt, $parametros);
        if (!$estado) {
            throw new Exception("ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg(), 1);
        }
        return $stmt;
    }
}
