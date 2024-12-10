<?php $shell = true;
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../hanaDB.php");
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->autocommit(false);
$db->begin_transaction();
$sq = "select pedrefer from pedrecab";
$rs = $db->query($sq);
$ldd = array();
while ($ax = $rs->fetch_assoc()) {
    $ldd[] = $ax['pedrefer'];
}
//ekko.bedat = '20210412' and
//TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
$centro = 'LCC1';
$aml = 'LDAL';

$sqhan = "
select distinct
            ekko.ebeln, 
            ekko.bsart, 
            mseg.budat_mkpf as BEDAT, 
            ekko.lifnr,
            ekpo.matnr, 
            ekpo.reslo, 
            ekpo.ebelp, 
            ekpo.lgort, 
            mseg.menge,
            ekpo.meins,
            ekpo.werks,
            ekko.RESWK,
            mseg.cputm_mkpf as eruhr,
            mseg.CPUDT_MKPF as erdat,
            nast.erdat AS fecha_modificacion,
            nast.eruhr AS hora_modificacion,
            t001l.lgobe as name1,
            ROW_NUMBER() OVER (PARTITION BY ekko.ebeln, ekko.bsart, ekko.lifnr, ekpo.matnr, ekpo.reslo, ekpo.ebelp ORDER BY nast.erdat DESC,nast.eruhr DESC) AS RowNum
        FROM sapabap1.EKKO
            left join sapabap1.EKPO on ekko.ebeln=ekpo.ebeln 
            left join sapabap1.NAST on ekko.ebeln=nast.objky 
            left join sapabap1.LFA1 on ekko.lifnr=lfa1.lifnr
            INNER JOIN SAPABAP1.MSEG on ekko.ebeln=mseg.ebeln and ekpo.ebeln=mseg.ebeln and ekpo.matnr=mseg.matnr and ekpo.ebelp=mseg.ebelp and mseg.bwart='351' and mseg.xauto='X'
            left join sapabap1.t001l on ekpo.reslo=t001l.lgort and mseg.umwrk=t001l.werks
        where 
            nast.erdat  = TO_CHAR( CURRENT_DATE, 'YYYYMMDD')
            AND ekpo.loekz not in ('L','S')
            and ekko.bsart in ('ZUB','EUB')
            and ekpo.werks = '{$centro}'
            and ekpo.lgort = '{$aml}'
            
 union
 select distinct
            ekko.ebeln, 
            ekko.bsart, 
            ekko.BEDAT, 
            ekko.lifnr,
            ekpo.matnr, 
            ekpo.reslo, 
            ekpo.ebelp, 
            ekpo.lgort, 
            ekpo.menge,
            ekpo.meins,
            ekpo.werks,
            ekko.RESWK,
            nast.eruhr,
            nast.erdat,
            nast.erdat AS fecha_modificacion,
            nast.eruhr AS hora_modificacion,
            lfa1.name1 as name1,
            ROW_NUMBER() OVER (PARTITION BY ekko.ebeln, ekko.bsart, ekko.lifnr, ekpo.matnr, ekpo.reslo, ekpo.ebelp ORDER BY nast.erdat DESC,nast.eruhr DESC) AS RowNum
        FROM sapabap1.EKKO
            left join sapabap1.EKPO on ekko.ebeln=ekpo.ebeln 
            left join sapabap1.NAST on ekko.ebeln=nast.objky 
            left join sapabap1.mseg on ekko.ebeln = mseg.ebeln
            left join sapabap1.LFA1 on ekko.lifnr=lfa1.lifnr
        where nast.erdat  = TO_CHAR( CURRENT_DATE, 'YYYYMMDD')
            AND ekpo.loekz not in ('L','S')
            and ekko.bsart in ('ZCL','ZNB','ZID')
            and ekpo.werks = '{$centro}'
            and ekpo.lgort = '{$aml}'";
$sqhan = "SELECT * FROM ($sqhan) AS temp
            WHERE temp.RowNum=1";
