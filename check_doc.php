<?php
require ('conect.php');
require_once("hanaDB.php");
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$response = array();
$usr = ($_POST['usuario']);
$pedido = str_pad($_POST['codigo'], 10, "0", STR_PAD_LEFT);
//    echo "aqtui";
$f = $c->getFunction('ZMM_F_CONTROL');
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
$result = $f->invoke([    
    'VUSUARIO' => $usr,
    'VENTREGA' => $pedido
]);
//var_dump($result);
if(is_numeric($_POST['material'])){
    $cdMaterial = str_pad($_POST['material'], 18, "0", STR_PAD_LEFT);
}else{
    $cdMaterial = $_POST['material'];
}
$sqhan = "select distinct SMATN from SAPABAP1.KOTD001 A 
            INNER JOIN SAPABAP1.KONDD B ON  A.KNUMH = B.KNUMH
            where MATWA = '{$cdMaterial}' or SMATN = '{$cdMaterial}' limit 1";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$rw = odbc_fetch_object($rst);
foreach($result["ZLIKP"] as $ind => $dat){
    $mats[trim($dat["MATNR"])]["VBELN"] = trim($dat["VBELN"]);
    $mats[trim($dat["MATNR"])]["MATNR"] = trim($dat["MATNR"]);
    $mats[trim($dat["MATNR"])]["POSNR"] = trim($dat["POSNR"]);
    $mats[trim($dat["MATNR"])]["MEINS"] = trim($dat["MEINS"]);
    $mats[trim($dat["MATNR"])]["ARKTTX"] = trim($dat["ARKTTX"]);
    @$mats[trim($dat["MATNR"])]["LFIMG"] += (int)$dat["LFIMG"];
}
//var_dump($mats);
//echo count($mats);
$flg = false;
$ckMats = "select lg_c_emp, lg_c_mat, lg_c_mat_desc, sum(lg_c_cant) as total from log_material left join log_cajas on lg_c_emp = ca_emp and lg_c_caja = ca_caja where lg_c_emp = '{$pedido}' GROUP BY lg_c_emp, lg_c_mat";
//echo $ckMats;
$rsdoc = $db->query($ckMats);
while($ax = $rsdoc->fetch_assoc()){
    $contras[$ax['lg_c_mat']] += $ax['total'];
    if($ax['total'] < $mats[$ax['lg_c_mat']]["LFIMG"]){
        $response[$ax['lg_c_mat']]['msg'] = 'Falta material '.$ax['lg_c_mat_desc'].' ('.$ax['lg_c_mat'].')';
        $response[$ax['lg_c_mat']]['err'] = 1;
        $flg = true;
    }
    if(isset($mats[$ax['lg_c_mat']]["LFIMG"]) && $ax['total'] > $mats[$ax['lg_c_mat']]["LFIMG"]){
        $response[$ax['lg_c_mat']]['msg'] = 'Sobra material '.$ax['lg_c_mat_desc'].' ('.$ax['lg_c_mat'].')';
        $response[$ax['lg_c_mat']]['err'] = 1;
        $flg = true;
    }else{
        $response[$ax['lg_c_mat']]['msg'] = 'No corresponde material '.$ax['lg_c_mat_desc'].' ('.$ax['lg_c_mat'].')';
        $response[$ax['lg_c_mat']]['err'] = 1;
        $flg = true;
    }
    if($ax['total'] == $mats[$ax['lg_c_mat']]["LFIMG"]){
        $response[$ax['lg_c_mat']]['msg'] = 'OK';
        $response[$ax['lg_c_mat']]['err'] = 0;
    }
}
if(count($mats) != count($contras)){
    foreach($mats as $mat => $dat){
        if(!key_exists($mat, $contras)){
//            echo $mat;
            $sqhands = "select distinct MAKTX from SAPABAP1.MAKT A WHERE MATNR = '{$mat}' and spras = 'S' limit 1";
            //                echo $sqhan;
            $rstds = odbc_exec($prd, $sqhands);
            $rwds = odbc_fetch_object($rstds);
            $response[$mat]['msg'] = 'Falta material '.$rwds->MAKTX.' ('.$mat.')';
            $response[$mat]['err'] = 1;
            $flg = true;
        }
    }
}
$data = $response;
if($flg == false){
    $ckMats = "select ca_caja, ca_st, sum(lg_c_cant) as total from log_cajas left join log_material on lg_c_emp = ca_emp and lg_c_caja = ca_caja where ca_emp = '{$pedido}' GROUP BY ca_caja";
    $rsdoc = $db->query($ckMats);
    while($ax = $rsdoc->fetch_assoc()){
        if($ax['ca_st'] == 0 && (!is_null($ax['total']) || $ax['total'] == 0)){
            $cajas[$ax['ca_caja']]['msg'] = 'Debe cerrar la caja '.$ax['ca_caja'];
            $cajas[$ax['ca_caja']]['err'] = 1;
        }else{
            $cajas[$ax['ca_caja']]['msg'] = 'OK';
            $cajas[$ax['ca_caja']]['err'] = 0;
        }
    }
    $data = $cajas;
}
require ('closeconn.php');
echo json_encode(array('dat' => $data));
