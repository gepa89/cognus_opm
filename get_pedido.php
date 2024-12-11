<?php
require ('/var/www/html/hanaDB.php');
 include('conection.php');
$arrAux = array();
if($_SESSION['user_rol'] == 2){
    $solicitante = " and ERNAM = '".$_SESSION['user']."'";
}else{
    $solicitante = '';
}
$flg = 0;
if(isset($_REQUEST) && $_REQUEST['id']){
    $sq = "AND a.BANFN = '{$_REQUEST['id']}'";
    $flg = 1;
}else if($_REQUEST['arg']){
    $sq = "AND a.BANFN like '%{$_REQUEST['arg']}%'";
    $flg = 1;
}else{
    $sq = "AND a.BADAT >= (TO_DATE(ADD_DAYS(current_date, -60)))";
    $flg = 0;
}
$grp = '';
if($_SESSION['user'] == 'PERMARTI'){
    $grp = "and a.AFNAM <> ''";
}else{
    $grp = "and j.EKGRP = (SELECT GRUPO_COMPRAS FROM ZGCOMPRAS where JEFE = '{$_SESSION['user']}' limit 1)";
}

//var_dump($grupos);
 $oci_qry = "SELECT DISTINCT
	a.BANFN,
	a.ERNAM,
	a.BADAT,
	a.AFNAM,
	a.BEDNR,
	c.SAKTO,
	c.KOSTL,
	c.AUFNR,
	a.BEDNR,
	d.TXT50,
	e.NAME1,
	f.KTEXT 
FROM
	SAPABAP1.eban a
	LEFT JOIN SAPABAP1.CDHDR b ON b.OBJECTID = a.BANFN
	LEFT JOIN SAPABAP1.EBKN c ON c.BANFN = a.BANFN
	LEFT JOIN SAPABAP1.skat d ON c.sakto = d.SAKNR
	LEFT JOIN SAPABAP1.CEPC e ON c.KOSTL = e.prctr 
	LEFT JOIN SAPABAP1.eket h on a.banfn=h.banfn
	left join sapabap1.ekko j on h.ebeln=j.ebeln
	
	AND e.kokrs IN ( 'SC01', 'SC02', 'SC03' )
	LEFT JOIN SAPABAP1.COAS f ON c.AUFNR = f.AUFNR
        
WHERE  a.FRGKZ = '6' 
        and a.LOEKZ <> 'X'
        and a.EBELN = ''
        {$grp}
        {$sq} order by a.BADAT desc";
//echo $oci_qry;
        $nop='';
