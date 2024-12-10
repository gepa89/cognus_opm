<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$user = strtoupper($_POST['user']);
$terminal = strtoupper($_POST['terminal']);


if(isset($user)){
    $sq = "delete from termi_assign where tercod = '{$terminal}' and ope = '{$user}'";
//    echo $sq;
    if($db->query($sq)){
        $sqx = "update termilog set fecapag = now(),horapag = now() where tercod = '{$terminal}' and operef = '{$user}' and fecapag is null";
        $db->query($sqx);
        $sqxx = "delete from assig_ped where tercod = '{$terminal}' and st = 0";
        $db->query($sqxx);
        $sqxx = "delete from ped_multiref where terminal = '{$terminal}' and codst = 0";
        $db->query($sqxx);
        $sqxx = "delete from ubitempo where tercod = '{$terminal}'";
        $db->query($sqxx);
    }
}
