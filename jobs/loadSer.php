<?php $shell = true;
require ('/var/www/html/empaque_ok/conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$sq = "select * from arti where artser = '1 '";
$rs = $db->query($sq);
$ldd = array();
while($ax = $rs->fetch_assoc()){
    $ldd[] = $ax['artrefer'];
}
foreach($ldd as $k => $v){
    $sqhan = "select 
            likp.kunag, 
            likp.vbeln, 
            likp.erdat, 
            likp.lfart, 
            likp.erzet,
            likp.VSBED,
            lips.VGBEL, 
            lips.LFIMG, 
            lips.MATNR, 
            lips.ARKTX, 
            lips.werks,
            lips.lgort,
            lips.meins
            from sapabap1.likp
            left join sapabap1.lips on likp.vbeln = lips.vbeln 
            left join sapabap1.vbfa on vbfa.VBELV=likp.VBELN
        WHERE 
        likp.erdat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            and lips.werks = '{$centro}'
            and lips.lgort = '{$aml}'";
//                echo $sqhan;
$rst = odbc_exec($qas, $sqhan);

}
$centro = 'LCC1';
$aml = 'LDAL';
$sqhan = "select 
            likp.kunag, 
            likp.vbeln, 
            likp.erdat, 
            likp.lfart, 
            likp.erzet,
            likp.VSBED,
            lips.VGBEL, 
            lips.LFIMG, 
            lips.MATNR, 
            lips.ARKTX, 
            lips.werks,
            lips.lgort,
            lips.meins
            from sapabap1.likp
            left join sapabap1.lips on likp.vbeln = lips.vbeln 
            left join sapabap1.vbfa on vbfa.VBELV=likp.VBELN
        WHERE 
        likp.erdat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            and lips.werks = '{$centro}'
            and lips.lgort = '{$aml}'";
//                echo $sqhan;
$rst = odbc_exec($qas, $sqhan);
$t_pend = $cc = 0;
while ($rw = odbc_fetch_object($rst)){
    $cod = str_pad($rw->VBELN, 10, '0', STR_PAD_LEFT);
//    echo $cod;
    
    if(!in_array($cod, $ldd)){
        $data[$cod]['cab']['clirefer']= $rw->KUNAG;
        $data[$cod]['cab']['pedexentre']= $rw->VBELN;
        $data[$cod]['cab']['pedclase']= $rw->LFART;        
        $data[$cod]['cab']['pedexfec']= date("Ymd", strtotime($rw->ERDAT));
        $data[$cod]['cab']['pedexhor']= date("His", strtotime($rw->ERZET));
        $data[$cod]['cab']['codenv']= $rw->VSBED;
        $data[$cod]['cab']['pedexref']= $rw->VGBEL;   
        $data[$cod]['cab']['siturefe']= 'PD';
        
        $data[$cod]['det'][$cc]['pedexref']= $rw->VGBEL;
        $data[$cod]['det'][$cc]['pedexentre']= $rw->VBELN;
        $data[$cod]['det'][$cc]['pedpos']= $rw->EBELP;
        $data[$cod]['det'][$cc]['artrefer']= $rw->MATNR;
        $data[$cod]['det'][$cc]['artdesc']= utf8_encode($rw->ARKTX);
        $data[$cod]['det'][$cc]['unimed']= $rw->MEINS;
        $data[$cod]['det'][$cc]['canpedi']= $rw->MENGE;
        $cc++;
    }
} 

//Pedido de transferencias Expediciones 
$sqhan = "select distinct 
            ekko.ebeln, ekko.bsart, ekko.BEDAT, ekko.lifnr, ekpo.matnr, ekpo.ebelp, ekpo.lgort, ekpo.menge, ekpo.meins, t001l.lgobe, ekpo.werks, ekko.RESWK, nast.eruhr, lfa1.name1 
            FROM sapabap1.EKKO 
            inner join sapabap1.EKPO on ekko.ebeln=ekpo.ebeln 
            inner join sapabap1.NAST on ekko.ebeln=nast.objky 
            left join sapabap1.mseg on ekko.ebeln = mseg.ebeln 
            left join sapabap1.LFA1 on ekko.lifnr=lfa1.lifnr 
            left join sapabap1.t001l on ekpo.lgort = t001l.lgort 
            where
            nast.kschl='NEU' 
            AND ekko.bsart in ('EUB','ZUB') 
            AND ekpo.loekz not in ('L','S')
            AND ekpo.loekz = '' 
            AND ekko.bedat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            and ekko.reswk = '{$centro}'";
//                echo $sqhan;
$rst = odbc_exec($qas, $sqhan);
while ($rw = odbc_fetch_object($rst)){
    $cod = str_pad($rw->EBELN, 10, '0', STR_PAD_LEFT);
//    echo $cod;
    
    if(!in_array($cod, $ldd)){
        $data[$cod]['cab']['clirefer']= $rw->LGORT;
        $data[$cod]['cab']['pedexentre']= $rw->EBELN;
        $data[$cod]['cab']['pedclase']= $rw->BSART;        
        $data[$cod]['cab']['pedexfec']= date("Ymd", strtotime($rw->BEDAT));
        $data[$cod]['cab']['pedexhor']= date("His", strtotime($rw->ERZET));
        $data[$cod]['cab']['codenv']= '';
        $data[$cod]['cab']['pedexref']= '';   
        $data[$cod]['cab']['siturefe']= 'PD';
        
        $data[$cod]['det'][$cc]['pedexref']= '';
        $data[$cod]['det'][$cc]['pedexentre']= $rw->EBELN;
        $data[$cod]['det'][$cc]['pedpos']= $rw->EBELP;
        $data[$cod]['det'][$cc]['artrefer']= $rw->MATNR;
        $data[$cod]['det'][$cc]['artdesc']= utf8_encode($rw->ARKTX);
        $data[$cod]['det'][$cc]['unimed']= $rw->MEINS;
        $data[$cod]['det'][$cc]['canpedi']= $rw->MENGE;
        $cc++;
    }
} 

foreach($data as $cod => $dat){
//    insertar cabecera
//    echo "<br/>________<br/>";
    $sqx = "insert into pedexcab set 
        clirefer = '{$dat['cab']['clirefer']}',
        pedexentre = '{$dat['cab']['pedexentre']}', 
        pedclase = '{$dat['cab']['pedclase']}',
        pedexfec = '{$dat['cab']['pedexfec']}',
        pedexhor = '{$dat['cab']['pedexhor']}', 
        codenv = '{$dat['cab']['codenv']}',
        siturefe = '{$dat['cab']['siturefe']}',
        pedexref = '{$dat['cab']['pedexref']}'";
//        echo $sq;
//        echo "<br/>________<br/>";
    if($db->query($sqx)){
        $sqd = "insert into pedexdet (pedpos,artrefer,artdesc,unimed,canpedi,pedexref,pedexentre) values ";
        $values = '';
        foreach($dat['det'] as $ind => $dt){
            if($values == ''){
                $values = "(".(int)$dt['pedpos'].",'".$dt['artrefer']."','".$dt['artdesc']."','".$dt['unimed']."',".(int)$dt['canpedi'].",'".$dt['pedexref']."','".$dt['pedexentre']."')";
            }else{
                $values .= ",(".(int)$dt['pedpos'].",'".$dt['artrefer']."','".$dt['artdesc']."','".$dt['unimed']."',".(int)$dt['canpedi'].",'".$dt['pedexref']."','".$dt['pedexentre']."')";
            }
            
//            verifico si existe en arti
//            $ck = "select * from arti where artrefer = '{$dt['artrefer']}'";
//            $rx = $db->query($ck);
//            if($rx->num_rows == 0){
//                $sqhan = "select 
//                            marc.matnr, 
//                            mara.MATKL, 
//                            mara.MEINS, 
//                            mara.prdha, 
//                            makt.MAKTX,
//                            mara.ean11, 
//                            MARC.SERNP
//                        from sapabap1.marc
//                            inner join sapabap1.mara on marc.matnr = mara.matnr
//                            inner join sapabap1.makt on marc.matnr = makt.matnr and makt.spras='S'
//                        where 
//                            marc.werks='{$centro}'
//                            and marc.matnr = '{$dt['artrefer']}'";
//                //                echo $sqhan;
//                $rst = odbc_exec($prd, $sqhan);
//                while ($rw = odbc_fetch_object($rst)){
//                    $artser = 0;
//                    if($rw->SERNP == 'ZCHA'){
////                        SE CARGA NRO DE SERIE
//                        $artser = 1;
//                    }
////                    SE CARGA ARTI
//                    $insAt = "insert into arti set 
//                        artrefer = '{$rw->MATNR}',
//                        artdesc = '{$rw->MAKTX}',
//                        unimed = '{$rw->MEINS}',
//                        artgrup = '{$rw->MATKL}',
//                        artjerar = '{$rw->PRDHA}',
//                        artean = '{$rw->EAN11}',
//                        artser = {$artser}";
//                        $db->query($insAt);
//                }
//            }
        } 
        echo $sqd.$values;
        echo "<br/>________<br/>";
        $db->query($sqd.$values);
    }
//    
}
$scriptname = basename(__FILE__, '.php');
$sqz = "select * from scheduled_jobs where script = '{$scriptname}'";
$rs = $db->query($sqz);
if($rs->num_rows > 0){
    $sq = "update scheduled_jobs set last = now() where script = '{$scriptname}'";
    $rs = $db->query($sq);
}else{    
    $sq = "insert into scheduled_jobs set script = '{$scriptname}', last = now()";
    $rs = $db->query($sq);
}
echo "<pre>"; var_dump($data);echo "</pre>";
