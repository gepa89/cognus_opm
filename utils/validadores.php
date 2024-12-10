<?php
function validar_campos($fuente, $campos)
{
    $es_valido = true;
    $mensaje = "";
    foreach ($campos as $campo) {
        $_POST[$campo] = htmlspecialchars(trim($_POST[$campo]));
        if (empty($_POST[$campo])) {
            $mensaje = "{$campo} no seteado";
            $es_valido = false;
            break;
        }
    }
    return ['valido' => $es_valido, 'mensaje' => $mensaje];
}
?>