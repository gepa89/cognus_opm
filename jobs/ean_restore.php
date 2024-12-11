<?php $shell = true;
require ('/var/www/html/empaque_ok/conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$sqhan = "select * from la_restore where updtd = 0";
//                echo $sqhan;
$rst = $db->query($sqhan);
$t_pend = $cc = $cod = 0;
while ($rw = $rst->fetch_assoc()){
    $data[$cod]['matnr']= substr_replace($rw['matnr'], "LA", 0, 2) ;
    $data[$cod]['ean11']= $rw['ean11'];
    $data[$cod]['meinh']= $rw['meinh'];
    $data[$cod]['lfnum']= $rw['lfnum'];
    $data[$cod]['eantp']= $rw['eantp'];
    $data[$cod]['hpean']= $rw['hpean'];
    $cod++;
}
//var_dump($data);
foreach($data as $k => $v){
    $sq = "insert into la_restore set 
        matnr = '{$v['matnr']}',
        ean11 = '{$v['ean11']}',
        meinh = '{$v['meinh']}',
        lfnum = '{$v['lfnum']}',
        eantp = '{$v['eantp']}',
        hpean = '{$v['hpean']}'";
        echo $sq."<br/>";
        $db->query($sq);
}
