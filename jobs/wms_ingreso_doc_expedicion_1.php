<?php $shell = true;
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../hanaDB.php");
include_once(__DIR__ . "/../../saprfc/prd.php");

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
//echo '<pre>';var_dump($_POST);echo '</pre>';

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db_semaforo = new mysqli($SERVER, $USER, $PASS, $DB);

$sql = "SELECT * FROM scheduled_jobs WHERE estado=true AND script='wms_ingreso_doc_expedicion'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error");
    exit;
}
$resultado = $res->fetch_assoc();
if ($res->num_rows > 0) {
    print_r("existe job activo");
    exit;
}

$sql = "UPDATE scheduled_jobs SET estado=true WHERE script='wms_ingreso_doc_expedicion'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error");
    exit;
}
$db_semaforo->close();
$db->begin_transaction();

$sq = "select pedexentre from pedexcab";
$rs = $db->query($sq);
$ldd = array();
while ($ax = $rs->fetch_assoc()) {
    $ldd[] = $ax['pedexentre'];
}
$centro = 'LCC1';
$aml = 'LDAL';
$sqhan = "select distinct
            likp.kunag, 
            kna1.name1,
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
            lips.POSNR,
            lips.meins,
            likp.VSTEL,
            lips.matkl,
            lips.lgpbe
            from sapabap1.likp
            left join sapabap1.lips on likp.vbeln = lips.vbeln 
            left join sapabap1.kna1 on likp.kunnr = kna1.kunnr
            left join sapabap1.vbfa on vbfa.VBELV=likp.VBELN
            
        WHERE 
        likp.erdat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            AND likp.lfart in ('ZCON','ZCOS','ZCRE','ZOBQ','ZSCP','ZGAR','ZSVC','ZMOS')
            and lips.werks = 'LCC1'
            and lips.lgort = 'LDAL'";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);

