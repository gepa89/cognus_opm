<?php

require ('../conect.php');
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$estante = $_POST['estante'];
$codalma = $_POST['codalma'];
$sql = "select * from ubica where ubcod = '{$estante}' and cod_alma = '{$codalma}' limit 1";

$rs = $db->query($sql);

if($rs->num_rows > 0){    
    while($rx = $rs->fetch_assoc()){
        $responde['hash'] = $rx['ubest'];
        $responde['cod'] = $rx['ubcod'];
        $responde['codalma'] = $rx['cod_alma'];
        $responde['hdes'] = $rx['ubdes'];    
        $responde['hhas'] = $rx['ubhas'];
        $responde['nivel'] = $rx['ubniv'];
//        almacenajes
        $sqalm = "select * from estalm where almest = '{$rx['ubest']}' and cod_alma = '{$rx['cod_alma']}'";
        $ralm = $db->query($sqalm);
        $calm = 0;
        while($ax = $ralm->fetch_assoc()){
            $responde['almacenaje'][$calm][0] = $ax['almdes'];
            $responde['almacenaje'][$calm][1] = $ax['almndes'];
            $responde['almacenaje'][$calm][2] = $ax['almhas'];
            $responde['almacenaje'][$calm][3] = $ax['almnhas'];
            $responde['almacenaje'][$calm][4] = $ax['almzon'];
            $calm++;
        }
//        picking
        $sqpick = "select * from estpic where picest = '{$rx['ubest']}' and cod_alma = '{$rx['cod_alma']}'";
        $rpick = $db->query($sqpick);
        $cpic = 0;
        while($ax = $rpick->fetch_assoc()){
            $responde['picking'][$cpic][0] = $ax['picdes'];
            $responde['picking'][$cpic][1] = $ax['picndes'];
            $responde['picking'][$cpic][2] = $ax['pichas'];
            $responde['picking'][$cpic][3] = $ax['picnhas'];
            $responde['picking'][$cpic][4] = $ax['piczon'];
            $cpic++;
        }
//        Sub niveles estsniv
        $sqsubn = "select * from estsniv where snivest = '{$rx['ubest']}' and cod_alma = '{$rx['cod_alma']}'";
        $rsubn = $db->query($sqsubn);
        $csniv = 0;
        while($ax = $rsubn->fetch_assoc()){
            $responde['subniv'][$csniv][0] = $ax['snivdes'];
            $responde['subniv'][$csniv][1] = $ax['snivndes'];
            $responde['subniv'][$csniv][2] = $ax['snivhas'];
            $responde['subniv'][$csniv][3] = $ax['snivnhas'];
            $responde['subniv'][$csniv][4] = $ax['snivsub'];
            $csniv++;
        }
//        Clases
        $sqclac = "select * from estcla where claest = '{$rx['ubest']}' and cod_alma= '{$rx['cod_alma']}'";
        $rclac = $db->query($sqclac);
        $ccla = 0;
        while($ax = $rclac->fetch_assoc()){
            $responde['clases'][$ccla][0] = $ax['clades'];
            $responde['clases'][$ccla][1] = $ax['clandes'];
            $responde['clases'][$ccla][2] = $ax['clahas'];
            $responde['clases'][$ccla][3] = $ax['clanhas'];
            $responde['clases'][$ccla][4] = $ax['clacla'];
            $responde['clases'][$ccla][5] = $ax['tipoubi'];
            $responde['clases'][$ccla][6] = $ax['dimension'];
            $ccla++;
        }
    }    
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Estante no existe';
}

echo json_encode(array( 'dat' => $responde, 'err' => $err,'msg' => $msg));

exit();
