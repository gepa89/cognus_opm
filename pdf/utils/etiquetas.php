<?php

function obtenerConfiguracionEtiqueta($indice)
{
    $etiquetas = [
        [
            //etiqueta tamnaho 70mm x 20 mm
            'tamanho' => [73, 21],
            'margin_top' => 5,
            'weight' => 30,
            'tamanho_letra' => 20,
            'tamanho_barra' => 250
        ],
        [
            //etiqueta tamnaho 90mm x 50 mm
            'tamanho' => [101, 49],
            'margin_top' => 12,
            'weight' => 70,
            'tamanho_letra' => 25,
            'tamanho_barra' => 250
        ],
        [
            //etiqueta tamnaho 90mm x 50 mm
            'tamanho' => [100, 130],
            'margin_top' => 30,
            'weight' => 150,
            'height' => 300,
            'tamanho_letra' => 100,
            'tamanho_barra' => 450
        ]
    ];
    return $etiquetas[$indice];
}
