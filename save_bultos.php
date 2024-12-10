<?php
require ('conect.php');
include 'src/adLDAP.php';
require_once("hanaDB.php");
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$id = $_POST['id'];
$codigo = $_POST['codigo'];
$caja = $_POST['caja'];
$usuario = $_POST['usuario'];
$ica_emp = $_POST['ica_emp'];
$ica_caja = $_POST['ica_caja'];
$ica_peso = $_POST['ica_peso'];
$ica_ubi = $_POST['ica_ubi'];
$ica_bulto = $_POST['ica_bulto'];
$ica_desc = $_POST['ica_desc'];
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$cc = 0;
//$nmanif = "select distinct nromanif from sapabap1.zmm_manifiesto where entrega = '{$codigo}' order by nromanif desc limit 1";
//    $rst = odbc_exec($prd, $nmanif);
//    while ($rw = odbc_fetch_object($rst)){
//        $cc++;
//    }
//    if($cc == 0){
        $sqc = "update log_cajas set 
            ca_peso = '{$ica_peso}',
            ca_ubi = '{$ica_ubi}',
            ca_bulto = '{$ica_bulto}',
            ca_desc = '{$ica_desc}',
            ca_ts = now()
            where ca_id = '{$id}'";
//    echo $sqc;
        if($db->query($sqc)){
            $hanaqr = "update sapabap1.ZMM_BULTOS set "
                . "PESO = '".$ica_peso."', DESCRIPCION = '".$ica_desc."', UBICACION = '".$ica_ubi."', CANTBULTOS = '".$ica_bulto."', LDDAT = '".date('Ymd', strtotime('now'))."', ERZET = '".date('His', strtotime('now'))."' "
                . "where  VBLEN = '{$codigo}' and CAJA = '".'CAJA-'.str_pad($caja, 5, '0', STR_PAD_LEFT)."'";
//                            . "values ".$vals.'';
            $rst = odbc_exec($prd, $hanaqr);
            $msg = 'Datos Guardados';
            $err = 0;
        }else{
            $msg = 'Error al guardar';
            $err = 1;
        }
//    }else{
//        $msg = 'Documento con manifiesto generado. No se puede modificar';
//        $err = 1;
//    }
    

require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err));

