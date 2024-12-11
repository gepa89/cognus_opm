<?php
require ('conect.php');
include 'src/adLDAP.php';
require_once("hanaDB.php");
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad($_POST['codigo'], 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

//0012712986
//4900000000

if($pd>= 4800000000){
    $sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
            left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
            where a.ebeln = '{$pd}' and a.bukrs in ('1000','2000')";
}else{
    $sqhan = "select distinct a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed, f.vtext as transp from sapabap1.likp a 
                left join sapabap1.lips d on d.vbeln = a.vbeln
                left join sapabap1.vbak e on e.vbeln = d.vgbel
                inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                left join sapabap1.t005u c on b.regio = c.bland
            left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S'
            where a.vbeln = '{$pd}'";
}

//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    if($pd>= 4800000000){
        $data['kunnr'] = '';
        $data['vgbel'] = '';
        $data['name1'] = '';
        $data['ort01'] = '';
        $data['regio'] = '';
        $data['stras'] = '';
        $data['vkorg'] = '';
        $data['vsbed'] = '';
        if($rw->BUKRS == '2000'){
            $data['name1'] = 'COMAGRO S.A.E.';
        }else if($rw->BUKRS == '1000'){
            $data['name1'] = 'CHACOMER S.A.E.';
        } 
    }else{
        $data['kunnr'] = $rw->KUNNR;
        $data['vgbel'] = $rw->VGBEL;
        $data['name1'] = $rw->NAME1;
        $data['ort01'] = $rw->ORT01;
        $data['regio'] = $rw->REGIO;
        $data['stras'] = $rw->STRAS;
        $data['bezei'] = $rw->BEZEI;
        $data['vsbed'] = $rw->VSBED.' '.$rw->VTEXT;
        if($rw->VKORG == 'COMA'){
            $data['vkorg'] = 'COMAGRO S.A.E.';
        }else if($rw->VKORG == 'ALAS'){
            $data['vkorg'] = 'ALAS S.A.';
        }else if($rw->VKORG == 'ATLA'){
            $data['vkorg'] = 'ATLANTIC S.A.';
        }else{
            $data['vkorg'] = 'CHACOMER S.A.E.';
        }
    }
}
if(count($data) > 0){
    $err = 0;
    $msg = '';
}else{
    $err = 1;
    $msg = 'Entrega no encontrada';
}

if($err == 0){
    $sqc = "select * from log_cajas where ca_emp = '{$pd}'";
    //echo $sqc;
    $rsx = $db->query($sqc);
    while($xx = $rsx->fetch_assoc()){
        $caja[] = $xx;
    }
    if(count($caja) > 0){
        $err = 0;
        $msg = '';
    }else{
        $err = 1;
        $msg = 'Entrega no controlada';
    }
}


require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err, 'dat' => $data, 'cajas' => $caja, 'cod' => md5($pd.strtotime('now')), 'cod2' => $pd));

