<?php
require_once(__DIR__ . '/../ExternalLogger.php');
require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

const LOGGER_DEBUG = 'debug';
const LOGGER_INFO = 'info';
const LOGGER_WARNING = 'warning';
const LOGGER_ERROR = 'error';
const HOME_LOGS = '/var/www/html/wmsdz/logs/';

function guardar_info_log($fuente, $mensaje)
{
    $log = new Logger('debug');
    try {
        ExternalLogger::info($fuente, $mensaje, debug_backtrace()[0]['file']);
        $log->pushHandler(new StreamHandler('/var/www/html/wmsdz/logs/log.log', Logger::DEBUG));
        $log->info($mensaje, [$fuente, $mensaje]);
    } catch (\Throwable $th) {
    //    print_r($th->getMessage());
    }
}
function guardar_error_log($fuente, $mensaje, $extra = "")
{
    $log = new Logger('debug');
    try {
        $log->pushHandler(new StreamHandler('/var/www/html/wmsdz/logs/error.log', Logger::DEBUG));
        $log->warning($mensaje, [$fuente, $mensaje, $extra]);
        ExternalLogger::error($fuente, $mensaje, debug_backtrace()[0]['file'], $extra);
    } catch (\Throwable $th) {
        #print_r($th->getMessage());
    }
}
function guardar_sql_log($mensaje)
{
    try {
        ExternalLogger::sql_log("sql", $mensaje, debug_backtrace()[0]['file']);
    } catch (\Throwable $th) {
        print_r($th->getMessage());
    }
}

function guardar_custom_log($fuente, $mensaje)
{
    $log = new Logger('debug');
    try {
        ExternalLogger::custom($fuente, $mensaje, debug_backtrace()[0]['file']);
        $log->pushHandler(new StreamHandler('/var/www/html/wmsdz/logs/info.log', Logger::DEBUG));
        $log->info($mensaje, [$fuente, $mensaje]);
    } catch (\Throwable $th) {
        print_r($th->getMessage());
    }
}

function guardar_entrada_salida_log($fuente, $mensaje)
{
    $file = debug_backtrace()[0]['file'];
    $ahora = date("Y-m-d H:i:s");
    $datos = "$ahora - $fuente - $file - $mensaje \n";

    try {
        error_log($datos, 3, "/var/www/html/wmsdz/logs/entrada_salida.log");
    } catch (\Throwable $th) {
        print_r($th->getMessage());
    }
}

function guardar_sap_log($mensaje)
{
    $file = debug_backtrace()[0]['file'];
    $ahora = date("Y-m-d H:i:s");
    $datos = "$ahora - $file - $mensaje \n";

    try {
        error_log($datos, 3, "../logs/info_sap.log");
    } catch (\Throwable $th) {
        print_r($th->getMessage());
    }
}

function debug_log($mensaje = "", $datos)
{
    $file = debug_backtrace()[0]['file'];
    $log = new Logger('debug');

    try {
        $log->pushHandler(new StreamHandler('/var/www/html/wmsdz/logs/log.log', Logger::DEBUG));
        $log->warning($mensaje, $datos);
    } catch (\Throwable $th) {
        print_r($th->getMessage());
    }
}


function guardar_log_archivo($mensaje, $datos, $nivel = LOGGER_INFO)
{
    $filename = explode("/", debug_backtrace()[0]['file'])[count(explode("/", debug_backtrace()[0]['file'])) - 1];
    $filename = explode(".", $filename)[0];
    $filename = $filename . ".log";
    $filepath = HOME_LOGS . $filename;
    if (!file_exists($filepath)) {
        $file = fopen($filepath, "w");
        fclose($file);
        chmod($filepath, 0777);
    }

    $log = new Logger('debug');

    try {
        $log->pushHandler(new StreamHandler($filepath, $nivel));
        $log->{$nivel}($mensaje, $datos);
    } catch (\Throwable $th) {
    }
}
