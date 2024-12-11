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
$sq = '';
if(isset($_REQUEST) && $_REQUEST['id']){
    $sq .= "AND a.ebeln = '{$_REQUEST['id']}'";
    $flg = 1;
}else if($_REQUEST['arg']){
    $sq .= "AND a.ebeln like '%{$_REQUEST['arg']}%'";
    $flg = 1;
}
$oci_qry = "select distinct frgco from sapabap1.T16FW where werks = '' and objid = '{$_SESSION['user']}'";
//echo $oci_qry;
        $nop='';
$result = odbc_exec($prd, $oci_qry);
$REL_CODE = '';
while ($row = odbc_fetch_object($result)){
    $REL_CODE = $row->FRGCO;
}
if($REL_CODE != ''){
    $sq .= " AND g.FRGCO = '{$REL_CODE}'";
    $flg = 1;
}
//var_dump($grupos);
 $oci_qry = "select distinct  
                a.ebeln,
                a.FRGGR,
                a.BSART,
                a.FRGKE,
                a.EKORG,
                a.ERNAM,
                a.BEDAT,
                a.LIFNR,
                a.WAERS,
                b.TXZ01,
                b.EBELP,
                b.WERKS,
                b.LGORT,
                b.MENGE,
                b.NETPR,
                b.MWSKZ,
                b.PEINH,
                c.SAKTO,
                c.KOSTL,
                c.AUFNR,
                d.KTEXT,
                e.NAME1 AS d_proveedor,
                f.TXT50,
                g.FRGCO
                from sapabap1.ekko a
                inner join sapabap1.ekpo b on a.ebeln = b.ebeln 
                left JOIN SAPABAP1.EKKN c ON c.EBELN = a.ebeln and b.ebelp = c.ebelp                             
                left JOIN SAPABAP1.AUFK d ON c.AUFNR = d.AUFNR
                left JOIN SAPABAP1.T16FW g ON g.FRGGR = a.FRGGR
                LEFT JOIN SAPABAP1.LFA1 e ON e.LIFNR = a.LIFNR                
                left JOIN SAPABAP1.SKAT f ON f.SAKNR = c.SAKTO
                where 
                a.BSART in ('ZCL','ZFC') and
                a.FRGKE= '3' and
                a.EKORG= '9000' and g.FRGCO in ('GA', 'NG','HR') AND g.FRGGR in ('09','NG','HR')
                {$sq} and b.LOEKZ not in ('L','S') order by BEDAT DESC";
//echo $oci_qry;
        $nop='';
