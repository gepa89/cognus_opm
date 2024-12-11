<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$user = $_POST['user'];
$pedido = strtoupper($_POST['pedido']);
$ean = strtoupper(trim($_POST['material']));


if (isset($pedido)) {

    $flg = 0;

    //    Obtengo todos los pedidos de recepcion asignados a la terminal
    $sq = "SELECT a.artrefer, b.artlotemar, a.pedpos, b.artean, b.artdesc,sum(a.canpedi) 
            as canpedi, sum(a.canpendi) 
            as canpendi, sum(a.canprepa) as canprepa, b.artser, d.artserie as artnroserie,
            d.serrecep,
            (SELECT COUNT(*) from capaubi where artrefer=a.artrefer) as ubicacion_piso
            FROM pedredet a
            left join arti b on a.artrefer = b.artrefer
            left join artean c on a.artrefer = c.artrefer
            left join serati d on a.artrefer = d.artrefer and a.pedrefer = d.artped
            where pedrefer = '{$pedido}' 
            and a.tienekit <> 'SI'
            and (c.ean = '{$ean}' or d.artserie = '{$ean}') 
            and a.canprepa < a.canpedi 
            and a.expst <> 1 
            GROUP BY a.artrefer, a.pedpos, d.artserie";
    //            echo $sq;

    $rs = $db->query($sq);
    $flg = 0;

    while ($ax = $rs->fetch_assoc()) {
        if ($ax['canprepa'] == $ax['canpedi']) {
            $response["error"] = TRUE;
            $response['mensaje'] = "Material ya controlado.";
            $flg = 1;
            continue;
        } else {
            if ($ax['serrecep'] != 0) {
                $response["error"] = TRUE;
                $response['mensaje'] = "Serie ya controlada.";

                $flg = 1;
                continue;
            } else {
                $ean = "select ean from artean where artrefer = '{$ax['artrefer']}'";
                $rsean = $db->query($ean);
                $ea = '';
                while ($axx = $rsean->fetch_assoc()) {
                    if ($ea != '') {
                        $ea .= "-" . $axx['ean'] . "";
                    } else {
                        $ea = "" . $axx['ean'] . "";
                    }
                }
                $ax['artean'] = $ea;
                $sql = "SELECT canpresen as cantidad_presentacion, preseref as presentacion FROM artipresen where artrefer = ?";
                $sqlbuilder = new MySqlQuery($db);
                $presentaciones = $sqlbuilder->rawQuery($sql, [$ax['artrefer']])->getAll();
                $ax['presentaciones'] = $presentaciones;
                $ax['tiene_presentacion_cajas'] = in_array("CJ", array_column($presentaciones, "presentacion"));
                if ($ax['tiene_presentacion_cajas']) {
                    $sql = "SELECT canpresen as cantidad_presentacion FROM artipresen where artrefer = ? and preseref = 'CJ'";
                    $sqlbuilder = new MySqlQuery($db);
                    $cantidad_presentacion = $sqlbuilder->rawQuery($sql, [$ax['artrefer']])->getOne();
                    $ax['cantidad_presentacion_cajas'] = $cantidad_presentacion->cantidad_presentacion;
                }
                $response['materiales'][] = $ax;
            }
        }
    }
    if (count($response['materiales']) > 0) {
        foreach ($response['materiales'] as &$material) {
            $material['ubicacion_piso'] = (int) $material['ubicacion_piso'] > 0;
        }
        $response["error"] = FALSE;
        $response['mensaje'] = "";
    } else if ($flg == 0) {
        $response["error"] = TRUE;
        $response['mensaje'] = "Material no corresponde.";
    }
    echo json_encode($response);
}
