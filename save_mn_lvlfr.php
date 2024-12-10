<?php
require ('conect.php');
require_once("hanaDB.php");
$db = new mysqli($SERVER,$USER,$PASS,$DB);
date_default_timezone_set('America/Asuncion');
// var_dump($_POST);
if(count($_POST['codigos']) > 0){
    foreach($_POST['codigos'] as $id => $pedido){
        $response = array();
        $usr = ($_POST['usuario']);
        $pedido = str_pad($pedido, 10, "0", STR_PAD_LEFT);

        if($pedido>= 4800000000){
            $sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
                    left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
                    where a.ebeln = '{$pedido}' and a.bukrs in ('1000','2000')";
        }else{
            $sqhan = "select a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                        left join sapabap1.lips d on d.vbeln = a.vbeln
                        left join sapabap1.vbak e on e.vbeln = d.vgbel
                        inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                        left join sapabap1.t005u c on b.regio = c.bland
                        left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                        left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                        left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
                    where a.vbeln = '{$pedido}'";
        }
//        echo $sqhan;
        $rst = odbc_exec($prd, $sqhan);
        odbc_autocommit($prd,false); 
        while ($rw = odbc_fetch_object($rst)){
            if($pedido>= 4800000000){
                $data['vstel'] = '';
                $data['kunnr'] = $rw->LGORT;
                $data['vgbel'] = '';
                $data['name1'] = '';
                $data['lifnr'] = '';
                $data['ort01'] = '';
                $data['regio'] = '';
                $data['stras'] = '';
                $data['bezei'] = '';
                $data['vsbed'] = '';
                
                $dt_snd2['MANDT'] = '300';
                $dt_snd2['ENTREGA'] = ''.$pedido;
                $dt_snd2['TIPO'] = '004';
                $dt_snd2['ALMACEN'] = ''.$rw->WERKS;
                $dt_snd2['HORA'] = date("His", strtotime('now'));
                $dt_snd2['FECHA'] = date("Ymd", strtotime('now'));
                $dt_snd2['USUARIO'] = ''.strtoupper($_POST['mnChofer']);
                $dt_snd2['CENTRO'] = ''.$rw->LGORT;
                $dt_snd2['ALMAEMISOR'] = ''.$rw->RESLO;
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
                
            }else{
                $data['vstel'] = $rw->VSTEL;
                $data['kunnr'] = $rw->KUNNR;
                $data['vgbel'] = $rw->VGBEL;
                $data['name1'] = $rw->NAME1;
                $data['lifnr'] = $rw->TRANSP;
                $data['ort01'] = $rw->ORT01;
                $data['regio'] = $rw->REGIO;
                $data['stras'] = $rw->STRAS;
                $data['bezei'] = $rw->BEZEI;
                $data['vsbed'] = $rw->VSBED." - ".$rw->VTEXT; 

                $dt_snd['MANDT'] = '300';
                $dt_snd['ENTREGA'] = ''.$pedido;
                $dt_snd['TIPO'] = '004';
                $dt_snd['PEXPEDICION'] = ''.$data['vstel'];
                $dt_snd['HORA'] = date("His", strtotime('now'));
                $dt_snd['FECHA'] = date("Ymd", strtotime('now'));
                $dt_snd['USUARIO'] = ''. strtoupper($_POST['mnChofer']);
                $dt_snd['NROCHAPA'] = ''.$_POST['mnChapa'];
                $dt_snd['MOTIVO'] = '';
                $dt_snd['OBSERVACION'] = ''.$_POST['mnObs'];
                //MANDT = 300
                //ENTREGA = ENTREGA
                //TIPO = 001
                //PEXPEDICION = CD01
                //HORA = His
                //FECHA = Ymd
                //USUARIO = user
                $sqhan = "insert into sapabap1.ZMM003_REGISTRO (MANDT, ENTREGA, TIPO, PEXPEDICION, HORA, FECHA, USUARIO, NROCHAPA, MOTIVO, OBSERVACION) values (?,?,?,?,?,?,?,?,?,?) ";
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
            }
        }
//        echo "<pre>";var_dump($dt_snd);echo "</pre>";
        
    } 
}
require ('closeconn.php');
echo json_encode(array('msg' => '', 'err' => 0));
