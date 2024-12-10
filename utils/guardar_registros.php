<?php
require_once("../logger/logger.php");
function guardar_movimiento_stock(
    $db,
    $pedido,
    $artrefer,
    $canti,
    $ubirefer,
    $clamov,
    $usuario,
    $fecmov,
    $hormov,
    $cod_alma,
    $etnum
) {
    $sql = "INSERT into movimientos 
        set ubirefer = '{$ubirefer}', 
        pedido ='{$pedido}', 
        usuario ='{$usuario}', 
        clamov='$clamov', 
        artrefer ='$artrefer', 
        canti = '$canti', 
        etnum = '$etnum', 
        cod_alma='$cod_alma', 
        fecmov='{$fecmov}',
        hormov='{$hormov}'";
    $res = $db->query($sql);
    if (!$res) {
        guardar_error_log(__FILE__,$db->error);
        guardar_error_log(__FILE__,$sql);
    }
}
