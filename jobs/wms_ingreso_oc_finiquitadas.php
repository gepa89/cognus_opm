<?php $shell = true;
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/hana.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';

$conn_odbc = HanaDB::obtenerInstanciaPRD();
$db = MysqlDB::obtenerInstancia();
$db->autocommit(false);
$db->begin_transaction();
$sq = "select pedrefer from oc_finiquitada";
$rs = $db->query($sq);
$ldd = array();
$data = [];
while ($ax = $rs->fetch_assoc()) {
    $ldd[] = $ax['pedrefer'];
}

//Obtengo todos los nros de pedidos
$sqhan = "SELECT
	a.ZZCOMPRA as ebeln,
	b.ZZFLETED,
	b.ZZDEPOSD2,
        
	TO_CHAR( TO_DATE( b.ZZFENBOS, 'YYYYMMDD' ), 'YYYY-MM-DD' ) AS ZZFENBOS,
	a.ZZLIFNR,
	NAME1,
	b.ZZCANBUL,
	b.ZZINTERNO,
	b.ZZASEGUR,
	b.ZZNUMPOL,
	b.ZZPTOLLE,
	ZZCONTENEDOR,
	ZZCONTTP,
	b.ZZOBS_P5,
	TO_CHAR( TO_DATE( b.ZZFLLASU, 'YYYYMMDD' ), 'YYYY-MM-DD' ) AS ZZFLLASU,
	b.ZZFVENDL,
	b.ZZPTODCO2,
	b.ZZMTSCUB,
        b.ZZOBS_P4
FROM
	SAPABAP1.ZMM_COMEX_01 a
	INNER JOIN SAPABAP1.ZMM_COMEX_02 b ON a.ZZCOMPRA = b.ZZCOMPRA
	INNER JOIN SAPABAP1.ZMM_COMEX_04 d ON a.ZZCOMPRA = d.ZZCOMPRA
	INNER JOIN SAPABAP1.LFA1 e ON a.ZZLIFNR = e.LIFNR 
WHERE
    b.ZZFENBOS = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' ) 
	AND TRIM( b.ZZFLETED ) IS NOT NULL 
	AND TRIM( b.ZZDEPOSD2 ) IS NOT NULL 
	AND TRIM( b.ZZFENBOS ) IS NOT NULL 
GROUP BY
	a.ZZCOMPRA,
	b.ZZFLETED,
	b.ZZDEPOSD2,
        
	b.ZZFENBOS,
	a.ZZLIFNR,
	NAME1,
	b.ZZCANBUL,
	b.ZZINTERNO,
	b.ZZASEGUR,
	b.ZZNUMPOL,
	b.ZZPTOLLE,
	ZZCONTENEDOR,
	ZZCONTTP,
	b.ZZOBS_P5,
	b.ZZFLLASU,
	b.ZZFVENDL,
	b.ZZPTODCO2,
	b.ZZMTSCUB,
	b.ZZOBS_P4";

$rst = odbc_exec($conn_odbc, $sqhan);
$t_pend = $cc = 0;
//print $sqhan;
while ($rw = odbc_fetch_object($rst)) {
    $cod = str_pad($rw->EBELN, 10, '0', STR_PAD_LEFT);
    //    echo $cod;

    if (!in_array($cod, $ldd)) {
        $data[$cod]['cab']['pedrefer'] = $cod;
        $data[$cod]['cab']['fletero'] = $rw->ZZFLETED;
        $data[$cod]['cab']['codalma'] = $rw->ZZDEPOSD2;
        $data[$cod]['cab']['fec_finiquito'] = date("Ymd", strtotime($rw->ZZFENBOS));
        $data[$cod]['cab']['codprove'] = $rw->ZZLIFNR;
        $data[$cod]['cab']['nomprove'] = $rw->NAME1;
        $data[$cod]['cab']['can_bultos'] = $rw->ZZCANBUL;
        $data[$cod]['cab']['num_interno'] = $rw->ZZINTERNO;
        $data[$cod]['cab']['aseguradora'] = $rw->ZZASEGUR;
        $data[$cod]['cab']['num_poliza'] = $rw->ZZNUMPOL;
        $data[$cod]['cab']['puerto_lleg'] = $rw->ZZPTOLLE;
        $data[$cod]['cab']['num_conte'] = $rw->ZZCONTENEDOR;
        $data[$cod]['cab']['tip_conte'] = $rw->ZZCONTTP;
        $data[$cod]['cab']['obse1'] = $rw->ZZOBS_P5;
        $data[$cod]['cab']['fec_lleg_asun'] = date("Ymd", strtotime($rw->ZZFLLASU));
        $data[$cod]['cab']['fecvenci_dias'] = date("Ymd", strtotime($rw->ZZFVENDL));
        $data[$cod]['cab']['puerto_devolu'] = $rw->ZZPTODCO2;
        $data[$cod]['cab']['metros_cub'] = $rw->ZZMTSCUB;
        $data[$cod]['cab']['prioridad'] = $rw->ZZOBS_P4;
        $cc++;
    }
}

foreach ($data as $cod => $dat) { 
    $sqx = "insert into oc_finiquitada set 
        pedrefer = '{$cod}',
        fletero = '{$dat['cab']['fletero']}', 
        codalma = '{$dat['cab']['codalma']}',
        fec_finiquito = '{$dat['cab']['fec_finiquito']}',
        codprove = '{$dat['cab']['codprove']}',
        nomprove = '{$dat['cab']['nomprove']}',
        can_bultos = '{$dat['cab']['can_bultos']}',
        num_interno = '{$dat['cab']['num_interno']}',
        aseguradora = '{$dat['cab']['aseguradora']}',
        num_poliza = '{$dat['cab']['num_poliza']}',
        puerto_lleg = '{$dat['cab']['puerto_lleg']}',
        num_conte = '{$dat['cab']['num_conte']}',
        tip_conte = '{$dat['cab']['tip_conte']}',
        obse1 = '{$dat['cab']['obse1']}',
        pedrefec = now(),
        pedrehorlle = now(),
        fec_lleg_asun='{$dat['cab']['fec_lleg_asun']}',
        fecvenci_dias='{$dat['cab']['fecvenci_dias']}',
        puerto_devolu = '{$dat['cab']['puerto_devolu']}',
        prioridad = '{$dat['cab']['prioridad']}',
        pedresitu = 'PD', 
        metros_cub = '{$dat['cab']['metros_cub']}'";
    //        echo "<br/>________<br/>";
 
   
//    $rs = $db->query($rs);
    $db->query($sqx);
//print $sqx;
$scriptname = basename(__FILE__, '.php');
$sq = "select * from scheduled_jobs where script = '{$scriptname}'";
$rs = $db->query($sq);
if ($rs->num_rows > 0) {
    $sq = "update scheduled_jobs set last = now() where script = '{$scriptname}'";
    $rs = $db->query($sq);
} else {
    $sq = "insert into scheduled_jobs set script = '{$scriptname}', last = now()";
    $rs = $db->query($sq);
}
}

$db->commit();
$db->close();
odbc_close($conn_odbc);
exit;
