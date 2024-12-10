<?php $shell = true;
require_once(__DIR__ . '/../conect.php');
print_r("svs");
require_once(__DIR__ . "/../hanaDB.php");
print_r("aca");
include_once(__DIR__ . "/../../saprfc/prd.php");

$db_semaforo = new mysqli($SERVER, $USER, $PASS, $DB);

$sql = "SELECT * FROM scheduled_jobs WHERE estado=true AND script='wms_consulta_entregas_facturadas'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error");
    exit;
}
$resultado = $res->fetch_assoc();
if ($res->num_rows > 0) {
    print_r("existe job activo");
    $db_semaforo->close();
    exit;
}

$sql = "UPDATE scheduled_jobs SET estado=true WHERE script='wms_consulta_entregas_facturadas'";

$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error");
    exit;
}
$db_semaforo->close();

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$hoy = date("Y-m-d");
$db->begin_transaction();
$sq = "SELECT DISTINCT pedexcab.pedexentre 
        FROM pedexcab 
        INNER JOIN pedexdet On pedexdet.pedexentre=pedexcab.pedexentre 
        WHERE pedexcab.siturefe IN ('CE','PP') AND pedexdet.fechcie='{$hoy}' AND pedexcab.factura IS NULL";
$rs = $db->query($sq);
if ($rs->num_rows == 0) {
    $db->close();
    odbc_close($prd);
    exit;
}
$ldd = array();
while ($ax = $rs->fetch_assoc()) {
    $ldd[] = $ax['pedexentre'];
}

$centro = 'CHEL';
$aml = 'CD11';

$pedidos = implode("','", $ldd);

$sqhan = "SELECT DISTINCT VBELN, VBELV
            from sapabap1.vbfa
            where VBELV  IN ('{$pedidos}')
            and VBTYP_N='M'";
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)) {
    $sql = "UPDATE pedexcab SET factura='{$rw->VBELN}' WHERE pedexentre='{$rw->VBELV}'";
    print_r($sql."\n");
    $res = $db->query($sql);
    if (!$res) {
        $db->rollback();
        $db->close();
        print_r("error");
        exit;
    }
}
$db->commit();
$db->close();
odbc_close($prd);

$db_semaforo = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "UPDATE scheduled_jobs SET estado=false WHERE script='wms_consulta_entregas_facturadas'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error actualizar estado job");
    exit;
}
$db_semaforo->close();