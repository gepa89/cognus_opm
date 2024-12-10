<?php

function formatear_numero($numero): string
{
    if ($numero === null || $numero === "") {
        return "";
    }
    $valor = (float) $numero;
    $copia = (int) $numero;

    if (($copia - $valor) == 0) {
        return $copia;
    }
    return number_format($valor, 3, ',', '');
}
function formatear_fecha($fecha, $formato = 'd/m/Y')
{
    if ($fecha == null || $fecha == "") {
        return "";
    }
    return date($formato, strtotime($fecha));
}
