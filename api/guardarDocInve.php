<?php
session_start();
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../api/helpers/helper_guardar_inve_cab.php");


//$db->autocommit(false);

guardar_info_log("guardar pedidos manual", json_encode($_POST));
$usuario = $_SESSION['user'];
if (!$usuario) {
    exit;
}
$cliente = htmlspecialchars(trim($_POST['cliente']));
$documento = htmlspecialchars(trim($_POST['documento']));
//$clase_documento = htmlspecialchars(trim($_POST['clase_documento']));
$moneda = htmlspecialchars(trim($_POST['moneda']));
$cod_alma = htmlspecialchars(trim($_POST['cod_almacen']));
$tipo = htmlspecialchars(trim($_POST['tipo']));
$mic = htmlspecialchars(trim($_POST['mic']));
$crt = htmlspecialchars(trim($_POST['crt']));
$factura = htmlspecialchars(trim($_POST['factura']));
$id_pedido = null;

$detalles = $_POST['detalles'];
guardar_info_log("guardar pedidos manual", $detalles);

$db = new mysqli($SERVER, $USER, $PASS, $DB);

$id_pedido = guardar_inve_cab($db, false, $cod_alma, $tipo, $id_pedido, $detalles, $usuario);
$db->close();
header('Content-type: application/json; charset=utf-8');
echo json_encode(array("results" => true, "id_pedido" => $id_pedido));
exit;
