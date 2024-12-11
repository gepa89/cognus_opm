<?php

require ('../conect.php');
require_once(__DIR__."/../utils/conversores.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$typ = $_POST['id'];
$art = $_POST['art'];
$pedrefer = $_POST['pedrefer'];
$codalma = $_POST['codalma'];

$response = array();
$título = $lk = '';
$header ="";
switch ($typ){
    case 'mdPp1'://
        $título ="Datos Picking";
        $header = '<tr><th>Artículo</th><th>Descripción</th><th>Mínimo</th><th>Máximo</th><th>Ubicación</th><th>Tipo</th><th>Zona</th><th>Estado Ubi.</th><th>Situación Ubi.</th><th>Acción</th></tr>';
        $sq = "SELECT arti.artrefer,
            arti.artdesc,
            artirepo.artminrep,
            artirepo.artmaxrep,
            
            artubiref.ubirefer,
            artubiref.ubitipo,
            ubimapa.zoncodpre,
            ubimapa.ubiestad,
            ubimapa.ubisitu
            FROM arti
            LEFT JOIN artubiref on arti.artrefer=artubiref.artrefer AND artubiref.cod_alma='{$codalma}'
            LEFT JOIN artirepo on arti.artrefer=artirepo.artrefer and artirepo.cod_alma='{$codalma}'
            
            LEFT JOIN ubimapa on artubiref.ubirefer=ubimapa.ubirefer and artubiref.cod_alma=ubimapa.cod_alma and ubimapa.cod_alma='{$codalma}'
            WHERE arti.artrefer='{$art}'";
            $lk = '<a title="Modifiación de Ubicación" target="_blank" href="p_ref_ubic_upd.php?mat='."".$art."".'"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>';
//                    echo $sq;
        break;
    case 'mdPp2'://
        $título ="Presentaciones";
        $header = '<tr><th>Artículo</th><th>Descripción</th><th>Cantidad</th><th>Cód.Presentación</th></tr>';
        $sq = "select arti.artrefer,arti.artdesc,artipresen.canpresen,artipresen.preseref
                    from arti,artipresen 
                    where arti.artrefer=artipresen.artrefer
                    and artipresen.artrefer='{$art}' and artipresen.cod_alma ='{$codalma}'";
        break;
    case 'mdPp3'://
        $título ="Stock Acumulado";
        $header = '';
        $sq = "";
        break;
    case 'mdPp4'://
        $título ="Movimientos";
        $header = '';
        $sq = "";
        break;
    case 'mdPp5'://
        $título = "Datos EAN";
        $header = '<tr><td>Artículo</td><td>Descripción</td><td>EAN</td></tr>';
        $sq = "select arti.artrefer,arti.artdesc,artean.ean
                from arti,artean
                WHERE arti.artrefer=artean.artrefer
                and arti.artrefer='{$art}'";
        break;
    case 'mdPp6'://
        $título ="Nros. De Serie";
        $header = '<tr><td>Artículo</td><td>Descripción</td><td>Serie</td><td>Año</td><td>Mes</td></tr>';
        $sq = "select arti.artrefer,arti.artdesc,serati.artserie,serati.artanio,serati.artmes
                    from arti,serati
                    WHERE arti.artrefer=serati.artrefer
                    #and arti.almcod=stockubi.almcod
                    and serati.serprep='0'
                    and arti.artrefer='{$art}'";
        break;
    case 'mdPp7'://
        $título ="Stock p/ Ubi";
        $header = '<tr><td>Ubicación</td><td>Artículo</td><td>Descripción</td><td>Cantidad</td><td>Presentación</td><td>Tipo</td></tr>';
        $sq = "SELECT stockubi.ubirefer,
                arti.artrefer,
                arti.artdesc,
                sum(stockubi.canti) as canti,
                '-' AS cantidad_presentacion,
                artipresen.preseref,
                ubimapa.ubitipo
		FROM artubiref
		INNER JOIN arti on artubiref.artrefer=arti.artrefer
                INNER JOIN stockubi on artubiref.artrefer=stockubi.artrefer and artubiref.ubirefer=stockubi.ubirefer and artubiref.cod_alma=stockubi.cod_alma
		INNER JOIN ubimapa on stockubi.ubirefer=ubimapa.ubirefer and stockubi.cod_alma=ubimapa.cod_alma
		LEFT JOIN artipresen on stockubi.artrefer=artipresen.artrefer and artipresen.preseref='UNI'
		WHERE arti.artrefer='{$art}'
		and stockubi.cod_alma='{$codalma}'     
		and ubimapa.ubitipo in ('PI','PS','ME','MR') GROUP BY stockubi.ubirefer
    
        UNION		

        SELECT stockubi.ubirefer,
        arti.artrefer,
        arti.artdesc,
        sum(stockubi.canti) as canti,
        (select canpresen from artipresen WHERE artrefer=arti.artrefer AND artipresen.preseref='CJ') as cantidad_presentacion,
        artipresen.preseref,
        ubimapa.ubitipo
		FROM artubiref
		INNER JOIN arti on artubiref.artrefer=arti.artrefer
                INNER JOIN stockubi on artubiref.artrefer=stockubi.artrefer and artubiref.ubirefer=stockubi.ubirefer and artubiref.cod_alma=    stockubi.cod_alma
		INNER JOIN ubimapa on stockubi.ubirefer=ubimapa.ubirefer and stockubi.cod_alma=ubimapa.cod_alma
		LEFT JOIN artipresen on stockubi.artrefer=artipresen.artrefer and artipresen.preseref='CJ'
		WHERE arti.artrefer='{$art}'
		and stockubi.cod_alma='{$codalma}'  
		and ubimapa.ubitipo ='RE' GROUP BY stockubi.ubirefer
		";
        
//                echo $sq;
        break;
    case 'mdPp8'://
        $título ="Expedición";
        $header = '<tr><td>Pedido</td><td>Estado</td><td>Cliente</td><td>Artículo</td><td>Descripción</td><td>Cantidad</td></tr>';
        $sq = "SELECT pedexcab.pedexentre,pedexcab.siturefe,pedexcab.clirefer,pedexdet.artrefer,arti.artdesc,pedexdet.canpedi
                from pedexcab,pedexdet,arti
                WHERE pedexcab.pedexentre=pedexdet.pedexentre
                AND arti.artrefer=pedexdet.artrefer
                and pedexcab.siturefe='PD' AND pedexdet.artrefer = '{$art}' AND pedexcab.almrefer = '{$codalma}'";
//                echo $sq;
        break;
    case 'mdpen'://
        $título ="Pendientes";
        $header = '<tr><td>Pedido</td><td>Posicion</td><td>Articulo</td><td>Des.Articulo</td><td>Ubicacion</td></tr>';
        $sq = "SELECT pedrecab.pedrefer,pedredet.pedpos,pedredet.artrefer,arti.artdesc,artubiref.ubirefer
                from pedrecab
                INNER JOIN pedredet on pedrecab.pedrefer=pedredet.pedrefer
                INNER JOIN arti on pedredet.artrefer=arti.artrefer
                LEFT JOIN artubiref on pedredet.artrefer=artubiref.artrefer and artubiref.cod_alma='DC01' AND artubiref.ubitipo='PI'

                WHERE pedrecab.pedrefer='{$pedrefer} 'AND artubiref.ubirefer is NULL
                GROUP BY artrefer";
//                echo $sq;
        break;
    case 'mdPp9'://
        $título ="Recepción";
        $header = '<tr><td>Pedido</td><td>Estado</td><td>Proveedor</td><td>Alm. Emisor</td><td>Posición</td><td>Artículo</td><td>Descripción</td><td>Cantidad</td></tr>';
        $sq = "select pedrecab.pedrefer,pedrecab.pedresitu,pedrecab.codprove,pedrecab.pedalemi,pedredet.pedpos,pedredet.artrefer,arti.artdesc,pedredet.canpedi
                from pedrecab,pedredet,arti
                where pedrecab.pedrefer=pedredet.pedrefer
                and arti.artrefer=pedredet.artrefer#el artiuclo es el que se puso en el filtro
                and pedresitu='PD' and pedredet.artrefer = '{$art}' and pedrecab.almrefer = '{$codalma}'";
        break;
}

$rs = $db->query($sq);
$cc = 0;
while($ax = $rs->fetch_assoc()){
    if (is_numeric($ax['cantidad_presentacion'])) {
        $ax['canti']  = $ax['canti'] /  $ax['cantidad_presentacion'];
        $ax['preseref'] = 'CJ';
    }
    $ax['canti'] = formatear_numero($ax['canti']);
    unset($ax['cantidad_presentacion']);
    $response[$cc] = $ax;
    $response[$cc]['actio'] = $lk;
    $cc++;
}
if(count($response) > 0){
    $msg = '';
    $err = FALSE;
}else{
    $msg = 'Sin datos para mostrar.';
    $err = TRUE;
}
echo json_encode(array( 'cab' => $response, 'tit' => $título, 'err' => $err, 'msg' => $msg, 'hdr' => $header));

exit();
