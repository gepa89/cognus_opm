<?php
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/hana.php");
require_once('helpers/utils.php');

define('ALMACEN', 'CD11');

$db_semaforo = MysqlDB::obtenerInstancia();
$conn_odbc = HanaDB::obtenerInstancia();
$sql = "SELECT * FROM scheduled_jobs WHERE estado=true AND script='wms_update_articulos'";
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

$sql = "UPDATE scheduled_jobs SET estado=true WHERE script='wms_update_articulos'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error");
    exit;
}
$db_semaforo->close();
$db = MysqlDB::obtenerInstancia();
$db->begin_transaction();

$centro = 'CHEL';
$aml = ALMACEN;
$sqhana = "SELECT mara.laeda, mara.matnr, mara.prdha, mara.ean11, mara.matkl, mara.mstae, mara.meins, 
            marc.SERNP, 
            MAKT.MAKTX,
			ZMMCLASE_VAL.BWTAR 
            FROM SAPABAP1.MARA, SAPABAP1.MARC, SAPABAP1.MAKT, SAPABAP1.ZMMCLASE_VAL
            WHERE MARC.WERKS = '{$centro}'
            AND MARA.LAEDA =  TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            AND MARA.MATNR = MARC.MATNR
            AND MARA.MATNR = MAKT.MATNR
            AND MARA.MATNR = ZMMCLASE_VAL.MATNR
            AND ZMMCLASE_VAL.WERKS = '{$centro}'
            AND MAKT.SPRAS = 'S' ORDER BY mara.matnr DESC";

$dt = odbc_exec($conn_odbc, $sqhana);
//var_dump($dt);
//echo 'entro';
$t_pend = $cc = 0;
//var_dump($dt);
while ($row = odbc_fetch_array($dt)) {
    $material = trim($row["MATNR"]);
    $sql = "SELECT * FROM SAPABAP1.CDHDR WHERE objectid='$material' AND tcode in ('MM02','MM02(MASS)') ORDER BY udate DESC, UTIME DESC LIMIT 1";
    $ut_res = odbc_exec($conn_odbc, $sql);
    $ultima_actualizacion = odbc_fetch_array($ut_res);
    $articulo = obtener_articulo($db, $row["MATNR"]);

    if ($articulo) {
        $date = $ultima_actualizacion['UDATE'];
        $time = $ultima_actualizacion['UTIME'];

        // Format the date and time into a single datetime string
        $datetimeString = date('Y-m-d H:i:s', strtotime("$date $time"));
        $fecha = new DateTime($articulo->fecaut);
        $hoy = new DateTime($datetimeString);
        if ($fecha < $hoy) {
            actualizarArticulos($row, $aml, $conn_odbc, $datetimeString);
        }
    } else {
        insertarArticulo($conn_odbc, $aml, $row);
    }
}
$scriptname = basename(__FILE__, '.php');
$sq = "select * from scheduled_jobs where script = '{$scriptname}'";
$rs = $db->query($sq);
if ($rs->num_rows > 0) {
    $sq = "update scheduled_jobs set last = now() where script = '{$scriptname}'";
    $rs = $db->query($sq);
} else {
    $sq = "insert into scheduled_jobs set script = '{$scriptname}', last = now()";
    $rs = $db->query($sq);
}
#$db->rollback();
$db->commit();
$db->close();

$db_semaforo = MysqlDB::obtenerInstancia();
$sql = "UPDATE scheduled_jobs SET estado=false WHERE script='wms_update_articulos'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error actualizar estado job");
    exit;
}
$db_semaforo->close();
HanaDB::cerrarConexion($conn_odbc);
exit;

function obtener_articulo($db, $artrefer)
{
    $sq = "SELECT * 
       from arti WHERE artrefer = '$artrefer'";
    $res = $db->query($sq);
    return $res->fetch_object();
}

