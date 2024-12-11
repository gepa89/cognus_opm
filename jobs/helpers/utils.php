<?php

function obtenerEanes($db, $articulo, $cod_alma)
{
    $sql = "SELECT * FROM artean WHERE artrefer='$articulo' and cod_alma='$cod_alma'";
    $res = $db->query($sql);
    $eanes = array();
    while ($fila = $res->fetch_assoc()) {
        $eanes[] = $fila['ean'];
    }
    return $eanes;
}
function eliminarEanes($db, $articulo, $cod_alma, $eanes)
{
    $eanes_cadena = implode("','", $eanes);
    $sql = "DELETE FROM artean WHERE artrefer='$articulo' and cod_alma='$cod_alma' and ean not in ('$eanes_cadena')";
    $res = $db->query($sql);
}