$t_pend = $cc = 0;
while ($rw = odbc_fetch_object($rst)) {
    $cod = str_pad($rw->VBELN, 10, '0', STR_PAD_LEFT);
    //    echo $cod;

    if (!in_array($cod, $ldd)) {
        $data[$cod]['cab']['clirefer'] = $rw->KUNAG;
        $data[$cod]['cab']['clinom'] = $rw->NAME1;
        $data[$cod]['cab']['pedexentre'] = $rw->VBELN;
        $data[$cod]['cab']['pedclase'] = $rw->LFART;
        $data[$cod]['cab']['pedexfec'] = date("Ymd", strtotime($rw->ERDAT));
        $data[$cod]['cab']['pedexhor'] = date("His", strtotime($rw->ERZET));
        $data[$cod]['cab']['codenv'] = $rw->VSBED;
        $data[$cod]['cab']['pedexref'] = $rw->VGBEL;
        $data[$cod]['cab']['codirec'] = $rw->LGPBE;
        $data[$cod]['cab']['almrefer'] = $rw->LGORT;
        $data[$cod]['cab']['puestoexp'] = $rw->VSTEL;
        $data[$cod]['cab']['siturefe'] = 'PD';
        $data[$cod]['cab']['obse1'] = obtenerMensajePedido($config, $rw->VGBEL);


        $data[$cod]['det'][$cc]['pedexref'] = $rw->VGBEL;
        $data[$cod]['det'][$cc]['pedexentre'] = $rw->VBELN;
        $data[$cod]['det'][$cc]['pedpos'] = $rw->POSNR;
        $data[$cod]['det'][$cc]['grupoart'] = $rw->MATKL;
        $data[$cod]['det'][$cc]['artrefer'] = $db->real_escape_string($rw->MATNR);
        $data[$cod]['det'][$cc]['artdesc'] = $db->real_escape_string(utf8_encode($rw->ARKTX));
        $data[$cod]['det'][$cc]['unimed'] = $rw->MEINS;
        $data[$cod]['det'][$cc]['canpedi'] = $rw->LFIMG;
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
            left join sapabap1.t001l on ekpo.lgort = t001l.lgort and ekpo.werks=t001l.werks
            where
            nast.kschl='NEU' 
            AND ekko.bsart in ('EUB','ZUB') 
            AND ekpo.loekz not in ('L','S')
            AND ekpo.loekz = '' 
            AND ekko.bedat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
            and ekko.reswk = 'LCC1'
            and ekpo.reslo='LDAL'";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)) {
    $cod = str_pad($rw->EBELN, 10, '0', STR_PAD_LEFT);
    //    echo $cod;
    print_r($cod);
    if (!in_array($cod, $ldd)) {
        $data[$cod]['cab']['clirefer'] = $rw->LGORT;
        $data[$cod]['cab']['pedexentre'] = $rw->EBELN;
        $data[$cod]['cab']['pedclase'] = $rw->BSART;
        $data[$cod]['cab']['pedexfec'] = date("Ymd", strtotime($rw->BEDAT));
        $data[$cod]['cab']['pedexhor'] = date("His", strtotime($rw->ERZET));
        $data[$cod]['cab']['codenv'] = '';
        $data[$cod]['cab']['pedexref'] = '';
        $data[$cod]['cab']['almrefer'] = 'LDAL';
        $data[$cod]['cab']['siturefe'] = 'PD';

        $data[$cod]['det'][$cc]['pedexref'] = '';
        $data[$cod]['det'][$cc]['pedexentre'] = $rw->EBELN;
        $data[$cod]['det'][$cc]['pedpos'] = $rw->EBELP;
        $data[$cod]['det'][$cc]['artrefer'] = $db->real_escape_string($rw->MATNR);
        $data[$cod]['det'][$cc]['artdesc'] = $db->real_escape_string(utf8_encode($rw->ARKTX));
        $data[$cod]['det'][$cc]['unimed'] = $rw->MEINS;
        $data[$cod]['det'][$cc]['canpedi'] = $rw->MENGE;
        $cc++;
    }
}

foreach ($data as $cod => $dat) {
    //    insertar cabecera
    //    echo "<br/>________<br/>";
    $ref = '';
    $sqMov = "SELECT movref from artmov where ensal = 'out' and pedclase like '%{$dat['cab']['pedclase']}%' limit 1";
    //    echo $sqMov;
    $rmov = $db->query($sqMov);
    $dtMov = $rmov->fetch_assoc();
    if ($dtMov['movref'] != '') {
        $ref = $dtMov['movref'];
    }

    $sqcLI = "INSERT into clientes set clirefer = '{$dat['cab']['clirefer']}', clinom = '{$dat['cab']['clinom']}', clidirec = '{$dat['cab']['clidir']}', clitip = 'VEN'";
    //    echo $sqMov;
    $db->query($sqcLI);
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $obs1 = $dat['cab']['obse1'] ?? "";
    $sqx = "INSERT into pedexcab set 
        clirefer = '{$dat['cab']['clirefer']}',
        pedexentre = '{$dat['cab']['pedexentre']}', 
        pedclase = '{$dat['cab']['pedclase']}',
        pedexfec = '{$dat['cab']['pedexfec']}',
        pedexhor = '{$dat['cab']['pedexhor']}', 
        codenv = '{$dat['cab']['codenv']}',
        siturefe = '{$dat['cab']['siturefe']}',            
        almrefer = '{$dat['cab']['almrefer']}',    
        codirec = '{$dat}',
        fecllegada = '{$fecha}',
        horllegada = '{$hora}',
        ensal = 'out',
        movref = '{$ref}',
        pedexref = '{$dat['cab']['pedexref']}',
        puestoexp = '{$dat['cab']['puestoexp']}',
        obse1 = '$obs1' ";
    $dividir_pedidos = verificarParametroDivisionPedidos($db, $dat['cab']['almrefer']);
    print_r("dividir pedido $dividir_pedidos");
    foreach ($dat['det'] as $detalle) {
        if (
            tienePresentacion(
                $db,
                $detalle['artrefer'],
                $detalle['canpedi']
            )
            && $dividir_pedidos
        ) {
            $sql_cabecera_cajas = "INSERT into pedexcabcajas set 
                clirefer = '{$dat['cab']['clirefer']}',
                pedexentre = '{$dat['cab']['pedexentre']}', 
                pedclase = '{$dat['cab']['pedclase']}',
                pedexfec = '{$dat['cab']['pedexfec']}',
                pedexhor = '{$dat['cab']['pedexhor']}', 
                codenv = '{$dat['cab']['codenv']}',
                siturefe = '{$dat['cab']['siturefe']}',            
                almrefer = '{$dat['cab']['almrefer']}',    
                codirec = '{$dat['cab']['codirec']}',       
                ensal = 'out',
                movref = '{$ref}',
                pedexref = '{$dat['cab']['pedexref']}',
                puestoexp = '{$dat['cab']['puestoexp']}',
                obse1 = '$obs1' ";
            $db->query($sql_cabecera_cajas);
            break;
        }
    }
    //        echo "<br/>________<br/>";
    if ($db->query($sqx)) {


        $sqd = "insert into pedexdet (pedpos,artrefer,artdesc,unimed,canpedi,pedexref,pedexentre,grupoart,idpadre,tienekit) values ";
        $values = '';
        foreach ($dat['det'] as $ind => $dt) {
            if (tienePresentacion($db, $dt['artrefer'], $dt['canpedi']) && $dividir_pedidos) {
                $sql_det_cajas = "insert into pedexdetcajas (pedpos,artrefer,artdesc,unimed,canpedi,pedexref,pedexentre,grupoart) values ";
                $valores = "(" . (int) $dt['pedpos'] . ",'" . $db->real_escape_string($dt['artrefer']) . "','" . $db->real_escape_string($dt['artdesc']) . "','" . $dt['unimed'] . "'," . $dt['canpedi'] . ",'" . $dt['pedexref'] . "','" . $dt['pedexentre'] . "','" . $dt['grupoart'] . "')";
                $res = $db->query($sql_det_cajas . $valores);
                if (!$res) {
                    print_r($sql_det_cajas . $valores);
                    print_r($db->error);
                    $db->rollback();
                    exit;
                }
            }
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
                        $articulo->artdesc,
                        $articulo->unimed,
                        $dt['canpedi'],
                        $dt['pedexref'],
                        $dt['pedexentre'],
                        $dt['grupoart'],
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
                $values = "(" . (int) $dt['pedpos'] . ",'" . $db->real_escape_string($dt['artrefer']) . "','" . $db->real_escape_string($dt['artdesc']) . "','" . $dt['unimed'] . "'," . $dt['canpedi'] . ",'" . $dt['pedexref'] . "','" . $dt['pedexentre'] . "','" . $dt['grupoart'] . "',null,'$tieneKit')";
            } else {
                $values .= ",(" . (int) $dt['pedpos'] . ",'" . $db->real_escape_string($dt['artrefer']) . "','" . $db->real_escape_string($dt['artdesc']) . "','" . $dt['unimed'] . "'," . $dt['canpedi'] . ",'" . $dt['pedexref'] . "','" . $dt['pedexentre'] . "','" . $dt['grupoart'] . "',null,'$tieneKit')";
            }
        }
        $is_ok = $db->query($sqd . $values);
        if (!$is_ok) {
            print_r($db->error);
            $db->rollback();
            exit;
        } else {
            $db->commit();
        }
    } else {
        print_r($db->error);
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
$db->close();

$db_semaforo = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "UPDATE scheduled_jobs SET estado=false WHERE script='wms_ingreso_doc_expedicion'";
$res = $db_semaforo->query($sql);
if (!$res) {
    print_r("error actualizar estado job");
    exit;
}
$db_semaforo->close();
exit;


function obtenerMensajePedido($config, $id_pedido)
{
    $con_sap = new SapConnection($config);
    try {
        //    echo $data['vgbel']." - ";

        $f = $con_sap->getFunction('ZREAD_TEXT');

        //    $f->setParameterActive('ARCHIVE_HANDLE', false);
        //    $f->setParameterActive('VCLIENT', false);
        $parametros = [
            'VID' => 'Z003',
            'VLANGUAGE' => 'S',
            'VNAME' => "$id_pedido",
            'VOBJECT' => 'VBBK'
        ];
        $result = $f->invoke($parametros);
        return $result['VLINES'][0]['TDLINE'];
    } catch (SapException $ex) {
        //    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
        //echo $msg;
    } catch (Exception $e) {
    }
    $con_sap->close();
    return "";
}

function verificarParametroDivisionPedidos($db, $cod_alma)
{
    $sql = "SELECT estaparam FROM config WHERE cod_alma = '$cod_alma' LIMIT 1";
    $query = $db->query($sql);
    $conf = $query->fetch_assoc();
    return $conf['estaparam'] === "1";
}
function tienePresentacion($db, $cod_articulo, $cantidad)
{
    $sql = "SELECT COUNT(*) AS cantidad 
            FROM artipresen 
            WHERE artrefer='$cod_articulo' AND preseref <> 'UNI' AND canpresen <= '$cantidad'";
    return $db->query($sql)->fetch_object()->cantidad > 0;
}

function obtenerKIT($db, $cod_articulo_padre)
{
    $sql = "SELECT arti.*, data.poskit FROM (SELECT artkit.* FROM arti LEFT JOIN artkit ON arti.artrefer = artkit.artrefer 
            WHERE arti.artrefer = '$cod_articulo_padre' AND arti.artmarkit = 'SI') AS data INNER JOIN arti on data.artrefkit = arti.artrefer";
    return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}