$result = odbc_exec($prd, $oci_qry);
while ($row = odbc_fetch_object($result)){
    $nop = $row->BEDNR;
    $dt = "select distinct
            a.EBELN, 
            a.ETENR, 
            a.EINDT, 
            a.SLFDT,
            c.OBJECTID, 
            c.USERNAME, 
            c.TCODE,
            d.NETPR,
            d.NETWR,
            e.SAKTO,e.KOSTL,e.AUFNR
        from SAPABAP1.EKET  a
        left join SAPABAP1.CDHDR c ON a.EBELN = c.OBJECTID
        left join SAPABAP1.EKPO d ON a.EBELN = d.EBELN
        left join SAPABAP1.EKKN e ON a.EBELN = e.EBELN
        where a.BANFN = '{$row->BANFN}' order by a.EBELN desc";
//                        echo $dt."<br/>";
    $rs_pf = odbc_exec($prd, $dt);
    $of_ap=0;
    $user_data = '';
    $usr_data = '';
    if($row->ERNAM == 'comprasc'){
        $usr_data = $row->BEDNR;
    }else{
        $usr_data = $row->ERNAM;
    }
    $usr_qry = "select t1.BNAME, t2.NAME_TEXT from SAPABAP1.USR01 t1 LEFT JOIN SAPABAP1.USER_ADDRP t2 ON t1.BNAME = t2.BNAME where t1.BNAME = '{$usr_data}' limit 1";
//    echo $usr_qry;
    $usrs = odbc_exec($prd, $usr_qry);
//        var_dump($usrs);
//    $rs = odbc_fetch_object($usrs);
//    var_dump($rs);
    while ($rs = odbc_fetch_object($usrs)){
//        echo "entro";
        $user_data = $rs->NAME_TEXT;
    }
        $dt_ofertas[$row->BANFN]['cuenta'] = $row->SAKTO;
        $dt_ofertas[$row->BANFN]['ceco'] = $row->KOSTL;
        $dt_ofertas[$row->BANFN]['ordint'] = $row->AUFNR;
        $dt_ofertas[$row->BANFN]['descuenta'] = $row->TXT50;
        $dt_ofertas[$row->BANFN]['desceco'] = $row->NAME1;
        $dt_ofertas[$row->BANFN]['desordenin'] = $row->KTEXT;
    $dt_ofertas[$row->BANFN]['n_op'] = $nop;
        $dt_ofertas[$row->BANFN]['usr'] = $user_data;
        $dt_ofertas[$row->BANFN]['fecha'] = date("d-m-Y",strtotime($row->BADAT));
    while ($rs = odbc_fetch_object($rs_pf)){
        
//        echo $rs->NETWR."<br/>";
        if((int)$rs->EBELN > 6000000000){
            if($rs->NETPR > 0){
                if(!isset($ofertas[$row->BANFN][$rs->EBELN]['of'])){
                    $ofertas[$row->BANFN][$rs->EBELN]['of']++;
                }
                if(trim($rs->TCODE) == 'ME45'){
                    $ofertas[$row->BANFN][$rs->EBELN]['ofap']++;
                }
            }
        }
        
        
//         echo $flg;
//                                
         if($flg == 1){
             
             if(!in_array($rs->EBELN, $arrAux)){
//                 echo "entro";
                 $bln = $rs->EBELN;
                $qry5 = "SELECT *  FROM SAPABAP1.A016 where  EVRTN LIKE '%".$bln."%'";
//                 echo $qry5."<br/>";
                $dt5 = odbc_exec($prd, $qry5);
                while ($rs = odbc_fetch_object($dt5)){
                    $ax[] = $rs;
                }
                $arrAux[] = $bln;
                foreach($ax as $kx => $vx){
               $qrymat = "SELECT DISTINCT
                            t2.EBELP,                    
                            t2.TXZ01,
                            t2.KTMNG,
                            t2.NETPR,
                            t2.NETWR,
                            t2.LOEKZ,
                            t1.WAERS
                    FROM
                            SAPABAP1.EKKO t1
                            INNER JOIN SAPABAP1.EKPO t2 ON  t1.EBELN = t2.EBELN
                            where t2.EBELN = '".$vx->EVRTN."'";
//                                           echo $qryprv."<br/>";
                   $dt7 = odbc_exec($prd, $qrymat);
                   while ($rsmat = odbc_fetch_object($dt7)){
                       if($rsmat->LOEKZ != 'L'){
                            $data_of[$vx->EVRTN][$rsmat->EBELP]['DESC'] = $rsmat->TXZ01;
                            $data_of[$vx->EVRTN][$rsmat->EBELP]['CANT'] = (int)$rsmat->KTMNG;
                            $data_of[$vx->EVRTN][$rsmat->EBELP]['MON'] = $rsmat->WAERS;
                       }else{
                           unset($data_of[$vx->EVRTN]);
                           unset($ofertas[$row->BANFN][$vx->EVRTN]);
                       }
                      
                   }
//               
               
               $oci_qrydwn = "select  max(KBETR) as mayor, min(KBETR) as menor from SAPABAP1.KONP where KNUMH =  '".$vx->KNUMH."'";
//                               echo "   <br/>    ".($oci_qrydwn);
               $dtdwn = odbc_exec($prd, $oci_qrydwn);
   //            var_dump($vx);
               while($row6 = odbc_fetch_object($dtdwn)){ 
                   if(isset($row6->MAYOR) || isset($row6->MENOR)){
                       $mn = '';
                        $qryprv = "SELECT DISTINCT
                            
                                        t1.WAERS,
                                       t1.EBELN,
                                       t7.LIFNR,
                                       t7.NAME1 AS d_proveedor,
                                       t7.STCD1,
                                       t7.STRAS,
                                       t7.TELF1,
                                       t7.TELF2
                                   FROM
                                       SAPABAP1.EKKO t1
                                       LEFT JOIN SAPABAP1.LFA1 t7 ON t7.LIFNR = t1.LIFNR
                                   WHERE
                                       t1.EBELN = '".$vx->EVRTN."'";
       //                                        echo $qryprv."<br/>";
                       $dt6 = odbc_exec($prd, $qryprv);
                       while ($rsprv = odbc_fetch_object($dt6)){
                           $provs[$vx->EVRTN]= $rsprv->D_PROVEEDOR;
                           $mn = $rsprv->WAERS;
                       }
                       if($mn == 'PYG'){
                           $data_of[$vx->EVRTN][$vx->EVRTP]['MAYORU'] = number_format(($row6->MAYOR*100), 0, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MAYOR'] = number_format(($row6->MAYOR*100)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT'], 0, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MENORU'] = number_format(($row6->MENOR*100), 0, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MENOR'] = number_format(($row6->MENOR*100)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT'], 0, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['RAW'] = (($row6->MENOR*100)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT']);
                            $porcen = 100 - (($row6->MENOR)*100)/$row6->MAYOR;
                            $data_of[$vx->EVRTN][$vx->EVRTP]['PORC'] = number_format($porcen, 2, ',','.');
                       }else{
                           $data_of[$vx->EVRTN][$vx->EVRTP]['MAYORU'] = number_format(($row6->MAYOR), 2, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MAYOR'] = number_format(($row6->MAYOR)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT'], 2, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MENORU'] = number_format(($row6->MENOR), 2, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['MENOR'] = number_format(($row6->MENOR)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT'], 2, ',','.');
                            $data_of[$vx->EVRTN][$vx->EVRTP]['RAW'] = (($row6->MENOR)*(int)$data_of[$vx->EVRTN][$vx->EVRTP]['CANT']);
                            $porcen = 100 - (($row6->MENOR)*100)/$row6->MAYOR;
                            $data_of[$vx->EVRTN][$vx->EVRTP]['PORC'] = number_format($porcen, 2, ',','.');
                       }
                       
                   }else{
                       unset($ofertas[$vx->EVRTN]);
                   }
               }
               
               $qrymat = "SELECT DISTINCT
                            t2.EBELP,                    
                            t2.TXZ01,
                            t2.KTMNG,
                            t2.NETPR,
                            t2.NETWR,
                            t1.WAERS
                    FROM
                            SAPABAP1.EKKO t1
                            INNER JOIN SAPABAP1.EKPO t2 ON  t1.EBELN = t2.EBELN
                            where t2.EBELN = '".$vx->EVRTN."'";
//                                           echo $qryprv."<br/>";
                   $dt7 = odbc_exec($prd, $qrymat);
                   while ($rsmat = odbc_fetch_object($dt7)){
                       $data_of[$vx->EVRTN][$rsmat->EBELP]['DESC'] = $rsmat->TXZ01;
                       $data_of[$vx->EVRTN][$rsmat->EBELP]['CANT'] = (int)$rsmat->KTMNG;
                       $data_of[$vx->EVRTN][$rsmat->EBELP]['MON'] = $rsmat->WAERS;
                   }
           }
             }
         }
        
    }
}
foreach($ofertas as $k => $v){
    if(count($v) > 0){
        $pedidos[$k]['c_oferta'] = count($v);
        
        foreach($v as $k2 => $v2){
            if($v2['ofap'] > 0){
                $pedidos[$k]['o_aprobada'] += 1;
            }else{
                $pedidos[$k]['o_aprobada'] += 0;
            }
        }
    }
}
$total=0;
foreach($pedidos as $k => $v){
    if($v['c_oferta'] > 0 && $v['o_aprobada'] == 0){
        $total++;
    }
}
$data['total'] = $total;
//var_dump($data_of);
echo json_encode(array("total" => $data, "ofertas" => $ofertas, "pedidos" => $pedidos, "datos" => $dt_ofertas, "d_ofertas" => $data_of, "provs" =>$provs));
