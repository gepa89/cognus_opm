<?php function retorna_resultado($codigo, $mensaje)
{
    header('Content-Type: application/json; charset=utf-8', true, $codigo);
    echo json_encode($mensaje);
    exit;
} ?>