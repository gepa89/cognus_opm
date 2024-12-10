<?php

use PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger;

require('../conect.php');
require_once('../logger/logger.php');
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$table = $_POST['table'];
$fields = $_POST['fields'];
//$descr = strtoupper($descr);
switch ($_POST['action']) {
    case 'upd':
        $Art = $_POST['Art'];
        $articulo = $_POST['Art'];
        $almacen = $_POST['Alm'];
        $hoy = date("Y-m-d");
        $sql = "SELECT * FROM $table WHERE artrefer='$Art' LIMIT 1";
        $res = $db->query($sql);
        $dato = $res->fetch_assoc();
        $agregar_ubicacion = $dato['artgrup'] != trim($_POST['Gar']);

        $sq = "update " . $table . " set " .
            "artdesc = '" . htmlentities($_POST['Des']) . "'," .
            "unimed = '" . $_POST['Umd'] . "'," .
            "artmarkit = '" . $_POST['Kit'] . "'," .
            "artser = '" . $_POST['Pse'] . "'," .
            "artgrup = '" . $_POST['Gar'] . "'," .
            "clirefer = '" . $_POST['Cli'] . "'," .
            "artlotemar = '" . $_POST['Plo'] . "'," .
            "costo = '" . $_POST['Cos'] . "'," .
            "almcod = '" . $_POST['Alm'] . "' where artrefer = '" . $_POST['Art'] . "' ";
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            guardar_error_log(__FILE__, $sq);
            guardar_error_log(__FILE__, $db->error);
            $err = 1;
            $msg = 'No se pudo guardar registro.';
            $db->rollback();
            goto fin;
            break;
        }
        if ($agregar_ubicacion) {
            $guardado = guardar_en_ubicacion($db, $articulo, $_POST['Gar'], $almacen);
            /*if (!$guardado) {
                print_r("error");
                $db->rollback();
                goto fin;
            }*/
            $err = 0;
            $msg = 'Datos guardados.';
        }
        $db->commit();
        break;
    case 'add':
        $sq = "insert into " . $table . " ({$fields}) values ";
        $sq .= "('" . $_POST['Art'] . "'," .
            "'" . $_POST['Des'] . "'," .
            "'" . $_POST['Ean'] . "'," .
            "'" . $_POST['Kit'] . "'," .
            "'" . $_POST['Umd'] . "'," .
            "'" . $_POST['Pse'] . "'," .
            "'" . $_POST['Gar'] . "'," .
            "'" . $_POST['Cli'] . "'," .
            "'" . $_POST['Plo'] . "'," .
            "'" . $_POST['Cos'] . "'," .
            "'" . $_POST['Alm'] . "')";
        $articulo = $_POST['Art'];
        $almacen = $_POST['Alm'];
        $presentacion = $_POST['Umd'];
        $ean = $_POST['Ean'];
        $hoy = date("Y-m-d");
        //        echo $sq;
        if ($db->query($sq)) {
            $sql = "INSERT INTO artipresen SET preseref='UNI', artrefer='$articulo',canpresen=1,cod_alma='$almacen'";
            $res = $db->query($sql);
            if (!$res) {
                $db->rollback();
                $err = 1;
                $msg = 'No se pudo guardar registro.';
                guardar_error_log(__FILE__, $db->error);
                goto fin;
            }

            $agregar_ean = trim($_POST['Ean']) != "";

            if ($agregar_ean) {
                $sql = "INSERT INTO artean SET artrefer='$articulo',ean='$ean',cod_alma='$almacen',fecaut='$hoy'";
                $res = $db->query($sql);
                if (!$res) {
                    $db->rollback();
                    $err = 1;
                    $msg = 'No se pudo guardar registro.';
                    guardar_error_log(__FILE__, $db->error);

                    goto fin;
                }
            }

            $guardado = guardar_en_ubicacion($db, $articulo, $_POST['Gar'], $almacen);
            if (!$guardado) {
                $db->rollback();
                goto fin;
            }
            $err = 0;
            $msg = 'Datos guardados.';
            $db->commit();
        } else {
            //            echo $db->error;
            $err = 1;
            $msg = 'No se pudo guardar registro.';
            guardar_error_log(__FILE__, $db->error);

            $db->rollback();
        }
        break;
}
fin:
$db->close();
echo json_encode(array('err' => $err, 'msg' => $msg));

function guardar_en_ubicacion($db, $articulo, $tipoArticulo, $almacen)
{
    $hoy = date("Y-m-d");
    $sql = "SELECT ubimapa.* 
    FROM ubimapa
    LEFT JOIN asigubi ON asigubi.ubirefer = ubimapa.ubirefer and asigubi.cod_alma=ubimapa.cod_alma 
    WHERE ubisitu='VA' AND ubitipo='PI' AND tipoubi IN ('$tipoArticulo','GE') AND asigubi.ubirefer IS NULL 
    ORDER BY CASE tipoubi
        WHEN '$tipoArticulo' THEN 1
        WHEN 'GE' THEN 2
        ELSE 1 
        END , ubirefer DESC LIMIT 1";
    $res = $db->query($sql);
    if (!$res) {
        return false;
    }
    if ($res->num_rows > 0) {
        $ubicacion = $res->fetch_object();
    } else {
        return false;
    }
    $sql = "INSERT INTO artubiref SET artrefer='$articulo',ubirefer='{$ubicacion->ubirefer}',cod_alma='$almacen',ubitipo='PI'";
    $res = $db->query($sql);
    if (!$res) {
        return false;
    }
    $sql = "INSERT INTO asigubi SET artrefer='$articulo',ubirefer='{$ubicacion->ubirefer}',cod_alma='$almacen',fecha='$hoy'";
    $res = $db->query($sql);
    if (!$res) {
        return false;
    }
    return true;
}
