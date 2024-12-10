<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//crear hash del estante
$terminal = strtoupper($_POST['terminal']);

if ($terminal != '') {
    //    obtengo los pedidos asignados a la terminal
    $sqq = "select a.pedido, b.terzonpre from assig_ped a inner join termi b on a.tercod = b.tercod where a.tercod = '{$terminal}' and a.st = 0 and a.ex_st = 1";
    //    echo $sqq;
    $rs1 = $db->query($sqq);
    $cc = 0;
    while ($ax1 = $rs1->fetch_assoc()) {
        $pedidos[$cc]['pedido'] = $ax1['pedido'];
        $pedidos[$cc]['terzonpre'] = $ax1['terzonpre'];
        $cc++;
    }
    $data = array();
    foreach ($pedidos as $ind => $ped) {
        //    por cada pedido verifico si ya se preparo en la zona que corresponde a la terminal
        $id_pedido = $ped['pedido'];
        $zona = $ped['terzonpre'];
        $sq2 = "SELECT
                    *
                from
                    (
                        SELECT
                        a.artrefer,
                        sum(c.canti) as canti
                    from
                        pedexdet a
                    inner join arti b on
                        a.artrefer = b.artrefer
                    inner join stockubi c on
                        a.artrefer = c.artrefer
                    inner join ubimapa d on
                        c.ubirefer = d.ubirefer
                    where
                        a.pedexentre = '$id_pedido'
                        and d.zoncodpre = '$zona'
                        and d.ubitipo = 'PI'
                        and expst = 0
                        and a.tienekit <> 'SI') as resultado
                WHERE
                    canti <> 0";
        //                    echo $sq2;
        $rs2 = $db->query($sq2);
        while ($ax2 = $rs2->fetch_assoc()) {
            $data[] = $ax2;
        }
    }
    //    echo "<pre>";var_dump($data);echo "</pre>";
    if (count($data) == 0) {
        $response["error"] = FALSE;
        $response['mensaje'] = "Se puede cerrar Pedido.";
    } else {
        $response["error"] = TRUE;
        $response['mensaje'] = "Pedido no se puede finalizar.";
    }
}
echo json_encode($response);
include '/var/www/html/closeconn.php';
