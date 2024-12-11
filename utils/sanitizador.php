<?php
function sanitizar($campo)
{
    return trim(htmlspecialchars($campo));
}
function limpiarCadena($cadena)
{
    return htmlspecialchars(trim($cadena), ENT_QUOTES, 'UTF-8');
}

?>