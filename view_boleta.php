<?php
require ('conect.php');
require_once("hanaDB.php");
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pds = $_POST['codigos'];
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

foreach($pds as $ind => $val) {
    $vpd = str_pad($val, 10, '0', STR_PAD_LEFT);
    $rst = odbc_exec($prd, $sqhan);
    $nmanif = "select distinct nromanif from sapabap1.zmm_manifiesto where entrega = '{$vpd}' order by nromanif desc limit 1";
    $rst = odbc_exec($prd, $nmanif);
    while ($rw = odbc_fetch_object($rst)){
        $data[$vpd]['cabecera']['manif'] = $rw->NROMANIF;
    }
    if(!$data[$vpd]['cabecera']['manif']){
        $data[$vpd]['cabecera']['manif'] = '';
    }
    if($vpd >= 4000000000){
        $sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
                left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
                where a.ebeln = '{$vpd}' and a.bukrs in ('1000','2000')";
    }else{
        $sqhanax = "select a.vstel, a.kunnr, a.kunag from sapabap1.likp a                  
            where a.vbeln = '{$pd}'";
//                echo $sqhan;
        $rstax = odbc_exec($prd, $sqhanax);
        while ($rwx = odbc_fetch_object($rstax)){
            $uax_dt[] = $rwx;
        }
        if($uax_dt['KUNNR'] != $uax_dt['KUNAG']){
            $sqhan = "select i.ebeln, a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                        left join sapabap1.lips d on d.vbeln = a.vbeln
                        left join sapabap1.vbak e on e.vbeln = d.vgbel
                        left join sapabap1.vbrk i on i.vbeln = a.vbeln
                        inner join sapabap1.kna1 b on a.kunnr = b.kunnr 
                        left join sapabap1.t005u c on b.regio = c.bland
                        left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                        left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                        left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
                    where a.vbeln = '{$pd}'";

        }else{
            $sqhan = "select i.ebeln, a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                        left join sapabap1.lips d on d.vbeln = a.vbeln
                        left join sapabap1.vbak e on e.vbeln = d.vgbel
                        left join sapabap1.vbrk i on i.vbeln = a.vbeln
                        inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                        left join sapabap1.t005u c on b.regio = c.bland
                        left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                        left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                        left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
                    where a.vbeln = '{$pd}'";

        }
    }
    $rst = odbc_exec($prd, $sqhan);
//    select distinct vbeln from sapabap1.vbfa where vbelv = '0012712950' and vbtyp_n = 'M'
//select distinct lifnr from sapabap1.vbpa where vbeln = 'vgbel' and parvw = 'SP'
    while ($rw = odbc_fetch_object($rst)){
        if($vpd>= 4800000000){
            $data[$vpd]['cabecera']['kunnr'] = ' ';
            $data[$vpd]['cabecera']['vgbel'] = ' ';
            $data[$vpd]['cabecera']['name1'] = ' ';
            $data[$vpd]['cabecera']['ort01'] = ' ';
            $data[$vpd]['cabecera']['regio'] = ' ';
            $data[$vpd]['cabecera']['vbeln'] = ' ';
            $data[$vpd]['cabecera']['lifnr'] = ' ';
            $data[$vpd]['cabecera']['nom_trans'] = ' ';
            $data[$vpd]['cabecera']['stras'] = ' ';
            $data[$vpd]['cabecera']['bezei'] = ' ';
            $data[$vpd]['cabecera']['vsbed'] = ' ';
            if($rw->BUKRS == '2000'){
                $data[$vpd]['cabecera']['vkorg'] = 'COMAGRO S.A.E.';
            }else if($rw->BUKRS == '1000'){
                $data[$vpd]['cabecera']['vkorg'] = 'CHACOMER S.A.E.';
            } 
        }else{
            $data[$vpd]['cabecera']['kunnr'] = "".$rw->KUNNR;
            $data[$vpd]['cabecera']['vgbel'] = "".$rw->VGBEL;
            $data[$vpd]['cabecera']['name1'] = str_replace(',', '', $rw->NAME1);
            $data[$vpd]['cabecera']['ort01'] = "".$rw->ORT01;
            $data[$vpd]['cabecera']['regio'] = "".$rw->REGIO;
            $data[$vpd]['cabecera']['vbeln'] = ''.$rw->EBELN;
            $data[$vpd]['cabecera']['lifnr'] = "".$rw->LIFNR;
            $data[$vpd]['cabecera']['nom_trans'] = ''.$rw->TRANSP;
            $data[$vpd]['cabecera']['stras'] = "".$rw->STRAS;
            $data[$vpd]['cabecera']['bezei'] = "".$rw->BEZEI;
            $data[$vpd]['cabecera']['vsbed'] = "".$rw->VSBED.' '.$rw->VTEXT;
            if($rw->VKORG == 'COMA'){
                $data[$vpd]['cabecera']['vkorg'] = 'COMAGRO S.A.E.';
            }else if($rw->VKORG == 'ALAS'){
                $data[$vpd]['cabecera']['vkorg'] = 'ALAS S.A.';
            }else if($rw->VKORG == 'ATLA'){
                $data[$vpd]['cabecera']['vkorg'] = 'ATLANTIC S.A.';
            }else{
                $data[$vpd]['cabecera']['vkorg'] = 'CHACOMER S.A.E.';
            }
        }
        

    }
}
//echo "<pre>"; var_dump($data); echo "<pre>";
if(count($data) > 0){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}
if($err == 0){
    foreach($pds as $ind => $val) {
        $vpd = str_pad($val, 10, '0', STR_PAD_LEFT);
        $sqc = "select * from log_cajas where ca_emp = '{$vpd}'";
//            echo $sqc;
        $rsx = $db->query($sqc);
        $cc = 0;
        while($xx = $rsx->fetch_assoc()){
            $data[$vpd]['cajas'][$cc]['entrega'] = $xx['ca_emp'];
            $data[$vpd]['cajas'][$cc]['caja'] = 'CAJA-'.str_pad($xx['ca_caja'], 5, '0', STR_PAD_LEFT);
            $data[$vpd]['cajas'][$cc]['peso'] = $xx['ca_peso'];
            $data[$vpd]['cajas'][$cc]['bulto'] = $xx['ca_bulto'];
            $data[$vpd]['cajas'][$cc]['ubicacion'] = $xx['ca_ubi'];
            $data[$vpd]['cajas'][$cc]['descripcion'] = $xx['ca_desc'];
            $cc++;
        }

    }
}
if(count($data[$vpd]['cajas']) == 0){
    $err = 1;
    $msg = 'Entrega sin cajas';
}

//echo "<pre>"; var_dump($data); echo "<pre>";


require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data));

