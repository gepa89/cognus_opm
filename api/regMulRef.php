<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
guardar_info_log(__FILE__, json_encode($_POST));
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$pedido = strtoupper($_POST['pedido']);
$terminal = strtoupper($_POST['terminal']);
$multiref = strtoupper($_POST['multiref']);
$datos_pedido = $db->query("SELECT * from pedexcab WHERE pedexentre='$pedido' LIMIT 1")->fetch_assoc();
$codalma = $datos_pedido['almrefer'];
$tiene_presentacion = $_POST['tiene_presentacion'] === "true" ? "1" : "0";
$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
$response = array();
$response['data'][0]['asignados'] = 0;
$response['data'][0]['pendientes'] = 0;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$zona = $rszz['terzonpre'];

if ($pedido != '') {
    if ($multiref != '') {

        $qry = "SELECT COUNT(*) as cantidad   
        from etiquetas  
        where ettip = 'EX' AND etnum='$multiref' limit 1";
        $query = $db->query($qry);
        if (!$query) {
            guardar_error_log("reg-multi", $qry);
            $response["error"] = TRUE;
            $response['mensaje'] = "Error al realizar consulta";
            $db->close();
            echo json_encode($response);
            exit;
        }
        $dato = $query->fetch_object();
        if ($dato->cantidad == 0) {
            $response["error"] = TRUE;
            $response['mensaje'] = "Multireferencia no encontrada";
            $db->close();
            echo json_encode($response);
            exit;
        }
        //    verifico si la multiref ya esta asignada
        $sq = "select pedido from ped_multiref where multiref = '{$multiref}' and cod_alma='$codalma' and codst = 0 ";

        #print_r($sq);
        $rs = $db->query($sq);
        if ($rs->num_rows == 0) {
            $sq3 = "select pedido from ped_multiref where pedido = '{$pedido}' and cod_alma='$codalma' and codst = 0 ";
            $rs3 = $db->query($sq3);
            if ($rs3->num_rows == 0) {
                $sq2 = "insert into ped_multiref set multiref = '{$multiref}', 
                terminal = '{$terminal}',
                zona = '{$zona}',
                cod_alma = '$codalma', 
                pedido = '{$pedido}', 
                pedcajas = '$tiene_presentacion',
                mrts = now()";
                if ($db->query($sq2)) {
                    $sq3 = "update assig_ped set ex_st = 1 where tercod = '{$terminal}' and pedido = '{$pedido}'";
                    $db->query($sq3);
                    $response["error"] = FALSE;
                    $response['mensaje'] = "";
                    $sq = "select DISTINCT
                               a.pedido, 
                               a.st,
                               a.ex_st, 
                               d.zoncodpre,
                               f.multiref,
                               (select count(artrefer) as tsku from pedexdet b where b.tienekit <> 'SI' and pedexentre = a.pedido) as tsku,
                               (select pedclase as pclase from pedexcab c where pedexentre = a.pedido) as clase
                               from assig_ped a 
                               inner join pedexdet b on pedexentre = pedido
                               inner join stockubi c on b.artrefer = c.artrefer
                               inner join ubimapa d on c.ubirefer = d.ubirefer and d.zoncodpre = a.zona
                               inner join termi e on a.tercod = e.tercod
                               left join ped_multiref f on a.pedido = f.pedido
                               where 
                               a.tercod = '$terminal' and 
                               e.terzonpre = '$zona' and 
                               st = 0 ";
                    $cc = 0;
                    $r1 = $db->query($sq);
                    while ($row = $r1->fetch_assoc()) {

                        if ($row['multiref'] == '') {
                            $response['data'][0]['pendientes']++;
                        } else {
                            $response['data'][0]['asignados']++;
                        }
                    }
                    $response['data'][0]['pendientes'] = '' . $response['data'][0]['pendientes'];
                    $response['data'][0]['asignados'] = '' . $response['data'][0]['asignados'];
                } else {
                    $response["error"] = TRUE;
                    $db->rollback();
                    guardar_error_log(__FILE__, $sq2);
                    guardar_error_log(__FILE__, json_encode($db->error_list));
                }
            } else {
                $response["error"] = TRUE;
                $response['mensaje'] = "Pedido con multi referencia asignada";
            }
        } else {
            $response["error"] = TRUE;
            $response['mensaje'] = "Multireferencia ya asignada";
        }
    }
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "No hay pedido asignado a esta terminal.";
}

echo json_encode($response);
include '/var/www/html/closeconn.php';