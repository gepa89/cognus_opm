<?php

function hojaEtiqueta($url, $texto, $configuracion)
{
    $tamanho_letra = $configuracion['tamanho_letra'];
    $tamanho_barra = $configuracion['tamanho_barra'];
    
    return "<table style=\"width: 100%;\">
    <tr>
      <td style=\"text-align: center;\">
        <img style='width: {$tamanho_barra}px' src=\"data:image/png;base64,$url\" />
      </td>
    </tr>
    <tr>
      <td style=\"text-align:center; font-size: {$tamanho_letra}px;\">$texto</td>
    </tr>

    </table>";
}
?>