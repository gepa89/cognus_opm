<?php

require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../api/helpers/helper_guardar_pedflete_cab.php");

//$db->autocommit(false);


guardar_info_log("guardar pedidos manual", json_encode($_POST));
$cliente = htmlspecialchars(trim($_POST['cliente']));
$proveedor = htmlspecialchars(trim($_POST['proveedor']));

$clasflete = htmlspecialchars(trim($_POST['clasflete']));
//$clase_documento = htmlspecialchars(trim($_POST['clase_documento']));
$moneda = htmlspecialchars(trim($_POST['moneda']));
$cod_alma = htmlspecialchars(trim($_POST['cod_almacen']));
$codsocie = htmlspecialchars(trim($_POST['codsocie']));
$orgcompra = htmlspecialchars(trim($_POST['orgcompra']));
$cencod = htmlspecialchars(trim($_POST['cencod']));
$puertcarga = htmlspecialchars(trim($_POST['puertcarga']));
$incote = htmlspecialchars(trim($_POST['incote']));
$factura = htmlspecialchars(trim($_POST['factura']));
$id_pedido = htmlspecialchars(trim($_POST['id_pedido'])) ?? null;

$detalles = $_POST['detalles'];
guardar_info_log("guardar pedidos manual", $detalles);

$db = new mysqli($SERVER, $USER, $PASS, $DB);

$id_pedido = guardar_pedfletes_cab($db, !empty($id_pedido), $proveedor, $clasflete, $moneda, $cod_alma, $cencod, $codsocie, $orgcompra, $puertcarga,$incote, $id_pedido, $detalles);
$db->close();
header('Content-type: application/json; charset=utf-8');
echo json_encode(array("results" => true, "id_pedido" => $id_pedido));
exit;
?>