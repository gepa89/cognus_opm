<?php
require ('../conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);

//params.put("pedido", ped);
//params.put("material", mat);
//params.put("ean", ean);
//params.put("terminal", term);
//params.put("usuario", usu);
//params.put("cantidad", cant);
//params.put("tipdoc", tip);
//
//
//B3
//{ubic=A00200110A, ean=9PAAHBBA6LA007283, tipdoc=ZCHA, material=59012110FA, pedido=0013192408, usuario=GUPA, terminal=TR01, cantidad=1}
//$db->autocommit(FALSE);
$pedido = $_POST['pedido'];
$material = $_POST['material'];
$ean = $_POST['ean'];
$terminal = $_POST['terminal'];
$usuario = $_POST['usuario'];
$cantidad = $_POST['cantidad'];
$tipdoc = $_POST['tipdoc'];
$ubic = $_POST['ubic'];

//obtengo la clase de pedido
$sq0 = "select pedclase from pedexcab where pedexentre = '{$pedido}' limit 1";
$rs0 = $db->query($sq0);
$rf0 = $rs1->fetch_assoc();
$clase = $rf0['pedclase'];

//reviso stockubi
$sq1 = "select canti from stockubi where ubirefer = '{$ubic}' and artrefer = '{$material}' limit 1";
$rs1 = $db->query($sq1);
$rf1 = $rs1->fetch_assoc();
$canStUbi = (int)$rf1['canti'];

//descuento el material preparado de stockubi
$canStUbi = $canStUbi - (int)$cantidad;
if($canStUbi == 0){
    $sq2 = "delete from stockubi where ubirefer = '{$ubic}' and artrefer = '{$material}'";
    
    if(!$db->query($sq2)){
        $errors[] = 1;
    }
}else{
    $sq2 = "update stockubi set canti = {$canStUbi} where artrefer = '{$material}'";
    if(!$db->query($sq2)){
        $errors[] = 1;
    }
}

//modifico stockart
$sq3 = "select candispo,canmure,canpedven,cantransfe, candevol from stockart where artrefer = '{$material}' limit 1";
$rs3 = $db->query($sq3);
$rf3 = $rs3->fetch_assoc();


$candispo = (int)$rf1['candispo'];
$canmure = (int)$rf1['canmure'];
$canpedven = (int)$rf1['canpedven'];
$cantransfe = (int)$rf1['cantransfe'];
$candevol = (int)$rf1['candevol'];
//resto de mi disponible
$candispo = $candispo - (int)$cantidad;
switch ($clase){
    case 'ZUB':
        $canpedven = $canpedven + (int)$cantidad;
        break;
    case 'EUB':
        $canpedven = $canpedven + (int)$cantidad;
        break;
    default:
        $cantransfe = $cantransfe + (int)$cantidad;
        break;
}
//actualizo stockart
$sq3 = "update stockart set  "
        . "candispo = {$candispo},"
        . "canmure = {$canmure},"
        . "canpedven = {$canpedven},"
        . "cantransfe = {$cantransfe},"
        . "candevol = {$candevol}"
        . " from stockart where  artrefer = '{$material}' limit 1";
if(!$db->query($sq3)){
    $errors[] = 1;
}

if($tipdoc == 'ZCHA'){    
    //inhabilito el nro de serie usado
    $sq4 = "update serati set serprep = 1 where artserie = '{$ean}'";
    if(!$db->query($sq4)){
        $errors[] = 1;
    }
}
if(!in_array(1, $errors)){
    if($db->commit()){        
    
        $response['mensaje'] = "Material Registrado.";
        $response["error"] = FALSE;
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo registrar material.";
    }
}else{
    $response["error"] = TRUE;
    $response['mensaje'] = "No se pudo registrar material.";
}

    
         echo json_encode($response);
 include '/var/www/html/closeconn.php';
