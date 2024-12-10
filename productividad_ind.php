<?php // $shell = true;
//phpinfo();
require ('conect.php');
ini_set('memory_limit', '1024M');


$db = new mysqli($SERVER,$USER,$PASS,$DB);
$user = strtoupper($_POST['usuario']);
$qry_main = "SELECT distinct ct_empaque, ct_user, ct_ts FROM log_cierre where ct_user = '{$user}' and date(ct_ts) >= CURDATE()";
//echo $qry_main;
$rs = $db->query($qry_main);
$usr = array();
$cc = 0;
while($ax = $rs->fetch_assoc()){
    if(!in_array($ax['ct_user'], $usrarr)){
        $usrarr[] = $ax['ct_user'];
    }
    $dt[$ax['ct_user']]['Pedidos']++;
    
    $qry_sec = "select lg_c_emp, lg_c_mat, lg_c_pos, SUM(DISTINCT lg_c_cant) as tot from log_material where lg_c_emp = '{$ax['ct_empaque']}' group by lg_c_emp, lg_c_mat, lg_c_pos order by lg_c_pos";
    
    $rss = $db->query($qry_sec);
    while($axsec = $rss->fetch_assoc()){
        $dt[$ax['ct_user']]['Materiales']++;
        $dt[$ax['ct_user']]['Cantidad'] += $axsec['tot'];
    }
}

foreach($dt as $usr => $data){
    foreach($data as $key => $val){
//        echo $key."<br/>";
            $res[$key][] = $val;
    }
}
//var_dump($dt);
echo json_encode(array('usr' =>$usrarr ,'dat' => $dt, 'series' => $res));