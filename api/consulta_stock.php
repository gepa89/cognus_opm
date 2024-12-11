<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
header('Content-type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
//$db->begin_transaction();
$texto_busqueda = $_GET['texto'];

$sql = "SELECT a.artrefer, 
       b.artdesc, 
       a.ubirefer, 
       a.cod_alma, 
       SUM(a.canti) AS cantidad
        FROM stockubi a
        INNER JOIN arti b ON b.artrefer = a.artrefer
        INNER JOIN ubimapa c ON c.ubirefer = a.ubirefer AND c.cod_alma = a.cod_alma
        WHERE a.artrefer = '$texto_busqueda' OR a.artrefer = (SELECT artrefer FROM artean WHERE artrefer = '$texto_busqueda' OR ean = '$texto_busqueda' limit 1)
        GROUP BY a.artrefer, b.artdesc, a.ubirefer, a.cod_alma
        UNION ALL
        SELECT a.artrefer,b.artdesc,a.ubirefer,a.cod_alma,SUM(canti) AS cantidad
        FROM stockubi a
        INNER JOIN arti b on b.artrefer=a.artrefer
        INNER JOIN ubimapa c on c.ubirefer=a.ubirefer and c.cod_alma=a.cod_alma
        WHERE a.ubirefer='$texto_busqueda'
        GROUP BY a.artrefer, b.artdesc, a.ubirefer, a.cod_alma
        UNION ALL
        SELECT a.artrefer,b.artdesc,a.ubirefer,a.cod_alma,SUM(canti) AS cantidad
        FROM stockubi a
        INNER JOIN arti b on b.artrefer=a.artrefer
        INNER JOIN ubimapa c on c.ubirefer=a.ubirefer and c.cod_alma=a.cod_alma
        WHERE a.etnum='$texto_busqueda'
        GROUP BY a.artrefer, b.artdesc, a.ubirefer, a.cod_alma";
$query = $db->query($sql);
$datos = $query->fetch_all(MYSQLI_ASSOC);
echo json_encode(['datos' => $datos]);
