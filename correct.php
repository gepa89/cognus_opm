<?php
require ('conect.php');
require_once("hanaDB.php");

$db = new mysqli($SERVER,$USER,$PASS,$DB);
date_default_timezone_set('America/Asuncion');
// var_dump($_POST);
    $response = array();
//    SELECT distinct lg_c_emp from log_material 
    $ckMats = "SELECT distinct lg_c_emp from log_material ";
    echo $ckMats;
    $rsdoc = $db->query($ckMats);
    $cc = 1;
    while($ax = $rsdoc->fetch_assoc()){
        $pedidos[] = $ax['lg_c_emp'];
    }
//    echo "<pre>"; var_dump($pedidos);echo "</pre>";
    foreach($pedidos as $id => $pedido){
        $ckMats = "select lg_c_emp, lg_c_mat, ca_caja, ca_peso, ca_bulto, ca_ubi, ca_desc,lg_c_user, lg_c_um, sum(lg_c_cant) as total from log_material left join log_cajas on lg_c_emp = ca_emp and lg_c_caja = ca_caja where lg_c_emp = '{$pedido}' GROUP BY lg_c_emp, lg_c_mat, ca_caja";
    //    echo $ckMats;
        $rsdoc = $db->query($ckMats);
        $cc = 1;
        while($ax = $rsdoc->fetch_assoc()){
            if($ax['lg_c_um'] == 'ST'){
                $um = 'UN';
            }else{
                $um = $ax['lg_c_um'];
            }
    //        $data[$ax['lg_c_emp']][$cc]['MANDT']='300';
            $data[$ax['lg_c_emp']][$cc]['VBLEN']=$ax['lg_c_emp'];
            $data[$ax['lg_c_emp']][$cc]['CAJA']= 'CAJA-'.str_pad($ax['ca_caja'], 5, '0', STR_PAD_LEFT);
            $data[$ax['lg_c_emp']][$cc]['POSNR']=$cc;
            $data[$ax['lg_c_emp']][$cc]['MATNR']=$ax['lg_c_mat'];
    //        $data[$ax['lg_c_emp']][$cc]['MEINS']=$um;
            $data[$ax['lg_c_emp']][$cc]['LFIMG']=$ax['total'];
    //        $data[$ax['lg_c_emp']][$cc]['PESO']=(float)$ax['ca_peso'];
    //        $data[$ax['lg_c_emp']][$cc]['DESCRIPCION']=$db->real_escape_string($ax['ca_desc']);
    //        $data[$ax['lg_c_emp']][$cc]['UBICACION']=$ax['ca_ubi'];
    //        $data[$ax['lg_c_emp']][$cc]['CANTBULTOS']=(int)$ax['ca_bulto'];
    //        $data[$ax['lg_c_emp']][$cc]['ERNAM']=$ax['lg_c_user'];
            $cc++;
        }
        foreach($data[$pedido] as $id => $val){
    //        var_dump($val);
            $hanaqr = "update sapabap1.ZMM_BULTOS set "
                    . "LFIMG = {$val['LFIMG']}"
                    . " where VBLEN = '{$pedido}' and CAJA = '{$val['CAJA']}' and MATNR = '{$val['MATNR']}'";
                    echo $hanaqr."<br/>";
    //                            . "values ".$vals.'';
            $result = odbc_exec($prd, $hanaqr);
        }
    }
//    echo "<pre>"; var_dump($data);echo "</pre>";
//    VBLEN, CAJA, POSNR, MATNR, MEINS, LFIMG, PESO, DESCRIPCION, UBICACION, CANTBULTOS, ERNAM, LDDAT, ERZET
    
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err));
