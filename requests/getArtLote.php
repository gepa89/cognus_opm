<?php

require('../conect.php');

$db = new mysqli($SERVER, $USER, $PASS, $DB);

$articulo = htmlspecialchars(trim($_POST['articulo']));
$cod_almacen = htmlspecialchars(trim($_POST['cod_alma']));
$ubicacion = htmlspecialchars(trim($_POST['ubicacion']));

$sql = "SELECT loteart.artlote,stockubi.ubirefer , COALESCE(SUM(stockubi.canti),0) as cantidad 
FROM loteart 
LEFT OUTER JOIN stockubi ON stockubi.artlote = loteart.artlote and stockubi.ubirefer = '$ubicacion'
WHERE loteart.artrefer = '$articulo'
AND loteart.cod_alma = '$cod_almacen'
GROUP BY loteart.artlote, stockubi.ubirefer";
try {
    $resultados = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
} catch (\Throwable $th) {
    print_r($th->getMessage());
}

$db->close();

$respuesta = array(
    'datos' => $resultados
);
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit();