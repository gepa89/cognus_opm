<?php
require_once('vendor/autoload.php');
require_once('index.php');

use GuzzleHttp\Client;



class ExternalLogger
{
    public static function debug($fuente, $message, $desde)
    {
        $datos = [
            'fuente' => $fuente,
            'mensaje' => $message,
            'desde' => $desde,
            'fecha' => date("Y-m-d H:i:s")
        ];
        $url = $_ENV['EXTERNAL_LOGGER']['DEBUG_URI'];

        self::send($url, $datos);
    }
    public static function info($fuente, $message, $desde)
    {
        $datos = [
            'fuente' => $fuente,
            'mensaje' => $message,
            'desde' => $desde,
            'fecha' => date("Y-m-d H:i:s")
        ];
        $url = $_ENV['EXTERNAL_LOGGER']['INFO_URI'];
        self::send($url, $datos);
    }
    public static function error($fuente, $message, $desde, $extra = "")
    {
        $datos = [
            'fuente' => $fuente,
            'mensaje' => $message,
            'desde' => $desde,
            'fecha' => date("Y-m-d H:i:s"),
            'extra' => $extra
        ];
        $url = $_ENV['EXTERNAL_LOGGER']['ERROR_URI'];
        self::send($url, $datos);
    }

    public static function sql_log($fuente, $message, $desde)
    {
        $datos = [
            'fuente' => $fuente,
            'mensaje' => $message,
            'desde' => $desde,
            'fecha' => date("Y-m-d H:i:s")
        ];
        $url = $_ENV['EXTERNAL_LOGGER']['SQL_URI'];
        self::send($url, $datos);
    }
    public static function custom($fuente, $message, $desde)
    {
        $datos = [
            'fuente' => $fuente,
            'mensaje' => $message,
            'desde' => $desde,
            'fecha' => date("Y-m-d H:i:s")
        ];
        $url = $_ENV['EXTERNAL_LOGGER']['CUSTOM_URI'];
        self::send($url, $datos);
    }

    private static function send($url, $datos)
    {
        try {
            $client = new Client([
                'base_uri' => $_ENV['EXTERNAL_LOGGER']['URL'],
                'timeout'  => 2.0,
            ]);
            $res = $client->postAsync($url, [
                'auth' => [$_ENV['EXTERNAL_LOGGER']['USER'], $_ENV['EXTERNAL_LOGGER']['PASSWORD']],
                'json' => $datos
            ])->then(function ($response) {
            });
            $res->wait();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
?>
