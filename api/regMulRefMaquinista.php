<?php
require('../conect.php');
header('Content-type: application/json; charset=utf-8');

//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$pedido = strtoupper($_POST['pedido']);
$terminal = strtoupper($_POST['terminal']);
$multiref = strtoupper($_POST['multiref']);
$sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
$response = array();
$response['data'][0]['asignados'] = 0;
$response['data'][0]['pendientes'] = 0;
$rsz = $db->query($sqz);
$rszz = $rsz->fetch_assoc();
$zona = $rszz['terzonpre'];

if ($pedido != '') {
    if ($multiref != '') {


        //    verifico si la multiref ya esta asignada
        $sq = "select pedido from ped_multiref where multiref = '{$multiref}' and codst = 0 ";
        $rs = $db->query($sq);
        if ($rs->num_rows == 0) {
            $sq3 = "select pedido from ped_multiref where pedido = '{$pedido}' and codst = 0 ";
            $rs3 = $db->query($sq3);
            if ($rs3->num_rows == 0) {
                $sq2 = "insert into ped_multiref set multiref = '{$multiref}',zona='$zona', terminal = '{$terminal}', pedido = '{$pedido}', mrts = now()";
                if ($db->query($sq2)) {
                    $sq3 = "update assig_ped set ex_st = 1 where tercod = '{$terminal}' and pedido = '{$pedido}'";
                    $db->query($sq3);
                    $response["error"] = FALSE;
                    $response['mensaje'] = "Multireferencia ya asignada";



                    $sq = "select DISTINCT
                               a.pedido, 
                               a.st,
                               a.ex_st, 
                               d.zoncodpre,
                                                                   f.multiref,
                               (select count(artrefer) as tsku from pedubidet b where pedubicod = a.pedido) as tsku,
                               (select pedclase as pclase from pedubicab c where pedubicod = a.pedido) as clase
                               from assig_ped a 
                               inner join pedubidet b on pedubicod = pedido
                               inner join stockubi c on b.artrefer = c.artrefer
                               inner join ubimapa d on c.ubirefer = d.ubirefer
                               inner join termi e on a.tercod = e.tercod
                               left join ped_multiref f on a.pedido = f.pedido
                               where 
                               a.tercod = '{$terminal}' and 
                               st = 0 ";
                    //     echo $sq;
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
                    //                        tvTotalPed.setText("Asignados: "+pedData2.getString("asignados"));
//                        tvTotalPedPen.setText("Pendientes: "+pedData2.getString("pendientes"));
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