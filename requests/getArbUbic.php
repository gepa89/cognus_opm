<?php

require('../conect.php');
require_once("../utils/conversores.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);



$ubica = $_POST['ubica'];
$codalma = $_POST['codalma'];


$sq = "select * from (
SELECT
	a.ubirefer,
	a.artrefer,
        c.artlotemar,
        a.cod_alma,
	b.preseref,
	b.canpresen,
	c.artdesc,
	sum( a.canti ) AS stock 
FROM
	stockubi a
	LEFT JOIN artipresen b ON a.artrefer = b.artrefer AND a.cod_alma=b.cod_alma
	inner join arti c on a.artrefer = c.artrefer
WHERE
	a.ubirefer = '{$ubica}'
        and a.cod_alma = '{$codalma}'
        
GROUP BY
        a.ubirefer,
	a.artrefer,
        a.cod_alma,
        b.preseref,
	b.canpresen
) x where x.stock >= 0";
$rs = $db->query($sq);
$cc = 0;
while ($row = $rs->fetch_assoc()) {
        $row['stock_formateado'] = formatear_numero($row['stock']);
        $data[$row['artrefer']] = $row;
        $cc++;
}

$tipoTrans = array();
$sq = "select movref, movdes, ensal, descsen from artmov where pedclase = 'AJS'";

//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while ($row = $rs->fetch_assoc()) {
        $tipoTrans[$row['movref']]['movref'] = $row['movref'];
        $tipoTrans[$row['movref']]['movdesc'] = $row['movdes'];
        $tipoTrans[$row['movref']]['ensal'] = $row['ensal'];
        $tipoTrans[$row['movref']]['descsen'] = $row['descsen'];
        $cc++;
}
$motAjs = array();
$sq = "select ajuref, ajudes, ajumov from ajutip";

//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while ($row = $rs->fetch_assoc()) {
        $motAjs[$row['ajuref']]['ajuref'] = $row['ajuref'];
        $motAjs[$row['ajuref']]['ajudes'] = $row['ajudes'];
        $motAjs[$row['ajuref']]['ajumov'] = $row['ajumov'];
        $cc++;
}
echo json_encode(array('dat' => $data, 'tmov' => $tipoTrans, 'mmov' => $motAjs));

exit();
