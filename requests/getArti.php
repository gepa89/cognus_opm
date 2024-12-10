<?php

require('../conect.php');
function sanitizar($campo)
{
    return trim(htmlspecialchars(($campo)));
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$limite = $_GET['length'] ? $_GET['length'] : 10;
$offset = $_GET['start'] ? $_GET['start'] : 0;
$codigo_articulo = sanitizar($_GET['arti']);
$grupo = sanitizar($_GET['grarti']);
$serie = sanitizar($_GET['peseries']);
$cod_almacen = sanitizar($_GET['priep']);
//echo $sq;
//if(is_numeric($_GET['arti'])){
//    $arti = str_pad($_GET['arti'], 18, "0", STR_PAD_LEFT);
//}else{
//    $arti = $_GET['arti'];
//}

$filtros = "";
$arti = $_GET['arti'];
if (!empty($codigo_articulo)) {
    $filtros = $filtros . " AND artrefer LIKE '%{$codigo_articulo}%'";
}

if (!empty($grupo)) {
    $filtros = $filtros . " AND artgrup = '{$grupo}'";
}

if (!empty($serie)) {
    $filtros = $filtros . " AND artser = '{$serie}'";
}

if (!empty($cod_almacen)) {
    $filtros = $filtros . " AND almcod = '{$cod_almacen}'";
}

$condicional = " FROM arti WHERE TRUE {$filtros} ";
$sql = "SELECT
	artrefer,
    artdesc,
    unimed,
    artrot,
    almcod,
    artser,
    artgrup,
    artean,
    artjerar,
    artpeso,
    artvolum,
    artancho,
    artlargo,
    presecod,
    fecaut,
    artval,
    clirefer,
    artlotemar,
    costo 
    {$condicional}
    ORDER BY fecaut DESC 
    LIMIT $limite OFFSET $offset
    ";

try {
   
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    $sql = "SELECT COUNT(*) AS cantidad {$condicional}";
    $cantidad = $db->query($sql)->fetch_assoc()['cantidad'];
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();
$resultados = array(
    'data' => $resultados,
    'recordsFiltered' => (int) $cantidad,
    'recordsTotal' => (int) $cantidad
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($resultados);
exit();