$rst = odbc_exec($prd, $sqhan);
$t_pend = $cc = 0;
while ($rw = odbc_fetch_object($rst)) {
    $cod = str_pad($rw->EBELN, 10, '0', STR_PAD_LEFT);
    //    echo $cod;

    if (!in_array($cod, $ldd)) {
        $data[$cod]['cab']['pedrefer'] = $cod;
        $data[$cod]['cab']['pedclase'] = $rw->BSART;
        $data[$cod]['cab']['pedrefec'] = date("Ymd", strtotime($rw->ERDAT));
        $data[$cod]['cab']['pedrehor'] = date("His", strtotime($rw->ERUHR));
        $data[$cod]['cab']['fecmod'] = date("Ymd", strtotime($rw->FECHA_MODIFICACION));
        $data[$cod]['cab']['hormod'] = date("His", strtotime($rw->HORA_MODIFICACION));
        $data[$cod]['cab']['nomprove'] = $rw->NAME1;
        $data[$cod]['cab']['pedalemi'] = $rw->RESLO;
        $data[$cod]['cab']['almrefer'] = $rw->LGORT;

        switch ($rw->BSART) {
            case 'EUB':
                $data[$cod]['cab']['codprove'] = $rw->RESWK;
                break;
            case 'ZCL':
                $data[$cod]['cab']['codprove'] = $rw->LIFNR;
                break;
            case 'ZUB':
                $data[$cod]['cab']['codprove'] = $rw->RESWK;
                break;
            case 'ZNB':
                $data[$cod]['cab']['codprove'] = $rw->LIFNR;
                break;
            case 'ZID':
                $data[$cod]['cab']['codprove'] = $rw->LIFNR;
                break;
        }
        $data[$cod]['det'][$cc]['pedpos'] = $rw->EBELP;
        $data[$cod]['det'][$cc]['artrefer'] = $rw->MATNR;
        $data[$cod]['det'][$cc]['unimed'] = $rw->MEINS;
        $data[$cod]['det'][$cc]['canpedi'] = $rw->MENGE;
        $cc++;
    }
}
foreach ($data as $cod => $dat) { 
    print_r($data);   //    insertar cabecera
    //    echo "<br/>________<br/>";
    $sqMov = "select movref from artmov where ensal = 'in' and pedclase like '%{$dat['cab']['pedclase']}%' limit 1";
    //    echo $sqMov;
    $rmov = $db->query($sqMov);
    $dtMov = $rmov->fetch_assoc();
    if ($dtMov['movref'] != '') {
        $ref = $dtMov['movref'];
    }

    $sqx = "insert into pedrecab set 
        pedrefer = '{$cod}',
        pedclase = '{$dat['cab']['pedclase']}', 
        pedresitu = 'PD', 
        pedrefec = '{$dat['cab']['pedrefec']}',
        pedrehor = '{$dat['cab']['pedrehor']}',
        pedalemi = '{$dat['cab']['pedalemi']}',
        almrefer = '{$dat['cab']['almrefer']}',
        pedrefeclle = now(),
        pedrehorlle = now(),
        fecmod='{$dat['cab']['fecmod']}',
        hormod='{$dat['cab']['hormod']}',
        nomprove = '{$dat['cab']['nomprove']}',
        ensal = 'in',
        movref = '{$ref}',
        codprove = '{$dat['cab']['codprove']}'";
    //        echo "<br/>________<br/>";
    if ($db->query($sqx)) {
        $sqd = "insert into pedredet (pedpos,artrefer,unimed,canpedi,pedrefer,idpadre,tienekit) values ";
        $values = '';
        foreach ($dat['det'] as $ind => $dt) {
            $tieneKit = 'NO';
            $kits = obtenerKIT($db, $dt['artrefer']);
            if (!empty($kits)) {
                $tieneKit = 'SI';
                $info = [];
                foreach ($kits as $articulo) {
                    $articulo = (object) $articulo;
                    $datos = [
                        $articulo->poskit,
                        $articulo->artrefer,
                        $articulo->unimed,
                        (int)$dt['canpedi'],
                        $cod,
                        $dt['artrefer'],
                        'NO'
                    ];
                    $resultado = implode("','", $datos);
                    $resultado = "('$resultado')";
                    $separador = $values == '' ? "" : ",";
                    $values .= $separador . $resultado;
                }
            }
            if ($values == '') {
                $values = "(" . (int)$dt['pedpos'] . ",'" . $dt['artrefer'] . "','" . $dt['unimed'] . "'," . $dt['canpedi'] . ",'" . $cod . "',null,'$tieneKit')";
            } else {
                $values .= ",(" . (int)$dt['pedpos'] . ",'" . $dt['artrefer'] . "','" . $dt['unimed'] . "'," . $dt['canpedi'] . ",'" . $cod . "',null,'$tieneKit')";
            }

            //            verifico si existe en arti
            $ck = "select * from arti where artrefer = '{$dt['artrefer']}'";
            $rx = $db->query($ck);
            if ($rx->num_rows == 0) {
                $sqhan = "select 
                            marc.matnr, 
                            mara.MATKL, 
                            mara.MEINS, 
                            mara.prdha, 
                            makt.MAKTX,
                            mara.ean11, 
                            MARC.SERNP
                        from sapabap1.marc
                            inner join sapabap1.mara on marc.matnr = mara.matnr
                            inner join sapabap1.makt on marc.matnr = makt.matnr and makt.spras='S'
                        where 
                            marc.werks='{$centro}'
                            and marc.matnr = '{$dt['artrefer']}'";
                //                echo $sqhan;
                $rst = odbc_exec($prd, $sqhan);
                while ($rw = odbc_fetch_object($rst)) {
                    $artser = 0;
                    if ($rw->SERNP == 'ZCHA') {
                        //                        SE CARGA NRO DE SERIE
                        $artser = 1;
                    }
                    //                    SE CARGA ARTI
                    $insAt = "insert into arti set 
                        artrefer = '{$rw->MATNR}',
                        artdesc = '{$rw->MAKTX}',
                        unimed = '{$rw->MEINS}',
                        artgrup = '{$rw->MATKL}',
                        artjerar = '{$rw->PRDHA}',
                        artean = '{$rw->EAN11}',
                        artser = '{$rw->SERNP}'";
                    $db->query($insAt);
                }
            }
        }
        $db->query($sqd . $values);

    }
    //    
}
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
$db->commit();
exit;
function obtenerKIT($db, $cod_articulo_padre)
{
    $sql = "SELECT arti.*, data.poskit FROM (SELECT artkit.* FROM arti LEFT JOIN artkit ON arti.artrefer = artkit.artrefer 
            WHERE arti.artrefer = '$cod_articulo_padre' AND arti.artmarkit = 'SI') AS data INNER JOIN arti on data.artrefkit = arti.artrefer";
    return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}