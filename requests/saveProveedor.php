<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$usuario = $_SESSION['user'];
if (!$usuario) {
    exit;
}
$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
$fecha = date('Y-m-d');
$hora = date("H:i:s");
switch ($_POST['action']) {
    case 'upd':
        $Cpr = $_POST['Cpr'];
        $artrefer = $_POST['Art'];
        $cod_alma = $_POST['Alm'];
        $sq = "update " . $table . " set " .
            "nombre = '" . $_POST['Nom'] . "'," .    
            "tipdoc = '" . $_POST['Tdoc'] . "'," .
            "documento = '" . $_POST['Doc'] . "'," .    
            "codpais = '" . $_POST['Pai'] . "'," . 
            "codregion = '" . $_POST['Reg'] . "'," .    
            "incote = '" . $_POST['Inc'] . "'," .
            "tiprove = '" . $_POST['Tpr'] . "'," .    
            "condpago = '" . $_POST['Cpa'] . "'," .
            "codmone = '" . $_POST['Mon'] . "'," .   
            "ramo = '" . $_POST['Ram'] . "'," .    
            "mail = '" . $_POST['Dco'] . "'," .
            "telefono = '" . $_POST['Tel'] . "'," .    
            "banco = '" . $_POST['Ban'] . "'," .
            "cuenta = '" . $_POST['Cue'] . "'," .    
            "titular = '" . $_POST['Tit'] . "'," .
            "tipdocb = '" . $_POST['Tdo'] . "'," .    
            "documentob = '" . $_POST['Docb'] . "'," .    
            "usermod = '" . $usuario . "'," .     
            "hormod = '" . $hora . "'," .     
            "fecmod = '{$fecha}' where 
            codprove = '" . $_POST['Cpr'] . "' ";
//                echo $sq;
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            $err = 1;
        //    print_r($sq);
            $msg = 'No se pudo guardar registro.';
        }
        break;
    case 'add':
       

        
        $sq = "insert into " . $table . " ({$fields},usuario,fecre,horcre) values ";
        $sq .= "('" . $_POST['Nom'] . "'," .
            "'" . $_POST['Tdoc'] . "'," .
            "'" . $_POST['Doc'] . "'," .    
            "'" . $_POST['Pai'] . "'," .
            "'" . $_POST['Reg'] . "'," .     
            "'" . $_POST['Inc'] . "'," .
            "'" . $_POST['Tpr'] . "'," .    
            "'" . $_POST['Cpa'] . "'," .
            "'" . $_POST['Mon'] . "'," .    
            "'" . $_POST['Ram'] . "'," .
            "'" . $_POST['Dco'] . "'," .
            "'" . $_POST['Tel'] . "'," .    
            "'" . $_POST['Ban'] . "'," .
            "'" . $_POST['Cue'] . "'," .     
            "'" . $_POST['Tit'] . "'," .
            "'" . $_POST['Tdo'] . "'," .    
            "'" . $_POST['Docb'] . "'," .
               
            "'" .$usuario. "',".
            "'" .$fecha. "',".
            "'" . $hora . "' )";


  //              print $sq;
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            //            echo $db->error;
            $err = 1;
            $msg = 'No se pudo guardar registro.';
        }
        break;
}
$db->close();
echo json_encode(array('err' => $err, 'msg' => $msg));