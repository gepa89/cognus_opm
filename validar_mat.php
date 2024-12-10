<?php
require ('conect.php');
require_once("hanaDB.php");
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);
if($_POST['codigo'] != ''){
    $response = array();
    $usr = ($_POST['usuario']);
    $pedido = str_pad(trim($_POST['codigo']), 10, "0", STR_PAD_LEFT);
    
    //registro la lectura, independientemente de si corresponde o no
    $in_log = "insert into log_input set 
                rg_input = '{$_POST['material']}',
                rg_cant = '{$_POST['cantidad']}',
                rg_tp = '{$_POST['flag']}',
                rg_pedido = '{$pedido}',
                rg_user = '{$usr}',
                rg_ts = now()
            ";
    $db->query($in_log);
    
//    echo "aqtui";
    $f = $c->getFunction('ZMM_F_CONTROL');
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $result = $f->invoke([    
        'VUSUARIO' => $usr,
        'VENTREGA' => $pedido
    ]);
    if(is_numeric(trim($_POST['material']))){
        $cdMaterial = str_pad(trim($_POST['material']), 18, "0", STR_PAD_LEFT);
    }else{
        $cdMaterial = strtoupper(trim($_POST['material']));
    }
    $sqhan = "select distinct SMATN, A.DATAB from SAPABAP1.KOTD001 A 
                INNER JOIN SAPABAP1.KONDD B ON  A.KNUMH = B.KNUMH
                where MATWA = '{$cdMaterial}' or SMATN = '{$cdMaterial}' order by A.DATAB DESC limit 1";
//                echo $sqhan;
    $rst = odbc_exec($prd, $sqhan);
    $rw = odbc_fetch_object($rst);
    
    foreach($result["ZLIKP"] as $ind => $dat){
//        var_dump($dat);
//        ["VBELN"]
//      ["MATNR"]=>
//      ["POSNR"]=>
//      ["MEINS"]=>
//      ["ARKTTX"]=>
//      ["LFIMG"]=>
        $mats[trim($dat["MATNR"])]["VBELN"] = trim($dat["VBELN"]);
        $mats[trim($dat["MATNR"])]["MATNR"] = trim($dat["MATNR"]);
        $mats[trim($dat["MATNR"])]["POSNR"] = trim($dat["POSNR"]);
        $mats[trim($dat["MATNR"])]["MEINS"] = trim($dat["MEINS"]);
        $mats[trim($dat["MATNR"])]["ARKTTX"] = trim($dat["ARKTTX"]);
        @$mats[trim($dat["MATNR"])]["LFIMG"] += (int)$dat["LFIMG"];
    }
//    var_dump($rw);
    if($rw){
        $codmat = $rw->SMATN;
    }else{
        $codmat = $cdMaterial;
    }
//    echo $codmat;
//    var_dump($mats  );
//    echo $rw->SMATN."<pre>"; var_dump($result);echo "</pre>"; 
    if(key_exists($codmat, $mats)){
        $mdb_qry = "select sum(lg_c_cant) as total from log_material where lg_c_emp = '{$pedido}' and lg_c_mat = '{$mats[$codmat]["MATNR"]}'";
//            echo $mdb_qry;
        $rs = $db->query($mdb_qry);
        $rsx = $rs->fetch_assoc();
        $tt = (int)$rsx['total'] + (int)$_POST['cantidad'];

        if( $tt <= $mats[$codmat]['LFIMG'] ){
            $box = explode('-',$_POST['caja']);
            $ckCaja = "select * from log_cajas where ca_caja = ".(int)$box[1]." and ca_emp = '{$pedido}'";
            $rsCaja = $db->query($ckCaja);
            if($rsCaja->num_rows == 0){
                $mdb_inCa = "insert into log_cajas set ca_emp = '{$pedido}', ca_caja = ".(int)$box[1].", ca_st = 0, ca_ts = now()";
                $db->query($mdb_inCa);
            }else{
                $laCaja = $rsCaja->fetch_assoc();
            }
            
            $mdb_qryi = "insert into log_material set 
                        lg_c_emp = '{$pedido}',
                        lg_c_pos = '{$mats[$codmat]["POSNR"]}',
                        lg_c_mat = '{$mats[$codmat]["MATNR"]}',
                        lg_c_mat_desc = '".$db->real_escape_string($mats[$codmat]["ARKTTX"])."',
                        lg_c_cant = {$_POST['cantidad']},
                        lg_c_user = '{$usr}',
                        lg_c_um = '{$mats[$codmat]["MEINS"]}',
                        lg_c_caja = ".(int)$box[1].",
                        lg_ts = now()
                        ";
            if($db->query($mdb_qryi)){
                $response['btn'] = '<i class="splashy-remove"></i>';
                $response['ID'] = $db->insert_id;
                $response['Ent'] = $pedido;
                $response['Mat'] = $codmat;
                $response['Des'] = trim(str_replace(array(','), '.', $mats[$codmat]["ARKTTX"]));
                $response['Can'] = $_POST['cantidad'];
                $response['Caj'] = "CAJA-".str_pad((int)$box[1], 5, '0', STR_PAD_LEFT);
                $response['Pes'] = $laCaja['ca_peso'];
                $response['Ubi'] = $laCaja['ca_ubi'];
                $response['Bul'] = $laCaja['ca_bulto'];
                $response['DsCa'] = $laCaja['ca_desc'];
            }
            $err = 0;
            $msg = '';
        }else{
            $err = 1;
            $msg = 'Cantidad ingresada exede al pedido';
        }
    }else{
        $err = 1;
        $msg = 'Material no corresponde al pedido';
    }
    require ('closeconn.php');
    echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $response));
}
