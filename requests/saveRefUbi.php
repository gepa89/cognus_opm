<?php
require('../conect.php');
require_once(__DIR__ . '/../utils/respuesta.php');

$db = new mysqli($SERVER, $USER, $PASS, $DB);

$flag = $_POST['flag'];
if ($flag == 1) {
    $row[] = $_POST['data'];
} else {
    $row = $_POST['data'];
}
$db->begin_transaction();
$msgx = '';
foreach ($row as $k => $v) {
    if ($v[0] != '') {
        $material = strtoupper(trim($v[0]));
        $ubicacion = trim(strtoupper(str_replace('-', '', $v[1])));
        $ubitipo = $v[2];
        $cod_alma = $v[3];
        $sq = "SELECT
                *
            from
                ubimapa
            left join 
                        artubiref on
                artubiref.ubirefer = ubimapa.ubirefer
                and artubiref.cod_alma = ubimapa.cod_alma
            where
                ubimapa.ubirefer = '$ubicacion'
                and ubimapa.ubitipo = '$ubitipo'
                and ubimapa.cod_alma = '$cod_alma'
            limit 1";
        $query = $db->query($sq);
        if (!$query) {
            $db->rollback();
            $db->close();
            retorna_resultado(200, array('err' => true, 'msg' => 'error 0'));
            exit;
        }
        if ($query->num_rows == 0) {
            $db->rollback();
            $db->close();
            retorna_resultado(200, array("err" => true, "msg" => "No existe ubicacion"));
        }

        //validar articulo
        $sq = "SELECT
            *
        from
            arti
        where
            artrefer = '$material'";
        $query = $db->query($sq);
        if (!$query) {
            $db->rollback();
            $db->close();
            retorna_resultado(200, array('err' => true, 'msg' => 'error 1'));
        }
        if ($query->num_rows == 0) {
            $db->rollback();
            $db->close();
            retorna_resultado(200, array('err' => true, 'msg' => 'No existe articulo'));

        }

        $sq = "SELECT
            *
        from
            arti
        left join artubiref on
            artubiref.artrefer = arti.artrefer
        where
            arti.artrefer = '$material' and artubiref.ubirefer = '$ubicacion' and cod_alma='$cod_alma' limit 1";
        $query = $db->query($sq);
        if (!$query) {
            retorna_resultado(200, array('err' => true, 'msg' => 'error 2'));

        }
        if ($query->num_rows == 0) {
            $sq = "INSERT INTO artubiref 
            SET artrefer='$material', ubirefer='$ubicacion', cod_alma='$cod_alma', ubitipo='$ubitipo'";
            $query = $db->query($sq);
            if (!$query) {
                $db->rollback();
                $db->close();
                retorna_resultado(200, array('err' => true, 'msg' => 'error 3'));
            }

        } else {
            $db->rollback();
            $db->close();
            retorna_resultado(200, array('err' => true, 'msg' => "Ya existe material en ubicacion $ubicacion - $material"));
        }

    } else {
        $db->rollback();
        $db->close();
        retorna_resultado(200, array('err' => true, 'msg' => 'Error articulo'));

    }
}
$db->commit();
$db->close();
retorna_resultado(200, array('err' => false, 'msg' => 'Guardado'));