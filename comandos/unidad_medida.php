<?php
require_once("../conect.php");
require_once("../hanaDB.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$centro = 'LCC1';
$aml = 'LDAL';

$sql = "SELECT artrefer FROM arti WHERE unimed=''";
$res = $db->query($sql);
$articulos = [];
while ($fila = $res->fetch_object()) {
    $articulos[] = $fila->artrefer;
}

$condicion = implode("','", $articulos);

$sql = "SELECT MATNR,MEINS FROM SAPABAP1.MARA WHERE MATNR IN ('$condicion')";

$res = odbc_exec($prd, $sql);
while ($fila = odbc_fetch_object($res)) {
    $unidad_medida = trim($fila->MEINS);
    $articulo = trim($fila->MATNR);
    if ($fila->MEINS == "ST") {
        $unidad_medida = "UN";
    } else if ($fila->MEINS == "PAA") {
        $unidad_medida = "PAR";
    }

    $sql = "UPDATE arti SET unimed = '$unidad_medida' WHERE artrefer='$articulo'";
    $db->query($sql);   
}
$db->commit();
$db->close();

odbc_close($prd);
