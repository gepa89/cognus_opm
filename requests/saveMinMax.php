<?php
require('../conect.php');
require_once("../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$row = $_POST['data'];


$msgx = '';
foreach ($row as $k => $v) {
    if ($v[0] != '') {
        if ($v[2] < $v[3]) {
            if (is_numeric($v[0])) {
                $ean = str_pad($v[0], 18, "0", STR_PAD_LEFT);
            } else {
                $ean = $v[0];
            }
            $ean = trim(mb_strtoupper($ean));
            $ubicacion = $v[1];
            $sq = "select * from arti a where a.artrefer = '$ean'";
            //                echo $sq;
            $rsx = $db->query($sq);
            if ($rsx->num_rows > 0) {

                $sq = "select a.artrefer,a.cecod,a.cod_alma from artirepo a inner join arti b on a.artrefer = b.artrefer where a.artrefer = '$ean' and a.cecod = '" . trim(mb_strtoupper($v[4])) . "' and a.ubirefer='$ubicacion' and a.cod_alma = '" . trim(mb_strtoupper($v[5])) . "'";
                //                        echo $sq;
                $rs = $db->query($sq);
                //artrefer
                //cecod
                //almcod
                //artminrep
                //artmaxrep
                if ($rs->num_rows > 0) {
                    try {
                        $se_puede_modificar = se_puede_modificar_min_max($db, $ean, $ubicacion);
                        if ($se_puede_modificar) {
                            $lerr[] = 1;
                            $msgx = "Reposicion en curso de material $ean";
                            continue;
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $sq_ins = " update artirepo set
                            ubirefer = '{$v[1]}',
                            artminrep = '{$v[2]}',
                            artmaxrep = '{$v[3]}'
                             where artrefer = '$ean' and ubirefer='$ubicacion' 
                             and cecod = '" . trim(mb_strtoupper($v[4])) . "' 
                             and cod_alma = '" . trim(mb_strtoupper($v[5])) . "'";
                    if ($db->query($sq_ins)) {
                        $lerr[] = 0;
                    } else {
                        $lerr[] = 1;
                    }
                } else {
                    $sq_ins = " insert into artirepo set
                            ubirefer = '{$v[1]}',
                            artminrep = {$v[2]},
                            artmaxrep = {$v[3]},
                            artrefer = '" . trim(mb_strtoupper($ean)) . "', cecod = '" . trim(mb_strtoupper($v[4])) . "', cod_alma = '" . trim(mb_strtoupper($v[5])) . "'";
                    if ($db->query($sq_ins)) {
                        $lerr[] = 0;
                    } else {
                        $lerr[] = 1;
                    }
                }
            } else {
                $msg = 'Error al guardar Configuración. 1';
                $fgil = $k + 1;
                $msgx .= "Fila" . $fgil . ': Material no existe en Maestro de Materiales ';
                $lerr[] = 1;
            }
        } else {
            $lerr[] = 1;
            $fgil = $k + 1;

            $msgx .= "Fila" . $fgil . ': Mínimo no puede superar al Máximo.';
        }
    }
}


if (in_array(1, $lerr)) {
    $msg = 'Error al guardar Configuración. 2' . $msgx;

    $err = 1;
} else {
    $msg = 'Configuración guardada';
    $err = 0;
}

echo json_encode(array('msg' => $msg, 'err' => $err));
exit();

function se_puede_modificar_min_max($db, $material, $ubicacion)
{
    $sql = "SELECT * FROM pedexdet INNER JOIN pedexcab ON pedexcab.pedexentre = pedexdet.pedexentre 
    WHERE pedclase = 'REPO' AND pedexdet.artrefer = '$material' AND ubirefer = '$ubicacion' AND pedexcab.siturefe='PD'";
    $res = $db->query($sql);
    return $res->num_rows > 0;
}
