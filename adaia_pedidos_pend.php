<?php // $shell = true;
//phpinfo();
require_once("hanaDB.php");

ini_set('memory_limit', '1024M');


$oci_qry = "SELECT EXPORDREF FROM adaia.EXPORDCAB 
WHERE ALMCOD = '2'
AND EXPORDSIT = 'CE'
AND FECORD >= trunc(sysdate) - 3
   And FECORD < trunc(sysdate) - 1";
$dt = oci_parse($conn2, $oci_qry);
oci_execute($dt);
$tt = 0;

while($row = oci_fetch_array($dt, OCI_ASSOC+OCI_RETURN_NULLS)){
    $data[] = str_pad((int)trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT);
    $tt++;
}
$sqhan = "select distinct vblen, lddat from sapabap1.ZMM_BULTOS where lddat between (TO_DATE(ADD_DAYS(current_date, -3))) and (TO_DATE(ADD_DAYS(current_date, -1)))";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$t_pend = 0;
while ($rw = odbc_fetch_object($rst)){
    $cod = str_pad($rw->VBLEN, 10, '0', STR_PAD_LEFT);
//    echo $cod;
    $data2[] = str_pad((int)trim($cod), 10, '0', STR_PAD_LEFT);
}

foreach($data as $k => $v){
    if(!in_array($v, $data2)){
        $pendientes[] = $v;
        $t_pend++;
    }
}
oci_free_statement($dt);
oci_close($prd);
echo json_encode(array('total' => $tt,'totalp' => $t_pend, 'pendintes' => $pendientes));