<?php

$permisos_disponibles = [
    "administrador" => [
        "externo",
        "recepcion",
        "expedicion",
        "empaque",
        "maquinista",
        "seguridad",
        "maestros",
        "configuraciones",
        "inventario",
        "reportes",
        "ajustes",
        "consultas",
        "recursos",
        "expedicion_cajas"
    ],
    "recepcion" => ["recepcion", "externo"],
    "impresiones" => ["", ""],
    "expedicion" => ["expedicion", "consultas","recursos","reportes"],
    "empaque" => ["empaque"],
];

?>