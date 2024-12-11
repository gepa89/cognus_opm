<?php
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$sql = "SELECT almcod FROM alma";
$almacenes = $db->query($sql);
$error = false;
foreach ($almacenes as $almacen) {
    try {
        $error = reposicion($db, $almacen['almcod']);
        if ($error) {
            break;
        }
    } catch (\Throwable $th) {
        print_r($th->getMessage());
        $error = true;
    }
}
if ($error) {
    $db->rollback();
} else {
    print_r("sin errores");
    $db->rollback();
}
$db->close();

function reposicion($db, $almacen)
{
    // obtener articulos pendientes de reposicion agrupados por ubicacion
    $sq = "SELECT
                a.artrefer,
                a.ubirefer,
                a.artminrep,
                a.artmaxrep,
                a.cod_alma,
                arti.artdesc,
                ubimapa.ubitipo,
                arti.unimed,
                artipresen.canpresen,
                artipresen.preseref,
                COALESCE(sum(b.canti),0) as canti
            FROM
                artirepo a
            INNER JOIN arti ON
                arti.artrefer = a.artrefer
            INNER JOIN artipresen ON
                arti.artrefer = artipresen.artrefer and artipresen.preseref ='UNI'
            INNER JOIN ubimapa ON
                ubimapa.ubirefer = a.ubirefer
                AND a.cod_alma = ubimapa.cod_alma
            LEFT JOIN stockubi b ON
                a.artrefer = b.artrefer
                AND b.cod_alma = a.cod_alma
                AND a.ubirefer = b.ubirefer
            WHERE
                a.cod_alma = '$almacen'
                AND ubimapa.ubitipo = 'PI'
            GROUP BY
                a.artrefer,
                a.ubirefer";
    $query = $db->query($sq);
    if (!$query) {
        return true;
    }
    while ($ax = $query->fetch_assoc()) {
        $ubicacion = $ax['ubirefer'];
        $cod_articulo = $ax['artrefer'];
        //obtenemos stock en altura para el articulo y ubicacion referida
        $sqRE = "SELECT
                    a.artrefer,
                    SUM(b.canti) AS cant,
                    b.cod_alma
                FROM
                    artirepo a
                    INNER JOIN stockubi b ON a.artrefer = b.artrefer
                    AND a.cod_alma = b.cod_alma
                    INNER JOIN ubimapa c ON b.ubirefer = c.ubirefer
                    AND a.cod_alma = b.cod_alma
                    INNER JOIN arti d ON a.artrefer = d.artrefer
                WHERE
                    c.ubitipo = 'RE'
                    AND a.artrefer = '$cod_articulo'
                    AND a.cod_alma = '$almacen'
                GROUP BY
                    a.artrefer,
                    b.cod_alma
                LIMIT 1";
        print_r($sqRE);exit;
        $rsRE = $db->query($sqRE);
        $rsREz = $rsRE->fetch_assoc();
        $cant_stock = $rsREz['cant'];
        //    obtener pedido de reposicion actual si existe
        $sq = "SELECT
                    SUM(a.canpedi) AS tot
                FROM
                    pedexdet a
                    INNER JOIN pedexcab b ON a.pedexentre = b.pedexentre
                WHERE
                    b.pedclase = 'REPO'
                    AND b.siturefe NOT IN ('CE', 'AN')
                    AND a.artrefer = '$cod_articulo'
                GROUP BY
                    a.artrefer";
        $ckTot = $db->query($sq);
        $rx = $ckTot->fetch_assoc();
        $cant_pendiente = floatval($rx['tot']);
        $val_calculado = floatval($cant_stock) - $cant_pendiente;

        /* se obtiene el resto de del pedido que debe reponerse */

        if ($val_calculado > 0) {
            $tot_ped = (floatval($ax['artmaxrep']) - floatval($ax['canti'])) - $cant_pendiente;
            if ($tot_ped > $val_calculado) {
                /* Si ya no hay stock en altura que complete la cantidad solicitada 
                entonces se repone hasta este ultimo valor */
                $tot_ped = $val_calculado;
            }
            $cantidad_pedido = $ax['canti'] + $cant_pendiente;
            if ($cantidad_pedido <= $ax['artminrep']) {
                $presentacion = $ax['preseref'];
                $cantidad_presentacion = (int) $ax['canpresen'];
                if ($presentacion != "UNI") {
                    $resto = $tot_ped / $cantidad_presentacion;
                    $tot_ped = round($resto) * $cantidad_presentacion;
                }


                $ck_id = "select pedexentre from pedexcab where pedexentre >= 9000000000 order by pedexentre desc limit 1";
                $ck_rs = $db->query($ck_id);
                if ($ck_rs->num_rows == 0) {
                    $id_doc = 9000000000;
                } else {
                    $ckd_id = $ck_rs->fetch_assoc();
                    $id_doc = $ckd_id['pedexentre'] + 1;
                }
                $sqx = "INSERT INTO
                            pedexcab
                        SET
                            pedexref = '{$id_doc}',
                            pedexentre = '{$id_doc}',
                            pedclase = 'REPO',
                            pedexfec = now(),
                            pedexhor = now(),
                            movref = 'REP',
                            siturefe = 'PD',
                            almrefer = '$almacen'";
                $query = $db->query($sqx);
                if (!$query) {
                    return true;
                }
                $sql = "INSERT INTO
                        pedexdet (
                            pedpos,
                            artrefer,
                            artdesc,
                            unimed,
                            canpedi,
                            pedexref,
                            pedexentre,
                            ubirefer
                        )
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $params = array("ssssssss", 0, &$ax['artrefer'], &$ax['artdesc'], &$ax['unimed'], &$tot_ped, &$id_doc, &$id_doc, &$ubicacion);
                //$stmt->bind_param("ssssssss", 0, $ax['artrefer'], $ax['artdesc'], $ax['unimed'], (int) $tot_ped, $id_doc, $id_doc, $ubicacion);
                call_user_func_array(array($stmt, "bind_param"), $params);

                if (!$stmt->execute()) {
                }
            }
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
    return false;
}
