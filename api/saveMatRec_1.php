<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$user = $_POST['usuario'];
$pedido = strtoupper($_POST['pedido']);
//4300088215
//LA0002550

 if(isset($pedido)){
    $selPa = "select pal_status from pallets where pal_pedido = '{$pedido}' and pal_desc = {$_POST['pallet']}";
    $r1 = $db->query($selPa);
    if($r1->num_rows == 0){
        $inPal = "insert into pallets set 
            pal_status = 0,
            pal_desc = {$_POST['pallet']},
            pal_pedido = '{$pedido}',
            pal_usr = '{$user}',
            pat_ts = now()
        ";
        $db->query($inPal);
    }
    $selMa = "SELECT a.canpedi, b.artser FROM pedredet a left join arti b on a.artrefer = b.artrefer  where a.pedrefer = '{$pedido}' and b.artrefer = '{$_POST['material']}' limit 1";
    $r2 = $db->query($selMa);
    $r2a = $r2->fetch_assoc(); 
    
    $selMa = "select sum(ap_cant) as total from pallet_mat where ap_pedido = '{$pedido}' and ap_mat = '{$_POST['material']}' group by ap_mat";
    $r3 = $db->query($selMa);
    $r3a = $r3->fetch_assoc();
    $canti = (int)$r3a['total']+(int)$_POST['cantidad'];
    $pen = (int)$r2a['canpedi']-(int)$canti;
    
        if($r2a['artser'] == 'ZCHA'){
            $sqhan = "SELECT DISTINCT EKPO.MATNR, MSEG.MBLNR, SER03.OBKNR, OBJK.SERNR, OBJK.EQUNR, EQUI.BAUJJ, EQUI.BAUMM
                        FROM SAPABAP1.MSEG, SAPABAP1.SER03, SAPABAP1.OBJK, SAPABAP1.EQUI, SAPABAP1.EKPO

                        WHERE MSEG.EBELN='{$pedido}'
                        and EKPO.MATNR='{$_POST['material']}'
                        and MSEG.SHKZG='S'
                        AND MSEG.MBLNR=SER03.MBLNR
                        AND SER03.SHKZG = 'S'
                        AND SER03.OBKNR=OBJK.OBKNR
                        AND OBJK.SERNR=EQUI.SERNR
                        AND EKPO.MATNR=EQUI.MATNR";
//                    echo $sqhan;
            $rst = odbc_exec($prd, $sqhan);
            $sernro = '';
            while ($rw = odbc_fetch_object($rst)){
                $sernro = $rw->SERNR;
                $mes = $rw->BAUJJ;
                $anio = $rw->BAUMM;
            }
            $inSer = "insert into serati set 
                artrefer = '{$_POST['material']}',
                artserie = '{$sernro}',
                artanio = '{$anio}',
                artmes = '{$mes}'
            ";

            $db->query($inSer);  
        }
        if($pen < 0){        
            $rgMat = "update pedredet set 
                canprepa = ".(int)$r2a['canpedi'].",
                canpendi = 0,
                canexp = ". abs($pen)."
                where pedrefer  = '{$pedido}' and artrefer = '{$_POST['material']}'
            ";
            if($db->query($rgMat)){
                $sq = " insert into artexdentrec set pedrefer= '{$pedido}',
                        artrefer = '{$_POST['material']}',
                        cant = ".abs($pen).",
                        artts = now()";
                if($db->query($sq)){      
                     if($_POST['serie'] != ''){
                        $rgMatser = "update serati set 
                                serrecep = 1
                                where artped  = '{$pedido}' and artserie = '{$_POST['serie']}'
                            ";


                        $db->query($rgMatser);
                    }

                    $inMat = "insert into pallet_mat set 
                        ap_pedido = '{$pedido}',
                        ap_mat = '{$_POST['material']}',
                        ap_cant = {$_POST['cantidad']},
                        ap_termi = '{$_POST['terminal']}',
                        ap_pall = {$_POST['pallet']},
                        etnum = '{$_POST['multiRef']}', 
                        ap_usr = '{$user}',
                        ap_ts = now()
                    ";
        //                echo $inMat;
                    if($db->query($inMat)){        
                        $response["error"] = FALSE;
                        $response['mensaje'] = "Material excedente registrado";
                    }else{
                        $response["error"] = TRUE;
                        $response['mensaje'] = "Error al guardar en pallet.";
                    }
                }else{
                    $response["error"] = TRUE;
                    $response['mensaje'] = "Error al guardar en pallet.";
                }
            }else{
                    $response["error"] = TRUE;
                    $response['mensaje'] = "Error al guardar material.";
                }
        }else{
            
            $rgMat = "update pedredet set 
                canprepa = {$canti},
                canpendi = {$pen}
                where pedrefer  = '{$pedido}' and artrefer = '{$_POST['material']}'
            ";
               
            if($db->query($rgMat)){  
            
                if($_POST['serie'] != ''){
                    $rgMatser = "update serati set 
                            serrecep = 1
                            where artped  = '{$pedido}' and artserie = '{$_POST['serie']}'
                        ";


                    $db->query($rgMatser);
                }

                $inMat = "insert into pallet_mat set 
                    ap_pedido = '{$pedido}',
                    ap_mat = '{$_POST['material']}',
                    ap_cant = {$_POST['cantidad']},
                    ap_termi = '{$_POST['terminal']}',
                    ap_pall = {$_POST['pallet']},
                    etnum = '{$_POST['multiRef']}',                        
                    ap_usr = '{$user}',
                    ap_ts = now()
                ";
    //                echo $inMat;
                if($db->query($inMat)){        
                    $response["error"] = FALSE;
                    $response['mensaje'] = "Material registrado";
                }else{
                    $response["error"] = TRUE;
                    $response['mensaje'] = "Error al guardar en pallet.";
                }
            }else{
                $response["error"] = TRUE;
                $response['mensaje'] = "Error al registrar cantidad.";
            }
        }
            
        
    
    echo json_encode($response);
}

