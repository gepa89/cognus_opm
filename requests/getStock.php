<?php

require('../conect.php');
require_once("../utils/conversores.php");
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$codigo_pedido = $_GET['pedido'];
$codalma = $_GET['codalma'];
$limite = $_GET['length'] ? $_GET['length'] : 10;
$offset = $_GET['start'] ? $_GET['start'] : 0;

$filtros = "";
if (!empty($codigo_pedido)) {
    $filtros = $filtros . " AND a.artrefer LIKE '%$codigo_pedido%' ";
}
$condicional = " FROM stockart a inner join arti b on a.artrefer = b.artrefer 
where a.cod_alma = '{$codalma}' {$filtros}";
$sql = "SELECT 
a.artrefer,
b.artdesc,
a.candispo,
a.canmure,
a.canpedven,
a.cantransfe,
a.cod_alma 
{$condicional} 
LIMIT {$limite} OFFSET {$offset}";

try {
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($resultados); $i++) {
        $resultados[$i]['candispo'] = formatear_numero($resultados[$i]['candispo']);
        $resultados[$i]['canmure'] = formatear_numero($resultados[$i]['canmure']);
        $resultados[$i]['canpedven'] = formatear_numero($resultados[$i]['canpedven']);
        $resultados[$i]['cantransfe'] = formatear_numero($resultados[$i]['cantransfe']);
    }
    $sql = "SELECT COUNT(*) AS cantidad {$condicional}";
    $cantidad = $db->query($sql)->fetch_assoc()['cantidad'];
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();

$respuesta = array(
    'data' => $resultados,
    'recordsFiltered' => (int) $cantidad,
    'recordsTotal' => (int) $cantidad
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit();
