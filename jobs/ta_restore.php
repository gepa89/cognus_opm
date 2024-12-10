<?php $shell = true;
require ('/var/www/html/wms/conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$sqhan = "select matnr,ean11,meinh,lfnum,eantp,hpean from sapabap1.mean
                where matnr like 'TA%'";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$t_pend = $cc = $cod = 0;
while ($rw = odbc_fetch_object($rst)){
    if(!in_array($cod, $ldd)){
        $data[$cod]['matnr']= $rw->MATNR;
        $data[$cod]['ean11']= $rw->EAN11;
        $data[$cod]['meinh']= $rw->MEINH;
        $data[$cod]['lfnum']= $rw->LFNUM;
        $data[$cod]['eantp']= $rw->EANTP;
        $data[$cod]['hpean']= $rw->HPEAN;
        $cod++;
    }
}
//var_dump($data);
foreach($data as $k => $v){
    $sq = "insert into ta_restore set 
        matnr = '{$v['matnr']}',
        ean11 = '{$v['ean11']}',
        meinh = '{$v['meinh']}',
        lfnum = '{$v['lfnum']}',
        eantp = '{$v['eantp']}',
        hpean = '{$v['hpean']}'";
        echo $sq."<br/>";
        $db->query($sq);
}
