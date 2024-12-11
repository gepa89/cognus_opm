<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$user = $_POST['user'];
$pedido = strtoupper($_POST['pedido']);
$ean = strtoupper($_POST['material']);


 if(isset($pedido)){
    
    $sq = "SELECT a.artrefer, b.artean, b.artdesc, a.canpedi, a.canpendi, a.canprepa, b.artser , b.artser 
            FROM pedredet a 
            left join arti b on a.artrefer = b.artrefer  
            where pedrefer = '{$pedido}' ";
//            echo $sq;
    $rs = $db->query($sq);
    $flg = 0;
    while($ax = $rs->fetch_assoc()){        
        $sqhan = "SELECT DISTINCT EKPO.MATNR, MSEG.MBLNR, SER03.OBKNR, OBJK.SERNR, OBJK.EQUNR, EQUI.BAUJJ, EQUI.BAUMM
                    FROM SAPABAP1.MSEG, SAPABAP1.SER03, SAPABAP1.OBJK, SAPABAP1.EQUI, SAPABAP1.EKPO

                    WHERE MSEG.EBELN='{$pedido}'
                    and EKPO.MATNR='{$ax['artrefer']}'
                    and MSEG.SHKZG='S'
                    AND MSEG.MBLNR=SER03.MBLNR
                    AND SER03.SHKZG = 'S'
                    AND SER03.OBKNR=OBJK.OBKNR
                    AND OBJK.SERNR=EQUI.SERNR
                    AND EKPO.MATNR=EQUI.MATNR";
//                echo $sqhan;
        $rst = odbc_exec($prd, $sqhan);
        $sernro = '';
        while ($rw = odbc_fetch_object($rst)){
                $sernro = $rw->SERNR;
                $mes = $rw->BAUJJ;
                $anio = $rw->BAUMM;
                $inSer = "insert into serati set 
                    artrefer = '{$ax['artrefer']}',
                    artserie = '{$sernro}',
                    artanio = '{$anio}',
                    artmes = '{$mes}'
                ";

                $db->query($inSer); 
            }
            
    }
//    Obtengo todos los pedidos de recepcion asignados a la terminal
    $sq = "SELECT a.artrefer, b.artean, b.artdesc, a.canpedi, a.canpendi, a.canprepa, b.artser, d.artserie as artnroserie
            FROM pedredet a 
            left join arti b on a.artrefer = b.artrefer  
            left join artean c on a.artrefer = c.artrefer  
            left join serati d on a.artrefer = d.artrefer and a.pedrefer = d.artped
            where pedrefer = '{$pedido}' and (c.ean = '{$ean}' or d.artserie = '{$ean}') and a.canprepa <= a.canpedi limit 1";
//            echo $sq;
    $rs = $db->query($sq);
    $flg = 0;
    while($ax = $rs->fetch_assoc()){
        if($ax['canprepa'] == $ax['canpedi']){
            $response["error"] = TRUE;
            $response['mensaje'] = "Material ya controlado.";
            $flg = 1;
            continue;
        }else{
            $ean = "select ean from artean where artrefer = '{$ax['artrefer']}'";
            $rsean = $db->query($ean);
            $ea = '';
            while($axx = $rsean->fetch_assoc()){
                if($ea != ''){
                    $ea .= "-".$axx['ean']."";
                }else{
                    $ea = "".$axx['ean']."";
                }
            }

            $ax['artean'] = $ea;
            $response['materiales'][] = $ax;
            
        }
    }
    if(count($response['materiales']) > 0){        
        $response["error"] = FALSE;
        $response['mensaje'] = "";
    }else if($flg == 0){
        $response["error"] = TRUE;
        $response['mensaje'] = "Material no corresponde.";
    }
    echo json_encode($response);
}

