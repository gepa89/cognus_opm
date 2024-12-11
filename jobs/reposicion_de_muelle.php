<?php
$shell = true;
require('/var/www/html/wmsd/conect.php');
$sql = "select
        muelle,
        pedrefer
        from
        pedmuelle,
        pallet_mat
    where
        pedmuelle.pedrefer = pallet_mat.ap_pedido
        and pedmuelle.pedstatus = '0'";
$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>