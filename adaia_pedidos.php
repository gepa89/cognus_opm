<?php // $shell = true;
//phpinfo();
require_once("hanaDB.php");
ini_set('memory_limit', '1024M');


$oci_qry = "SELECT EXPORDREF FROM adaia.EXPORDCAB 
WHERE ALMCOD = '2'
AND EXPORDSIT = 'CE'
AND FECORD >= trunc(sysdate)
   And FECORD < trunc(sysdate) + 1";
$dt = oci_parse($conn2, $oci_qry);
oci_execute($dt);
$tt = 0;

while($row = oci_fetch_array($dt, OCI_ASSOC+OCI_RETURN_NULLS)){
    $data[] = str_pad((int)trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT);
    $tt++;
}
$sqhan = "select distinct vblen, lddat from sapabap1.ZMM_BULTOS where lddat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$t_proc = 0;
while ($rw = odbc_fetch_object($rst)){
    $cod = str_pad($rw->VBLEN, 10, '0', STR_PAD_LEFT);
//    echo $cod;
    if(in_array($cod, $data)){
        $procesado[] = $cod;
        $t_proc++;
    }
}

oci_free_statement($dt);
oci_close($conn2);
echo json_encode(array('total' => $tt,'totalp' => $t_proc, 'dat' => $data, 'proc' => $procesado));