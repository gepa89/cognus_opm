<?php $shell = true;
//phpinfo();
require ('conect.php');
require_once("hanaDB.php");
ini_set('memory_limit', '1024M');

$db = new mysqli($SERVER,$USER,$PASS,$DB);

//obtengo almacenes
$sq = "select * from prep_almacen";
$rs = $db->query($sq);
while($ax = $rs->fetch_assoc()){
    if(!@$alm[$ax['al_org_venta']][$ax['al_centro']]){
        @$alm[$ax['al_org_venta']][$ax['al_centro']] = "'".$ax['al_almacen']."'";
    }else{
        @$alm[$ax['al_org_venta']][$ax['al_centro']] .= ",'".$ax['al_almacen']."'";
    }
}

foreach($alm as $org => $dt1){
    foreach($dt1 as $centro => $dt2){
        $sqhan = "select 
                    a.KUNAG,
                    a.VBELN,
                    a.ERDAT,
                    a.VKORG,
                    a.LFART,
                    a.VSTEL,
                    b.RFMNG,
                    b.VBTYP_N,
                    c.LFIMG,
                    c.MATNR,
                    c.ARKTX,
                    c.LGPBE,
                    c.VGBEL,
                    c.ERDAT,
                    c.WERKS,
                    c.LGORT
                     from sapabap1.likp a
                            left join sapabap1.vbfa b on a.vbeln = b.vbelv
                            left join sapabap1.lips c on a.vbeln = c.vbeln
                    where 
                            a.vkorg = '{$org}'
                            and a.lfart in ('ZCRE','ZCON','ZCOS')
                            and c.werks = '{$centro}'
                            and c.lgort in ({$dt2})
                            and b.RFMNG is null 
                            and a.erdat >= (TO_DATE(current_date))";
//        echo $sqhan."<br/>";
        $rst = odbc_exec($qas, $sqhan);
        while ($rw = odbc_fetch_object($rst)){
            $ins = "insert into prep_ingresos set
                    prep_cli = '".trim($rw->KUNAG)."',
                    prep_entrega = '".trim($rw->VBELN)."',
                    prep_cre = '".date('Y-m-d',strtotime(trim($rw->ERDAT)))."',
                    prep_org_venta = '".trim($rw->VKORG)."',
                    prep_cl_ent = '".trim($rw->LFART)."',
                    prep_pto_exp = '".trim($rw->VSTEL)."',
                    prep_cant_pedida = '".trim((float)$rw->LFIMG)."',
                    prep_mat = '".trim($rw->MATNR)."',
                    prep_mat_dec = '".$db->real_escape_string(trim(str_replace(',', '.', $rw->ARKTX)))."',
                    prep_ubi = '".trim($rw->LGPBE)."',
                    prep_pedido = '".trim($rw->VGBEL)."',
                    prep_centro = '".trim($rw->WERKS)."',
                    prep_almacen = '".trim($rw->LGORT)."',
                    prep_st = '0',
                    prep_ts = now()";
            echo $ins."<br/><br/>";
            $db->query($ins);

        }
    }
}

 require ('closeconn.php');
//echo json_encode(array('total' => $tt,'totalp' => $t_proc, 'dat' => $data, 'proc' => $procesado));