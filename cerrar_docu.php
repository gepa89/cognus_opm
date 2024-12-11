<?php
require ('conect.php');
require_once("hanaDB.php");
$db = new mysqli($SERVER,$USER,$PASS,$DB);
date_default_timezone_set('America/Asuncion');
// var_dump($_POST);
if($_POST['codigo'] != ''){
    
    $response = array();
    $usr = ($_POST['usuario']);
    $pedido = str_pad($_POST['codigo'], 10, "0", STR_PAD_LEFT);
    
    if($pedido>= 4800000000){
        $sqhan = "select WERKS, LGORT, RESLO from sapabap1.ekpo where ebeln = '{$pedido}'";
        //                echo $sqhan;
        $rst = odbc_exec($prd, $sqhan);
        $rw = odbc_fetch_object($rst);
        $dt_snd2['MANDT'] = '300';
        $dt_snd2['ENTREGA'] = $pedido;
        $dt_snd2['TIPO'] = '003';
        $dt_snd2['ALMACEN'] = $rw->WERKS;
        $dt_snd2['HORA'] = date("His", strtotime('now'));
        $dt_snd2['FECHA'] = date("Ymd", strtotime('now'));
        $dt_snd2['USUARIO'] = $usr;
        $dt_snd2['CENTRO'] = $rw->LGORT;
        $dt_snd2['ALMAEMISOR'] = $rw->RESLO;
    }
    
    
    
        
        
        
    odbc_autocommit($prd,false); 
    
    
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
        $data[$ax['lg_c_emp']][$cc]['MANDT']='300';
        $data[$ax['lg_c_emp']][$cc]['VBLEN']=$ax['lg_c_emp'];
        $data[$ax['lg_c_emp']][$cc]['CAJA']= 'CAJA-'.str_pad($ax['ca_caja'], 5, '0', STR_PAD_LEFT);
        $data[$ax['lg_c_emp']][$cc]['POSNR']=$cc;
        $data[$ax['lg_c_emp']][$cc]['MATNR']=$ax['lg_c_mat'];
        $data[$ax['lg_c_emp']][$cc]['MEINS']=$um;
        $data[$ax['lg_c_emp']][$cc]['LFIMG']=$ax['total'];
        $data[$ax['lg_c_emp']][$cc]['PESO']=(float)str_replace('.', ',', $ax['ca_peso']);
        $data[$ax['lg_c_emp']][$cc]['DESCRIPCION']=$db->real_escape_string($ax['ca_desc']);
        $data[$ax['lg_c_emp']][$cc]['UBICACION']=$ax['ca_ubi'];
        $data[$ax['lg_c_emp']][$cc]['CANTBULTOS']=(int)$ax['ca_bulto'];
        $data[$ax['lg_c_emp']][$cc]['ERNAM']=$ax['lg_c_user'];
        $data[$ax['lg_c_emp']][$cc]['LDDAT']=date('Ymd', strtotime('now'));
        $data[$ax['lg_c_emp']][$cc]['ERZET']=date('His', strtotime('now'));
        $cc++;
    }
//    var_dump($data);
//    VBLEN, CAJA, POSNR, MATNR, MEINS, LFIMG, PESO, DESCRIPCION, UBICACION, CANTBULTOS, ERNAM, LDDAT, ERZET
    foreach($data[$pedido] as $id => $val){
//        var_dump($val);
        $hanaqr = "INSERT INTO sapabap1.ZMM_BULTOS "
                . "(MANDT, VBLEN, CAJA, POSNR, MATNR, MEINS, LFIMG, PESO, DESCRIPCION, UBICACION, CANTBULTOS, ERNAM, LDDAT, ERZET) "
                . "values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
//                            . "values ".$vals.'';
        $res = odbc_prepare($prd,$hanaqr);
        if(!odbc_execute($res, $val)){
//            var_dump($val);
            $errs[] = 1;
        }else{
            
            $errs[] = 0;
        }
    }
//    var_dump($data);
    if(!in_array(1, $errs)){
        odbc_commit($prd); 
        
        
        $dt_snd['MANDT'] = '300';
        $dt_snd['ENTREGA'] = $pedido;
        $dt_snd['TIPO'] = '003';
        $dt_snd['PEXPEDICION'] = $_POST['exp'];
        $dt_snd['HORA'] = date("His", strtotime('now'));
        $dt_snd['FECHA'] = date("Ymd", strtotime('now'));
        $dt_snd['USUARIO'] = $usr;
        //MANDT = 300
        //ENTREGA = ENTREGA
        //TIPO = 001
        //PEXPEDICION = CD01
        //HORA = His
        //FECHA = Ymd
        //USUARIO = user
        $sqhan = "insert into sapabap1.ZMM003_REGISTRO (MANDT, ENTREGA, TIPO, PEXPEDICION, HORA, FECHA, USUARIO) values (?,?,?,?,?,?,?) ";
        //                echo $sqhan;
        $res = odbc_prepare($prd,$sqhan);
        if(!odbc_execute($res, $dt_snd)){
            $errs2[] = 1;
        }else{
            $errs2[] = 0;
        }
        if(!in_array(1, $errs)){
            odbc_commit($prd); 
        }
        
        if(isset($dt_snd2)){
            $sqhan = "insert into sapabap1.ZMM_REGISTRO_TRF (MANDT, ENTREGA, TIPO, ALMACEN, HORA, FECHA, USUARIO, CENTRO, ALMAEMISOR) values (?,?,?,?,?,?,?,?,?) ";
            //                echo $sqhan;
            $res = odbc_prepare($prd,$sqhan);
            if(!odbc_execute($res, $dt_snd2)){
                $errs2[] = 1;
            }else{
                $errs2[] = 0;
            }
            if(!in_array(1, $errs)){
                odbc_commit($prd); 
            }
        }
        
        
        
        
        $sq_gt = "select * from log_pendientes where 
                    pend_doc = '{$pedido}' and pend_st = 0";
        $rr = $db->query($sq_gt);
        if($rr->num_rows > 0){
            $sq_c = "update log_pendientes set pend_st = '1', pend_upd_ts = now() where pend_doc = '{$pedido}'";
            $db->query($sq_c);
        }
        $sq_c = "insert into log_cierre set ct_empaque = '{$pedido}', ct_user = '{$usr}', ct_st = 1, ct_ts = now()";
        $db->query($sq_c);
        $err = 0;
        $msg = "Documento cerrado correctamente";
    }else{
        $err = 1;
        $msg = "Ocurrió un error al cerrar el documento";
    }
}
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err));