function actualizarArticulos($row, $aml, $conn_odbc, $fecha_sap)
{
    $db = MysqlDB::obtenerInstancia();
    $db->begin_transaction();
    $row = (object) $row;
    $articulo = $row->MATNR;
    $almacen = ALMACEN;
    $sq = "update arti set            
            artdesc = ?,
            unimed = ?,
            artgrup = ?,
            artrot = ?,
            artean = ?,
            artjerar = ?,
            artval = ?,
            almcod = '{$almacen}',
            fecaut = '{$fecha_sap}'
            where 
            artrefer = ?
            ";
    $row->MAKTX = preg_replace("/'/", "", $row->MAKTX);
    $row->MAKTX = preg_replace('/"/', "", $row->MAKTX);
    $row->MAKTX = preg_replace("/`/", "", $row->MAKTX);
    $stmt = $db->prepare($sq);
    if ($row->MEINS == "ST") {
        $unidad_medida = "UN";
    } else if ($row->MEINS == "PAA") {
        $unidad_medida = "PAR";
    }
    $stmt->bind_param("ssssssss", $row->MAKTX, $unidad_medida, $row->MATKL, $row->MSTAE, $row->EAN11, $row->PRDHA, $row->BWTAR, $articulo);
    $res = $stmt->execute();
    if (!$res) {
        guardar_error_log("error_actualizar_articulos", $sq . json_encode($row), $stmt->error);
        $db->rollback();
        $db->close();
        print_r("error 2");
        return;
    } else {
        print("articulo actualizado \n");
    }

    _manejar_eanes($db, $conn_odbc, $articulo, $aml);
    $db->commit();
    $db->close();
}

function insertarArticulo($conn_odbc, $aml, $rw)
{
    $db = MysqlDB::obtenerInstancia();
    $db->begin_transaction();

    $rw = (object) $rw;
    $cod = $rw->MATNR;
    $hoy = date("Y-m-d");
    $almacen = ALMACEN;
    $sq = "insert into arti set 
                artrefer = ?,
                artdesc = ?,
                unimed = ?,
                artser = ?,
                artgrup = ?,
                artjerar = ?,
                artval = ?,
                almcod = '{$almacen}',
                fecaut = '$hoy'
                ";
    $rw->MAKTX = preg_replace("/'/", "", $rw->MAKTX);
    $rw->MAKTX = preg_replace('/"/', "", $rw->MAKTX);
    $rw->MAKTX = preg_replace("/`/", "", $rw->MAKTX);

    $stmt = $db->prepare($sq);

    if ($rw->MEINS == "ST") {
        $unidad_medida = "UN";
    } else if ($rw->MEINS == "PAA") {
        $unidad_medida = "PAR";
    }

    $stmt->bind_param("sssssss", $cod, $rw->MAKTX, $unidad_medida, $rw->SERNP, $rw->MATKL, $rw->PRDHA, $rw->BWTAR);
    $res = $stmt->execute();
    if (!$res) {
        guardar_error_log(__FILE__, $sq . json_encode($rw), $db->error);
        $db->rollback();
        $db->close();
        print_r("error 2");
        return;
    }
    $sq = "insert into artipresen set
    artrefer = '{$cod}',
    cod_alma = '{$aml}',    
    canpresen = 1,
    preseref = 'UNI';
    ";
    $res = $db->query($sq);
    if (!$res) {
        guardar_error_log(__FILE__, $sq . json_encode($rw), $db->error);
        $db->rollback();
        $db->close();
        print_r("error 4");
        return;
    }
    _manejar_eanes($db, $conn_odbc, $cod, $aml);
    $db->commit();
    $db->close();
}

function _manejar_eanes($db, $conn_odbc, $codigo_articulo, $almacen)
{
    $sqhanX = "SELECT MATNR, EAN11, EANTP 
                FROM SAPABAP1.MEAN
                WHERE MATNR =  '{$codigo_articulo}'";
    echo $sqhanX;
    $rstX = odbc_exec($conn_odbc, $sqhanX);

    $eanes_guardados = obtenerEanes($db, $codigo_articulo, $almacen);
    $eanes_actuales = array();

    while ($rw = odbc_fetch_object($rstX)) {
        $ean = $rw->EAN11;
        if (is_numeric($rw->EANTP)) {
            $ean = str_pad($ean, $rw->EANTP, '0', STR_PAD_LEFT);
        }
        if (is_numeric($rw->EANTP)) {
            $ean = str_pad($ean, $rw->EANTP, '0', STR_PAD_LEFT);
        }

        $eanes_actuales[] = $ean;

        if (!in_array($rw->EAN11, $eanes_guardados)) {
            $sq = "INSERT into artean set 
                    artrefer = '{$rw->MATNR}',
                    cod_alma = '{$almacen}',    
                    ean = '" . ($ean) . "',
                    fecaut = now()
                    ";
            $res = $db->query($sq);
            if (!$res) {
                print_r("fallo");
            }
        }
    }
    eliminarEanes($db, $codigo_articulo, $almacen, $eanes_actuales);
}
