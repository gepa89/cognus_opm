<?php
class BaseDeDatos
{

    public static function obtenerInstancia()
    {
        return new mysqli('127.0.0.1', 'u920596356_opm', 'Guidagog2050.', 'u920596356_opm');
    }
}

?>