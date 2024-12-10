<?php require('../conect.php');
require_once(__DIR__ . '/../logger/logger.php');
guardar_info_log("cierre pedido", "iniciando...");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$terminal = strtoupper($_POST['terminal']);
$pedido = strtoupper($_POST['pedido']);
$muelle = strtoupper($_POST['muelle']);
$cod_alma = $_POST['cod_almacen'];
$response = array("error" => "", "mensaje" => "");
guardar_info_log("cierre pedido", json_encode($_POST));
$es_valido = esValido();
if (!$es_valido) {
    $db->close();
    $response['error'] = true;
    $response['mensaje'] = "faltan parametros";
    echo json_encode($response);
    exit;
}
$sq = "DELETE from pallets 
                where pal_pedido = '{$pedido}' AND pal_status=0";
$db->query($sq);
if (isset($pedido)) {
    $sqz = "select terzonpre from termi where tercod = '{$terminal}' limit 1";
    $rsz = $db->query($sqz);
    $rszz = $rsz->fetch_assoc();
    $sq2 = "select 
        a.artrefer, 
        a.canpedi, 
        b.artdesc,
        b.artser,
        (select GROUP_CONCAT(ean) as eans from artean c where c.artrefer = a.artrefer GROUP BY c.artrefer) as ean,
        d.zoncodpre
        from pedexdet a 
        inner join arti b on a.artrefer = b.artrefer
        inner join stockubi c on a.artrefer = c.artrefer
        inner join ubimapa d on c.ubirefer = d.ubirefer
        where a.pedexentre = '{$ax['pedido']}' and a.canprepa < a.canpedi";
    $rs = $db->query($sq2);
    $situ = '';
    if ($rs->num_rows > 0) {
        $situ = 'CP';
    } else {
        $situ = 'CE';
    }
    //    reviso si pallet esta vacio
    $sq = "update pedrecab set
        pedresitu = '{$situ}',
        pedrecie = now(),
        pedrehorcie = now()
        where pedrefer = '{$pedido}'";
    $flg = 0;
    if ($db->query($sq)) {
        $sqCLOSE = "update assig_ped set
        st = 1
        where pedido = '{$pedido}' AND tercod = '$terminal'";
        $db->query($sqCLOSE);
        $sqDOCK = "insert into pedmuelle set
        pedrefer = '{$pedido}',
        muelle = '{$muelle}',
        cod_alma='{$cod_alma}',
        pets = now()";
        $db->query($sqDOCK);
        /*$sqDOCKupd = "update ubimapa set
        ubisitu = 'LL'
        where ubirefer = '{$muelle}'";
        $db->query($sqDOCKupd);*/
        //        se cierran los pallets abiertos y se eliminan los vacios

        //        reviso si material del pedido existe en stockubi
//        $sqmat = "select artrefer, canprepa from pedredet where pedrefer = '{$pedido}'";

        $response["error"] = FALSE;
        $response['mensaje'] = "Pedido cerrado correctamente";
    } else if ($flg == 0) {
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo actualizar pedido.";
    }
}
//$db->autocommit(false);
$db->commit();
$db->close();
echo json_encode($response);
exit;
function esValido()
{
    $respuesta = true;
    if (!isset($_POST['terminal']) || empty($_POST['terminal'])) {
        guardar_error_log("cierre pedido", "terminal no seteada");
        $respuesta = false;
    }
    if (!isset($_POST['pedido']) || empty($_POST['pedido'])) {
        guardar_error_log("cierre pedido", "pedido no seteado");
        $respuesta = false;
    }
    if (!isset($_POST['cod_almacen']) || empty($_POST['cod_almacen'])) {
        guardar_error_log("cierre pedido", "almacen no seteado");
        $respuesta = false;
    }
    if (!$respuesta) {
        guardar_error_log("cierre pedido datos", json_encode($_POST));
    }
    return $respuesta;
}