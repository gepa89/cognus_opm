<?php $shell = true;
//phpinfo();
require_once("conect.php");
require_once("hanaDB.php");
ini_set('memory_limit', '1024M');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$oci_qry = "SELECT EXPORDREF FROM adaia.EXPORDCAB 
WHERE ALMCOD = '2'
AND EXPORDSIT = 'CE'
AND FECORD >= trunc(sysdate)
   And FECORD < trunc(sysdate) + 1";
$dt = oci_parse($conn2, $oci_qry);
oci_execute($dt);
$tt = 0;

while ($row = oci_fetch_array($dt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = str_pad((int) trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT);
    $tt++;
}
$sqhan = "select distinct vblen, lddat from sapabap1.ZMM_BULTOS where lddat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$t_pend = 0;
while ($rw = odbc_fetch_object($rst)) {
    $cod = str_pad($rw->VBLEN, 10, '0', STR_PAD_LEFT);
    //    echo $cod;
    $data2[] = str_pad((int) trim($cod), 10, '0', STR_PAD_LEFT);
}

foreach ($data as $k => $v) {
    if (!in_array($v, $data2)) {
        $pendientes[] = $v;
        $t_pend++;
    }
}
if ($t_pend > 0) {
    foreach ($pendientes as $id => $vl) {
        //        echo $vl;
        $sq = "insert into log_pendientes set 
            pend_doc = '{$vl}',
            pend_st = 0,
            pend_ts = now()
        ";
        echo $sq . "<br/>";
        $db->query($sq);
    }
}
var_dump($pendientes);
oci_free_statement($dt);
oci_close($conn2);
//echo json_encode(array('total' => $tt,'totalp' => $t_pend,  'pend' => $pendientes));