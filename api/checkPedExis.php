<?php
//require ('../conect.php');
////echo '<pre>';var_dump($_POST);echo '</pre>';
//$db = new mysqli($SERVER,$USER,$PASS,$DB);
////crear hash del estante
//$terminal = strtoupper($_POST['pedido']);
//
//$response = array();
//$centro = 'LCC1';
//$aml = 'LDAL';
//$sqhan = "select distinct
//            ekko.ebeln, 
//            ekko.bsart, 
//            ekko.BEDAT, 
//            ekko.lifnr,
//            ekpo.matnr, 
//            ekpo.reslo, 
//            ekpo.ebelp, 
//            ekpo.lgort, 
//            ekpo.menge,
//            ekpo.meins,
//            ekpo.werks,
//            ekko.RESWK,
//            nast.eruhr,
//            lfa1.name1
//        FROM sapabap1.EKKO
//            inner join sapabap1.EKPO on ekko.ebeln=ekpo.ebeln 
//            inner join sapabap1.NAST on ekko.ebeln=nast.objky 
//            left join sapabap1.mseg on ekko.ebeln = mseg.ebeln
//            left join sapabap1.LFA1 on ekko.lifnr=lfa1.lifnr
//        where             
//            nast.kschl='NEU'
//            AND ekko.bedat  = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
//            AND ekpo.loekz not in ('L','S')
//            and ekpo.werks = '{$centro}'
//            and ekpo.lgort = '{$aml}'";
////                echo $sqhan;
//$rst = odbc_exec($prd, $sqhan);
//$t_pend = $cc = 0;
//while ($rw = odbc_fetch_object($rst)){
//    $data[] = $rw; 
//}

//    if(count($data) > 0){
        $response["error"] = FALSE;
//    }else{
//        $response["error"] = TRUE;
//        $response['mensaje'] = "Pedido no encontrado.";
//    }
    
         echo json_encode($response);
 include '/var/www/html/closeconn.php';
