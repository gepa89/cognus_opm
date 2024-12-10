<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$cod_articulo = $db->real_escape_string($_GET['cod_articulo']);
$tipo = $db->real_escape_string($_GET['tipo']);

guardar_info_log("obtener articulo", json_encode($_GET));

/*$sql = "SELECT artrefer, artdesc, unimed, costo FROM arti WHERE artrefer='$cod_articulo' LIMIT 1";*/
$filtro_tipo_ubicacion = "";

if ($tipo != "todos") {
    $filtro_tipo_ubicacion = " AND c.ubitipo = '$tipo'";
}
$sql = "SELECT b.artrefer, 
b.artdesc, 
sum(a.canti) as total,
a.ubirefer,
c.ubitipo
from stockubi a 
inner join arti b on a.artrefer = b.artrefer 
inner join ubimapa c on c.ubirefer = a.ubirefer and c.cod_alma = a.cod_alma  
where a.artrefer ='$cod_articulo' $filtro_tipo_ubicacion
group by a.ubirefer, a.artrefer
HAVING sum(a.canti) <> 0";

$query = $db->query($sql);
if (!$query) {
    guardar_error_log("obtener documentos", $db->error);
    $db->close();
    header('Content-type: application/json; charset=utf-8', true, 400);
    echo json_encode(array("mensaje" => "Ocurrio un error"));
    exit;
}
$respuesta = array("estado" => "exito", "mensaje" => "", "dato" => null);

if ($query->num_rows == 0) {
    $db->close();
    $respuesta['estado'] = "alerta";
    $respuesta['mensaje'] = "No se encontro articulo";
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($respuesta);
    exit;
}

$respuesta['dato'] = $query->fetch_all(MYSQLI_ASSOC);
$db->close();
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit;
