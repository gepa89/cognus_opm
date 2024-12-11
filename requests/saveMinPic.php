<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$row = $_POST['data'];


$msgx = '';
    foreach($row as $k => $v){
        
        if($v[0] != ''){
//            if($v[1] < $v[2]){
                if(is_numeric($v[0])){
                    $ean = str_pad($v[0], 18, "0", STR_PAD_LEFT);
                }else{
                    $ean = $v[0];
                }
                $sq = "select * from arti a where a.artrefer = '".trim(mb_strtoupper($ean))."' and a.almcod = '".trim(mb_strtoupper($v[3]))."'";
//                echo $sq;
                $rsx = $db->query($sq);
                if($rsx->num_rows > 0){
                    $sq = "select * from artinve a inner join arti b on a.artrefer = b.artrefer where a.artrefer = '".trim(mb_strtoupper($ean))."' and a.cencod = '".trim(mb_strtoupper($v[2]))."' and a.almcod = '".trim(mb_strtoupper($v[3]))."'";
    //                        echo $sq;

                    $rs = $db->query($sq);

                    //artrefer
                    //cecod
                    //almcod
                    //artminrep
                    //artmaxrep

                    if($rs->num_rows > 0){
                        $sq_ins = " update artinve set
                            artminve = {$v[1]} 
                             where artrefer = '".trim(mb_strtoupper($ean))."' and cencod = '".trim(mb_strtoupper($v[2]))."' and almcod = '".trim(mb_strtoupper($v[3]))."'"
                            ;
            //                echo $sq_ins;
                        if($db->query($sq_ins)){
                            $lerr[] = 0;
                        }else{
                            $lerr[] = 1;
                        }
                    }else{
                        $sq_ins = " insert into artinve set
                            artminve = {$v[1]},
                            artrefer = '".trim(mb_strtoupper($ean))."', cencod = '".trim(mb_strtoupper($v[2]))."', almcod = '".trim(mb_strtoupper($v[3]))."'"
                            ;
    //                        echo $sq_ins;
                        if($db->query($sq_ins)){
                            $lerr[] = 0;
                        }else{
                            $lerr[] = 1;
                        }
                    }
                    
                }else{
                    $lerr[] = 1;
                    $fgil =$k+1;
                    $msgx .= "Fila".$fgil.': Material no existe en Maestro de Materiales ';
                }
//            }else{
//                $msg = 'Error al guardar Configuración.';
//                $fgil =$k+1;
//                $msgx .= ' Mínimo no puede superar al Máximo en fila '.$fgil.".";
//                $lerr[] = 1;
//            }
        }
    }
    
            
    if(in_array(1, $lerr)){
        $msg = 'Error al guardar Configuración.'.$msgx;
        
        $err = 1;
    }else{
        $msg = 'Configuración guardada';
        $err = 0;
    }

    echo json_encode(array('msg' => $msg, 'err' => $err));
    exit();
