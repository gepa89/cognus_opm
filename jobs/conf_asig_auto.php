<?php $shell = true;
//require ('/var/www/html/hanaDB.php'); 
require('/var/www/html/wmsd/conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo "aqui";

date_default_timezone_set("America/Asuncion");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
#$db->begin_transaction();
//$db->autocommit(FALSE);
$sq = "select asig, ruta from config limit 1";
//echo $sq;
$rs = $db->query($sq);
$cfg = $rs->fetch_assoc();
$fecha = date("Y-m-d");
$hora = date("H:i:s");
//echo $cfg;
// El sistema no asigna la cantidad que dice su capactidad porque encontro un pedido que no tiene la zona de picking
// eso tambien hacer que sea todo lento despues
$flag = 1;
if ($cfg['asig'] == 1) {
    while ($flag == 1) {
        $stermi = "SELECT
            x.tercod,
            x.tipac,
            (x.canped - x.canasig) AS libre,
            x.almrefer,
            x.terzonpre 
            FROM
                (
                    SELECT
                        a.tercod,
                        b.canped,
                        b.tipac,
                        b.terzonpre,
                        b.almrefer,
                        (
                            SELECT
                                COUNT(c.tercod) AS total
                            FROM
                                assig_ped c
                            WHERE
                                c.tercod = a.tercod
                                AND st = 0
                        ) AS canasig
                    FROM
                        termi_assign a
                        INNER JOIN termi b ON a.tercod = b.tercod
                    WHERE
                        b.tipac IN ('EXPE', 'REPO')
                ) AS x
            WHERE
                (x.canped - x.canasig) > 0";
        //    echo $stermi."<br/>";
        $rter = $db->query($stermi);
        if (!$rter) {
            imprimir("error");
        }
        $response = $rter->num_rows;

        if ($response > 0) {
            while ($ax = $rter->fetch_assoc()) {
                $assigned = asigPedTerm($ax['tercod'], $cfg['ruta'], $ax['tipac'], $ax['almrefer'], $ax['terzonpre'], $db);
                if ($assigned) {
                    continue;
                } else {
                    $flag = 0;
                }
            }
        } else {
            $flag = 0;
        }
    }
}
#$db->commit();
$db->close();

function imprimir($dato)
{
    print_r("$dato \n");
}
function obtenerCapacidadTerminal($db, $terminal)
{
    $stermi2 = "select x.tercod, x.tipac, (x.canped - x.canasig) as libre,x.almrefer  
    from (select a.tercod, b.canped, b.tipac, b.almrefer, 
    (select COUNT(c.tercod) as total from assig_ped c where c.tercod = a.tercod and st = 0) as canasig 
    from termi_assign a inner join termi b on a.tercod = b.tercod where b.tipac in ('EXPE','REPO')) as x 
    where x.tercod = '{$terminal}' AND  (x.canped - x.canasig) > 0";
    //   echo $stermi2."<br/>";
    $rter2 = $db->query($stermi2);
    return $rter2;
}

function asigPedTerm($terminal, $ruta, $tipo, $almrefer, $zona, $db)
{
    guardar_info_log("asignar terminal", "en curso");
    //cantidad asignada a la terminal
    switch ($ruta) {
        case 'A1': //POR HORA
            $getPed = "select * from pedexcab a left join assig_ped b on a.pedexentre = b.pedido where b.st is null ORDER BY pedexfec, pedexhor asc limit 1";
            $rs = $db->query($getPed);
            $pedido = $rs->fetch_assoc();
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            if (isset($pedido['pedexentre'])) {
                $puedoAsignar = validarStockPedido($pedido['pedexentre'], $db, $almrefer);
                if ($puedoAsignar) {
                    $si = "insert into assig_ped set pedido = '{$pedido['pedexentre']}', fecasig = '{$fecha}', horasig = '{$hora}', tercod = '{$terminal}', st = 0, cod_alma='$almrefer'";
                    if ($db->query($si)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
            break;
        case 'A2': //POR FRANJA
            imprimir("por franja");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            // obtengo orden de prioridad de asígnación
            $time = date("H:i:s", strtotime("now"));
            $day = date("N", strtotime("now"));
            $ClassD = '';
            if ($tipo == 'REPO') {
                $ClassD = " and clas_doc in ('MAQUI','EXPEMAQUI','REPO')";
            }
            $sq_ord = "SELECT * FROM prioridades 
            where true {$ClassD} and (dia_des <= {$day} 
            and dia_has >= {$day}) and (hor_des <= '{$time}' 
            and hor_has >= '{$time}' and cod_alma = '{$almrefer}') 
            order by ord_pri asc";
            $rs_ord = $db->query($sq_ord);

            $sql_cod_envios = "SELECT * FROM prioridades 
            where cod_env is not null and (dia_des <= {$day} 
            and dia_has >= {$day}) and (hor_des <= '{$time}' 
            and hor_has >= '{$time}' and cod_alma = '{$almrefer}') 
            order by ord_pri asc";
            $query = $db->query($sql_cod_envios);

            $clases_documentos = array();
            $codigos_envio = array();
            while ($fila = $query->fetch_object()) {
                $pre_clase_documentos = explode(",", $fila->clas_doc);
                foreach ($pre_clase_documentos as $clase) {
                    $aux = "\"$clase\"";
                    if (!in_array($aux, $clases_documentos)) {
                        $clases_documentos[] = "\"$clase\"";
                    }
                }

                $codigos_envio[] = "\"$fila->cod_env\"";
            }
            $filtro_cod_envio = " AND a.codenv IN (" . implode(",", $codigos_envio) . ")";
            $filtro_clase_documento = " AND a.pedclase IN (" . implode(",", $clases_documentos) . ")";
            while ($aux_ord = $rs_ord->fetch_assoc()) {
                //imprimir(json_encode($aux_ord));
                $prioridad[] = $aux_ord;
            }
            foreach ($prioridad as $k => $val) {
                $cod = $val["cod_env"];
                if ($tipo == 'EXPE' && empty($cod)) {
                    continue;
                }
                if ($tipo == 'REPO' && $prioridad[$k]['clas_doc'] == "EXPEMAQUI") {
                    $tiene_presentacion = '1';
                    $getPed = "select
                    Distinct pedexentre,
                    almrefer
                    from
                        (
                        select
                            *,
                            (
                            select
                                count(*)
                            from
                                pedexdetcajas
                            inner join stockubi on
                                pedexdetcajas.artrefer = stockubi.artrefer
                            inner join ubimapa on
                                ubimapa.ubirefer = stockubi.ubirefer
                            where
                                pedexdetcajas.pedexentre = a.pedexentre
                                and stockubi.cod_alma = a.almrefer
                                and ubimapa.ubitipo in ('RE','PS')
                                and ubimapa.zoncodalm = '$zona') as cantidad
                        from
                            pedexcabcajas a
                        left join assig_ped b on
                            a.pedexentre = b.pedido
                        where
                            a.siturefe in('PD', 'PP')
                            {$filtro_cod_envio}{$filtro_clase_documento}
                        ORDER BY
                            codenv,
                            pedexfec,
                            pedexhor asc) as pedexcabcajas
                    where
                        cantidad > 0";
                } elseif ($tipo == 'REPO' && $prioridad[$k]['clas_doc'] == "REPO") {
                    $tiene_presentacion = 'null';
                    $getPed = "select
                    Distinct pedexentre, almrefer
                        from
                            (
                            select
                                *,
                                (
                                select
                                    count(*)
                                from
                                    pedexdet
                                inner join artubiref on
                                    pedexdet.artrefer = artubiref.artrefer
                                inner join ubimapa on
                                    ubimapa.ubirefer = artubiref.ubirefer
                                where
                                    pedexdet.pedexentre = a.pedexentre
                                    and artubiref.cod_alma = a.almrefer
                                    and ubimapa.ubitipo = 'PI'
                                    and ubimapa.zoncodpre = '$zona'
                                                        ) as cantidad
                            from
                                pedexcab a
                            left join assig_ped b on
                                a.pedexentre = b.pedido
                            where
                                a.siturefe in('PD') 
                                AND a.pedclase = 'REPO'
                                and b.id is null
                            ORDER BY
                                codenv,
                                pedexfec,
                                pedexhor asc) as pedexcab
                        where
                            cantidad > 0";
                } elseif ($tipo == 'REPO' && $prioridad[$k]['clas_doc'] == "MAQUI") {
                    $tiene_presentacion = 'null';
                    $getPed = "SELECT
                                    pedubicab.pedubicod as pedexentre,
                                    pedubicab.cod_alma as almrefer
                                FROM
                                    pedubicab
                                LEFT join assig_ped on
                                    assig_ped.pedido = pedubicab.pedubicod
                                WHERE
                                    pedubicab.pedclase = 'UB'
                                    and pedubicab.situped IN ('PD', 'PP')
                                    and pedubicab.cod_alma = '$almrefer'
                                    and assig_ped.id is null
                                order by
                                    pedrefer ASC";
                } else {
                    $tiene_presentacion = 'null';
                    $hoy = date('Y-m-d');
                    $fecha = date("Y-m-d");
                    $hora = date("H:i:s");
                    $getPed = "SELECT
                                *
                            from
                                (
                                SELECT
                                    Distinct pedexentre AS pedexentreCalc,
                                    almrefer,
                                    ord_pri,
                                    pedexfec,
                                    (
                                    select
                                        count(*)
                                    from
                                        pedexdet
                                        inner join pedexcab on pedexdet.pedexentre=pedexcab.pedexentre
                                    where
                                        pedexdet.grupoart = 'CU'
                                        and pedexcab.codenv not in ('08','09')
                                        and pedexdet.pedexentre = pedexentreCalc) as cant_cu
                                from
                                    (
                                    select
                                        a.*,
                                        prioridades.ord_pri,
                                        (
                                        select
                                            count(*)
                                        from
                                            pedexdet
                                        inner join artubiref on
                                            pedexdet.artrefer = artubiref.artrefer
                                        inner join ubimapa on
                                            ubimapa.ubirefer = artubiref.ubirefer
                                        where
                                            pedexdet.pedexentre = a.pedexentre
                                            and artubiref.cod_alma = a.almrefer
                                            and ubimapa.ubitipo = 'PI'
                                            and artubiref.cod_alma = '$almrefer' 
                                        and ubimapa.zoncodpre = '$zona'
                                        ) as cantidad
                                    from
                                        pedexcab a
                                    left join assig_ped b on
                                        a.pedexentre = b.pedido
                                    inner join prioridades on
                                        prioridades.cod_env = a.codenv 
                                    where
                                        b.pedido is null 
                                        and a.siturefe in('PD') {$filtro_cod_envio} {$filtro_clase_documento} ) as pedexcab
                                where
                                    cantidad > 0
                                    order by
                                    pedexfec ASC, ord_pri ASC) as resultados
                                WHERE
                                    resultados.cant_cu = 0 ";
                    /*$getPed = "SELECT
                            Distinct pedexentre,
                            almrefer,
                            ord_pri
                            from
                                (
                                select
                                    a.*,
                                    prioridades.ord_pri,
                                    (
                                    select
                                        count(*)
                                    from
                                        pedexdet
                                    inner join artubiref on
                                        pedexdet.artrefer = artubiref.artrefer
                                    inner join ubimapa on
                                        ubimapa.ubirefer = artubiref.ubirefer
                                    where
                                        pedexdet.pedexentre = a.pedexentre
                                        and artubiref.cod_alma = a.almrefer
                                        and ubimapa.ubitipo = 'PI'
                                        and artubiref.cod_alma = '$almrefer' 
                                        and ubimapa.zoncodpre = '$zona'
                                    ) as cantidad
                                from
                                    pedexcab a
                                left join assig_ped b on
                                    a.pedexentre = b.pedido
                                inner join prioridades on prioridades.cod_env=a.codenv 
                                where
                                    a.siturefe in('PD', 'PP')
                                    {$filtro_cod_envio} {$filtro_clase_documento}
                                ) as pedexcab
                            where
                                cantidad > 0
                            order by ord_pri ASC";*/
                }
                $rs = $db->query($getPed);
                if ($rs->num_rows > 0) {
                    print_r("verificando asignacion \n");
                    //   $pedido = $rs->fetch_assoc();
                    while ($pedido = $rs->fetch_assoc()) {
                        print_r($pedido);
                        if ($entroasig == 1) {
                            //    return false; 
                            continue;
                        } else {
                            //    return true; 

                            // verificar la capacidad del terminal 
                            $query_capacidad = obtenerCapacidadTerminal($db, $terminal);
                            if (!$query_capacidad) {
                                imprimir("error");
                            }
                            $capacidad_terminal = $query_capacidad->num_rows;

                            if ($capacidad_terminal > 0) {
                                $ax = $query_capacidad->fetch_assoc();
                                if ($ax['libre'] == 0) {
                                    continue;
                                } else {
                                    //guardar_info_log("clase documento", $tipo);
                                    $puedoAsignar = true;
                                    if ($tipo != 'REPO') {
                                        imprimir("distinto");
                                        $puedoAsignar = validarStockPedido($pedido['pedexentreCalc'], $db, $almrefer);
                                    } else {
                                        $pedido['pedexentreCalc'] = $pedido['pedexentre'];
                                    }
                                    if ($puedoAsignar) {
                                        if (isset($pedido['pedexentreCalc'])) {
                                            if ($pedido['almrefer'] == $almrefer) {
                                                $si = "insert into assig_ped set pedido = '{$pedido['pedexentreCalc']}', tercod = '{$terminal}', fecasig = '{$fecha}', horasig = '{$hora}',zona='$zona',pedcajas=$tiene_presentacion , st = 0, cod_alma = '{$almrefer}'";
                                                guardar_error_log(__FILE__, $si);

                                                if ($db->query($si)) {

                                                    $entroasig++;


                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            } else {
                                                print_r("distinto almacen");
                                            }
                                        } else {
                                            // return false;
                                        }
                                    } else {
                                        imprimir("no puede asignar " . $pedido['pedexentreCalc']);
                                        // return false;
                                    }
                                }
                                // }                

                            }


                            // fin while pedi
                        }
                    }
                } else {
                    print_r("sin pedidos \n");
                    //sleep(2);
                }
            }
            /*if ($tipo == "REPO") {
                guardar_info_log(__FILE__, "asignar repo cajas");
                guardar_info_log("cod_env", $cod_env);
                guardar_info_log("cls_qery", $cls_qry);
                asignarPedidosConPresentacion($db, $cod_env, $cls_qry, $terminal, $almrefer, $zona);
            }*/



            break;
        case 'A3': //POR ZONA
            break;
    }
}

function verificar_asignacion($db, $pedido, $terminal, $codalma)
{
    guardar_info_log("asignar pedido", "verificar asignacion");
    $sql = "SELECT clas_doc FROM prioridades WHERE cod_alma='{$codalma}' AND cod_env IS NULL ORDER BY ord_pri ASC";
    $query = $db->query($sql);
    $resultado = $query->fetch_assoc();
    guardar_info_log("tipo documento", $resultado['clas_doc']);

    $sql = "SELECT COUNT(*) AS cantidad FROM pedmuelle WHERE pedstatus=0 AND cod_alma='{$codalma}'";
    $query = $db->query($sql);
    $resultado = $query->fetch_assoc();
    guardar_info_log("asignar pedido", json_encode($resultado));
    // trabajo maquinista pendiente, no asigna nuevo recurso
    if ($resultado['cantidad'] > 0) {
        guardar_info_log("asignar pedido", "No puede asignar");
        return false;
    }
    //asignar terminal por que no hay reposicion y trabajo maquinista
    return true;
}
function validarStockPedido($pedido, $db, $almrefer)
{
    imprimir("validar stock pedido " . $pedido);
    $sq = "select 
        a.artrefer,
        sum(b.canti) as canti,
        b.ubirefer,
        c.ubitipo
        from pedexdet a
        inner join stockubi b on a.artrefer = b.artrefer and  b.cod_alma = '{$almrefer}'
        inner join ubimapa c on b.ubirefer = c.ubirefer and c.ubitipo = 'PI'
                INNER JOIN artubiref d  on c.ubirefer = d.ubirefer and c.cod_alma = d.cod_alma
        where 
        a.pedexentre = '{$pedido}' 
        and a.tienekit <> 'SI'
        GROUP BY a.artrefer, b.ubirefer, b.cod_alma

       ";
    $rs = $db->query($sq);
    if ($rs->num_rows > 0) {
        $dato = $rs->fetch_assoc();
        return (int) $dato['canti'] > 0;
    }
    return false;
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
