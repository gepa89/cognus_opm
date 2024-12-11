<?php $shell = true;
// phpinfo();
//ini_set('memory_limit', '-1');
//date_default_timezone_set('America/Asuncion');
// var_dump($_SER)
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../hanaDB.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$rs = $db->query($sq);

$centro = 'CH00';
$aml = 'DC01';
$sqhana = " SELECT mara.laeda, mara.matnr, mara.prdha, mara.ean11, mara.matkl, mara.mstae, mara.meins, 
            marc.SERNP, 
            MAKT.MAKTX,
			ZMMCLASE_VAL.BWTAR 
            FROM SAPABAP1.MARA, SAPABAP1.MARC, SAPABAP1.MAKT, SAPABAP1.ZMMCLASE_VAL
            WHERE MARC.WERKS = '{$centro}'
            AND MARA.LAEDA =  TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            AND MARA.MATNR = MARC.MATNR
            AND MARA.MATNR = MAKT.MATNR
            AND MARA.MATNR = ZMMCLASE_VAL.MATNR
            AND ZMMCLASE_VAL.WERKS = '{$centro}'
            AND MAKT.SPRAS = 'S' ";
//   echo $sqhana;

$dt = odbc_exec($prd, $sqhana);
//var_dump($dt);
//echo 'entro';
$t_pend = $cc = 0;
//var_dump($dt);
while ($row = odbc_fetch_array($dt)) {
    print_r($row['MATNR'] . "\n");
    $articulo = obtener_articulo($db, $row["MATNR"]);
    if ($articulo) {
        $fecha = new DateTime($articulo->fecaut);
        $hoy = new DateTime(date("Y-m-d"));
        if ($fecha < $hoy) {
            actualizarArticulos($db, $row, $aml, $prd);
        }
    } else {
        insertarArticulo($db, $prd, $centro, $aml, $row);
    }
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
$scriptname = basename(__FILE__, '.php');
$sq = "insert into scheduled_jobs_ck set script = '{$scriptname}', last = now()";
$rs = $db->query($sq);
$db->commit();
$db->close();

function obtener_articulo($db, $artrefer)
{
    $sq = "SELECT * 
       from arti WHERE artrefer = '$artrefer'";
    $res = $db->query($sq);
    return $res->fetch_object();
}

function actualizarArticulos($db, $row, $aml, $prd)
{
    print_r("actualizando \n");

    $sq = "update arti set            
                artdesc = '" . $db->real_escape_string($row['MAKTX']) . "',
                unimed = '{$row['MAINS']}',
                artgrup = '{$row['MATKL']}',
                artrot = '{$row['MSTAE']}',
                artean = '{$row['EAN11']}',
                artjerar = '{$row['PRDHA']}',
                artval = '{$row['BWTAR']}',
                almcod = '{$aml}',
                fecaut = now()
                where 
                artrefer = '" . ($row["MATNR"]) . "'
                ";
    //                echo $sq;
    $cod = $row["MATNR"];
    if ($db->query($sq)) {
        print_r("actualizado articulo \n");
        //$sqhanX = "select cod_barra, cod_articulo from ST_BARRAS where cod_articulo = '{$row["COD_ARTICULO"]}'";
        //
        //        

        $sqhanX = "SELECT MATNR, EAN11
                        FROM SAPABAP1.MEAN
                        WHERE MATNR =  '{$cod}'";
        $dtean = odbc_exec($prd, $sqhanX);
        if (!$dtean) {
            print_r("error");
        }
        //oci_execute($dtean);
        $sql = "DELETE FROM artean WHERE artrefer='$cod'";
        $query = $db->query($sql);
        if (!$query) {
            print_r("error");
        }
        while ($rowean = odbc_fetch_object($dtean)) {
            /*$sq = "replace into artean set                 
            ean = '" . ($rowean["EAN11"]) . "'
            where 
            artrefer = '{$rowean["MATNR"]}'
            ";
            $query = $db->query($sq);
            if (!$query) {
                print_r($db->error);
            }else{
                print_r("acualizado artean \n");
            }*/
            $ean = $rowean->EAN11;
            $material = $rowean->MATNR;
            $sql = "INSERT INTO artean
                (ean, artrefer,fecaut)
                VALUES( '$ean', '$material',now())";
            $query = $db->query($sql);
            if (!$query) {
                print_r("error");
            }
        }

    } else {
        print_r("error");
    }
}

function insertarArticulo($db, $prd, $centro, $aml, $rw)
{
    print_r("insertando \n");
    $cod = $rw['MATNR'];
    $sq = "insert into arti set 
                artrefer = '{$cod}',
                artdesc = '" . $db->real_escape_string($rw['MAKTX']) . "',
                unimed = '{$rw->MEINS}',
                artser = '{$rw->SERNP}',
                artgrup = '{$rw->MATKL}',
                artjerar = '{$rw->PRDHA}',
                artval = '{$rw->BWTAR}',
                    almcod = '{$aml}',
                fecaut = now()
                ";
    echo $sq . "<br/>";
    if ($db->query($sq)) {
        $sqhanX = "SELECT mara.matnr,
                        mara.laeda, 
                        mean.ean11 

                        FROM SAPABAP1.MARA, SAPABAP1.MARC, SAPABAP1.Mean
                        WHERE MARC.WERKS= '" . $centro . "'

                        AND MARA.MATNR=MARC.MATNR
                        AND mara.matnr=mean.matnr
                        AND mara.matnr = '{$cod}'";
        //                echo $sqhan;
        $rstX = odbc_exec($prd, $sqhanX);

        while ($rw = odbc_fetch_object($rstX)) {
            $sq = "insert into artean set 
                artrefer = '{$cod}',
                cod_alma = '{$aml}',    
                ean = '" . ($rw->EAN11) . "',
                fecaut = now()
                ";
            $db->query($sq);
            $sq = "insert into artipresen set
                    artrefer = '{$cod}',
                    cod_alma = '{$aml}',    
                    canpresen = 1,
                    preseref = 'UNI';
                ";
            $db->query($sq);
        }
    } else {
        $sqhanX = "SELECT mara.matnr,
                        mara.laeda, 
                        mean.ean11 

                        FROM SAPABAP1.MARA, SAPABAP1.MARC, SAPABAP1.Mean
                        WHERE MARC.WERKS= '" . $centro . "'

                        AND MARA.MATNR=MARC.MATNR
                        AND mara.matnr=mean.matnr
                        AND mara.matnr = '{$cod}'";
        //                echo $sqhan;
        $rstX = odbc_exec($prd, $sqhanX);

        while ($rw = odbc_fetch_object($rstX)) {
            $sq = "insert into artean set 
                artrefer = '{$cod}',
                cod_alma = '{$aml}',    
                ean = '" . ($rw->EAN11) . "',
                fecaut = now()
                ";
            $db->query($sq);
            $sq = "insert into artipresen set
                    artrefer = '{$cod}',
                    cod_alma = '{$aml}',    
                    canpresen = 1,
                    preseref = 'UNI';
                ";
            $db->query($sq);
        }
    }
}