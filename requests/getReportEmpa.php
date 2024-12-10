<?php

require('../conect.php');

//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
    
if (isset($_POST["desPed"]) && $_POST["desPed"] != '') {
    $pd = str_pad(trim($_POST['desPed']), 10, '0', STR_PAD_LEFT);
    $desPed = $pd;
    $fl = 1;
} else {
    $desPed = "";
}

//var_dum$flp($fl);
//echo $dsAr;
$cabeceras = $detalles = array();
if ($fl == 1) {
    $sq = "
select
	*
from
	(
	SELECT
		pedexdet.pedpos,
		pedexdet.artrefer,
		pedexdet.artdesc,
		pedexdet.canpedi,
		pedexdet.canprepa,
		pedexdet.canpendi,
		pedexdet.usuario,
                pedexdet.fechcie,
		pedexdet.horcie,
		pedexdet.pedexentre
	FROM
		pedexdet
	join (
		SELECT
			pedexcab.pedexentre,
			clientes.clirefer
			
		FROM
			ped_multiref,
			pedexcab,
			pedexdet,
			clientes
		WHERE
			ped_multiref.pedido = pedexcab.pedexentre
			AND ped_multiref.pedido = pedexdet.pedexentre
			AND pedexcab.clirefer = clientes.clirefer
			AND ped_multiref.multiref = '{$desPed}'
			AND ped_multiref.codst = '1'
			AND pedexcab.siturefe in ('CE','PP')
		ORDER BY
			ped_multiref.mrts DESC
		LIMIT 1) as dt
	WHERE
		dt.pedexentre = pedexdet.pedexentre) as resultado
inner join pedexcab on pedexcab.pedexentre = resultado.pedexentre
INNER JOIN clientes on pedexcab.clirefer = clientes.clirefer GROUP BY artrefer";

    $rs = $db->query($sq);

    $materiales = array();
    while ($row = $rs->fetch_assoc()) {
        if ($materiales[$row['pedexentre']] == '') {
            $materiales[$row['pedexentre']] = "'" . $row['artrefer'] . "'";
        } else {
            $materiales[$row['pedexentre']] .= ",'" . $row['artrefer'] . "'";
        }
    }

    //    echo $materiales;
    

    //    var_dump($zonas);
    $rs = $db->query($sq);
    $lk = '';

    $detalles = array();
    while ($row = $rs->fetch_assoc()) {
        $cabeceras[$row['pedexentre']]['pedexref'] = '' . $row['pedexref'];
        $cabeceras[$row['pedexentre']]['pedexentre'] = '' . $row['pedexentre'];
        $cabeceras[$row['pedexentre']]['clirefer'] = '' . $row['clirefer'];
        $cabeceras[$row['pedexentre']]['clinom'] = '' . $row['clinom'];
        $cabeceras[$row['pedexentre']]['pedclase'] = '' . $row['pedclase'];
        $cabeceras[$row['pedexentre']]['pedexfec'] = '' . $row['pedexfec'];
        $cabeceras[$row['pedexentre']]['pedexhor'] = '' . $row['pedexhor'];
        $cabeceras[$row['pedexentre']]['almrefer'] = '' . $row['almrefer'];
        $cabeceras[$row['pedexentre']]['siturefe'] = '' . $row['siturefe'];
       
        //HASTA ACA ES LO QUE ESTABA//

        $auxDat = array();
        $auxDat["pedexref"] = $row["pedexref"];
        $auxDat["pedexfec"] = $row["pedexfec"];
        $auxDat["pedexhor"] = $row["pedexhor"];
        $auxDat["codenv"] = $row["codenv"];
        $auxDat["pedpos"] = $row["pedpos"];
        $auxDat["artcodi"] = $row["artcodi"];
        $auxDat["artrefer"] = $row["artrefer"];
        $auxDat["artdesc"] = htmlspecialchars(utf8_decode($row["artdesc"]));
        $auxDat["unimed"] = $row["unimed"];
        $auxDat["canpedi"] = $row["canpedi"];
        $auxDat["canpendi"] = $row["canpendi"];
        $auxDat["canprepa"] = $row["canprepa"];
        $auxDat["almrefer"] = $row["almrefer"];
        $auxDat["cencod"] = $row["cencod"];
        $auxDat["expst"] = $row["expst"];
        $auxDat["fechcie"] = $row["fechcie"];
        $auxDat["horcie"] = $row["horcie"];
        $auxDat["usuario"] = $row["usuario"];
        $auxDat["movref"] = $row["movref"];
        $auxDat["siturefe"] = $row["siturefe"];
        //$detalles[$row['pedexentre']] = [$auxDat];
        if (!isset($detalles[$row["pedexref"]])) {
            $detalles[$row["pedexref"]] = array();
        }
        array_push($detalles[$row["pedexref"]], $auxDat);
       
    }


    


    foreach ($cabeceras as $ped => $dat) {


        $assigns = '';
        //        asignados
        if ($dat['pedresitu'] <> 'AN') {
            if ($dat['pedclase'] <> 'REPO') {
                if ($dat['tercod'] != '') {


                    $assigns = explode(',', $dat['tercod']);


                } else {
                    $assigns = '';
                }

                $lk = '';
                if (!is_array($assigns)) {
                    // var_dump($zonas[$ped]);

                    // exit();

                    foreach ($zonas[$ped] as $k => $v) {
                        $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);"onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $dat['almrefer'] . "','EXPE'" . ')" >Asignar Z-' . $v . '</a><br/>';
                    }
                } else {


                    foreach ($assigns as $k => $v) {
                        $sq = "select terzonpre from termi where tercod = '" . $v . "' and almrefer = '" . $dat['almrefer'] . "'";
                        $rx = $db->query($sq);

                        // echo '<pre>';var_dump($sq );echo '</pre>';
                        //   exit();

                        while ($ax = $rx->fetch_assoc()) {
                            $zAsignada[] = $ax['terzonpre'];
                            $tAsignada[$ax['terzonpre']] = $v;
                        }
                    }




                    foreach ($zonas[$ped] as $k => $v) {
                        if (!in_array($v, $zAsignada)) {
                            $lk .= '<a class="btn btn-primary btn-fnt-size" href="javascript:void(0);" onclick="asReception(' . "'" . $ped . "','" . $v . "','" . $auxDat["almrefer"] . "','EXPE'" . ')" >Asignar Z-' . $v . '</a><br/>';
                        } else {
                            $lk .= $tAsignada[$v] . "<br/>";
                        }

                    }


                }


            } else {
                $SQ = "select * from assig_ped where pedido = '{$ped}'";
                //            echo $SQ;
                $rk = $db->query($SQ);
                $rxs = $rk->fetch_assoc();
                //            var_dump($rxs);
                if ($rk->num_rows > 0) {
                    //                $dt = $rxs->fetch_assoc();
                    $lk = "" . $rxs['tercod'];
                } else {
                    $ped = trim($ped);
                    $cod_almacen = strtoupper(trim($dat['almrefer']));
                    $lk = "<a class=\"btn btn-primary btn-fnt-size\" href=\"javascript:void(0);\" onclick=\"asReceptionRe('$ped','REPO','{$cod_almacen}')\" >Asignar REPO</a><br/>";
                }
                //            var_dump($lk);

            }
        } else {
            $lk = "";
        }

        $cabeceras[$ped]['pedaction'] = $lk;
    }
}



//
//var_dump($detalles);
echo json_encode(array('cab' => $cabeceras, 'det' => $detalles));

exit();