$result = odbc_exec($prd, $oci_qry);
$total = $valorOC = $CC = 0;
while ($row = odbc_fetch_object($result)){
    
    $pedidos[$row->EBELN]['EBELN'] = ''.$row->EBELN;
    $pedidos[$row->EBELN]['BSART'] = ''.$row->BSART;
    $pedidos[$row->EBELN]['FRGKE'] = ''.$row->FRGKE;
    $pedidos[$row->EBELN]['EKORG'] = ''.$row->EKORG;
    $pedidos[$row->EBELN]['ERNAM'] = ''.$row->ERNAM;
    $pedidos[$row->EBELN]['BEDAT'] = ''.date('d-m-Y', strtotime($row->BEDAT));
    $pedidos[$row->EBELN]['LIFNR'] = ''.$row->LIFNR;
    $pedidos[$row->EBELN]['WAERS'] = ''.$row->WAERS;
    $pedidos[$row->EBELN]['WERKS'] = ''.$row->WERKS;
    $pedidos[$row->EBELN]['LGORT'] = ''.$row->LGORT;
    $pedidos[$row->EBELN]['D_PROVEEDOR'] = ''.$row->D_PROVEEDOR;
    
    $detped[$row->EBELN][$CC]['EBELN'] = ''.$row->EBELN;
    $detped[$row->EBELN][$CC]['BSART'] = ''.$row->BSART;
    $detped[$row->EBELN][$CC]['FRGKE'] = ''.$row->FRGKE;
    $detped[$row->EBELN][$CC]['EKORG'] = ''.$row->EKORG;
    $detped[$row->EBELN][$CC]['ERNAM'] = ''.$row->ERNAM;
    $detped[$row->EBELN][$CC]['BEDAT'] = ''.$row->BEDAT;
    $detped[$row->EBELN][$CC]['LIFNR'] = ''.$row->LIFNR;
    $detped[$row->EBELN][$CC]['WAERS'] = ''.$row->WAERS;
    $detped[$row->EBELN][$CC]['TXZ01'] = ''.$row->TXZ01;
    $detped[$row->EBELN][$CC]['EBELP'] = ''.$row->EBELP;
    $detped[$row->EBELN][$CC]['WERKS'] = ''.$row->WERKS;
    $detped[$row->EBELN][$CC]['LGORT'] = ''.$row->LGORT;
    $detped[$row->EBELN][$CC]['MENGE'] = ''.(float)$row->MENGE;
    $detped[$row->EBELN][$CC]['MWSKZ'] = ''.$row->MWSKZ;    
    
    if($row->MWSKZ == 'P0'){
        if($row->WAERS == 'PYG'){                
            $detped[$row->EBELN][$CC]['NETPR']=((($row->NETPR*100) / $row->PEINH));
            
            $detped[$row->EBELN][$CC]['NETPRALL']=round((((($row->NETPR*100) / $row->PEINH))))*$row->MENGE;
            $valorOC+=round((((($row->NETPR*100) / $row->PEINH))))*$row->MENGE;
            
        }else{
            $detped[$row->EBELN][$CC]['NETPR']=($row->NETPR / $row->PEINH);
            $detped[$row->EBELN][$CC]['NETPRALL']=round(($row->NETPR / $row->PEINH))*$row->MENGE;
            $valorOC+=(($row->NETPR / $row->PEINH))*$row->MENGE;
        }
    }else if($row->MWSKZ == 'P3'){
        if($row->WAERS == 'PYG'){                
            $detped[$row->EBELN][$CC]['NETPR']=((($row->NETPR*100) / $row->PEINH)*1.0476);
            $detped[$row->EBELN][$CC]['NETPRALL']=round((((($row->NETPR*100) / $row->PEINH)*1.0476)))*$row->MENGE;
            $valorOC+=round((((($row->NETPR*100) / $row->PEINH)*1.0476)))*$row->MENGE;
        }else{
            $detped[$row->EBELN][$CC]['NETPR']=($row->NETPR / $row->PEINH)*1.0476;
            $detped[$row->EBELN][$CC]['NETPRALL']=round(($row->NETPR / $row->PEINH)*1.0476)*$row->MENGE;
            $valorOC+=(($row->NETPR / $row->PEINH)*1.0476)*$row->MENGE;
        }
    }else{
        if($row->WAERS == 'PYG'){                
            $detped[$row->EBELN][$CC]['NETPR']= ((($row->NETPR*100) / $row->PEINH)*1.1);
            $detped[$row->EBELN][$CC]['NETPRALL']=round((((($row->NETPR*100) / $row->PEINH)*1.1)))*$row->MENGE;
            $valorOC+=round((((($row->NETPR*100) / $row->PEINH)*1.1)))*$row->MENGE;
        }else{
            $detped[$row->EBELN][$CC]['NETPR']=($row->NETPR / $row->PEINH)*1.1;
            $detped[$row->EBELN][$CC]['NETPRALL']=round(($row->NETPR / $row->PEINH)*1.1)*$row->MENGE;
            $valorOC+=(($row->NETPR / $row->PEINH)*1.1)*$row->MENGE;
        }
    }
    $detped[$row->EBELN][$CC]['NETPR']= number_format($detped[$row->EBELN][$CC]['NETPR'],2,",",".");
    $detped[$row->EBELN][$CC]['NETPRALL']= number_format($detped[$row->EBELN][$CC]['NETPRALL'],2,",",".");
    $pedidos[$row->EBELN]['VALOR'] = number_format($valorOC,2,",",".");
//    $pedidos[$row->EBELN]['VALOR'] = ''.number_format($valorOC,2,",",".");
    $detped[$row->EBELN][$CC]['SAKTO'] = ''.$row->SAKTO;
    $detped[$row->EBELN][$CC]['KOSTL'] = ''.$row->KOSTL;
    $detped[$row->EBELN][$CC]['KTEXT'] = ''.$row->KTEXT;
    $detped[$row->EBELN][$CC]['TXT50'] = ''.$row->TXT50;    
    
    $CC++;
}
$total = count($pedidos);
$data['total'] = $total;
//var_dump($data_of);
echo json_encode(
        array(
            "total" => $data, 
            "pedidos" => $pedidos,
            "detalle" => $detped
        ));
