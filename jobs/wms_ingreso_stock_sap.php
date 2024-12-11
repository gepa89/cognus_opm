<?php $shell = true;
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../hanaDB.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$centro = 'CH00';
$aml = 'DC01';
$sqhan = "select MARD.MATNR, MARD.LABST,MARD.LGORT,MAKT.MAKTX
        from sapabap1.mard
        inner join SAPABAP1.makt on mard.matnr=makt.matnr
        where werks='CH00'
        AND LGORT ='DC01'
        AND LABST > '0'
        and makt.spras ='S'";
            
//                echo $sqhan;
                
$rst = odbc_exec($qas, $sqhan);
if (!$rst) {
    print_r("error");
    print_r(odbc_error());
    exit;

}
$t_pend = $cc = 0;

//var_dump($rst);
while ($rw = odbc_fetch_object($rst)){
    $cod = $rw->MATNR;
//    echo $cod;
    
//    if(!in_array($cod, $ldd)){
//        INSER EN ARTI	TAB SAP	CAM SAP	
//        artrefer	MARA	MATNR	
//        artdesc	MAKT	MAKTX	
//        unimed	MARA	MEINS	
//        artser	MARC	SERNP	
//        artgrup	MARA	MATKL	
//        artjerar	MARA	PRDHA	
//        fecaut			Fecha que se importo el registro
        $sq = "insert into stocksap set 
                artrefer = '{$cod}',
                artdesc = '".$db->real_escape_string($rw->MAKTX)."',
                cod_alma = '{$rw->LGORT}',
                candispo = '{$rw->LABST}',
                fecingre = now()
                ";
                echo $sq."<br/>";
        
          
            
        $db->query($sq);        
//    }
} 
$scriptname = basename(__FILE__, '.php');
$sq = "select * from scheduled_jobs where script = '{$scriptname}'";
$rs = $db->query($sq);
if($rs->num_rows > 0){
    $sq = "update scheduled_jobs set last = now() where script = '{$scriptname}'";
    $rs = $db->query($sq);
}else{    
    $sq = "insert into scheduled_jobs set script = '{$scriptname}', last = now()";
    $rs = $db->query($sq);
}
echo "<pre>"; var_dump($data);echo "</pre>";
