<?php
require_once(__DIR__ . "/../conect.php");
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/respuesta.php");
require_once(__DIR__ . "/../api/helpers/helper_guardar_pedido_in_cab.php");
//$db->autocommit(false);

guardar_info_log("guardar pedidos manual", json_encode($_POST));
$cliente = htmlspecialchars(trim($_POST['cliente']));
$documento = htmlspecialchars(trim($_POST['documento']));
//$clase_documento = htmlspecialchars(trim($_POST['clase_documento']));
$moneda = htmlspecialchars(trim($_POST['moneda']));
$cod_alma = htmlspecialchars(trim($_POST['cod_almacen']));
$id_pedido = htmlspecialchars(trim($_POST['id_pedido']));
$actualizar = !empty($id_pedido);
$cod_alma_salida = htmlspecialchars(trim($_POST['cod_almacen_salida']));

$detalles = $_POST['detalles'];
guardar_info_log("guardar pedidos manual", $detalles);

$db = new mysqli($SERVER, $USER, $PASS, $DB);

try {
    $hoy = date('Y-m-d H:i:s');

    $db->begin_transaction();
    $sql = "";
    if ($actualizar) {
        /*$sql = "UPDATE pedidosecab SET 
        clirefer = '{$cliente}', 
        clasdoc = '{$documento}', 
        codmone='{$moneda}',
        cod_alma='{$cod_alma}' 
        WHERE docompra=$id_pedido";*/
    } else {
        $sql = "INSERT INTO pedidosecab SET 
        clirefer = '{$cliente}', 
        clasdoc = '{$documento}', 
        codmone='{$moneda}',
        cod_alma='{$cod_alma}',
        alma_emi='{$cod_alma_salida}',
        fecre='{$hoy}';";
    }


    $query = $db->query($sql);

    if (!$query) {
        guardar_error_log("egreso documento", $db->error);
        $db->rollback();
        $db->close();
        retorna_resultado(422, ['error' => $db->error]);
    }

    if (!$actualizar) {
        $id_pedido = (int) mysqli_insert_id($db);
    }
    guardar_info_log("pedidp", $id_pedido);
    $sql = "SELECT * from clientes where clirefer = '{$cliente}'";
    $nombre_cliente = $db->query($sql)->fetch_assoc()['clinom'];

    $movref = "V02";
    if ($documento === "ZUB") {
        $movref = "T02";
        guardar_pedidos_in_cab($db, false, $cliente, $documento, $moneda, $cod_alma, null, $detalles);

    }

    $sql = "INSERT into pedexcab set 
    pedexref = '{$id_pedido}',
    pedexentre = '{$id_pedido}', 
    clirefer = '{$cliente}',
    pedclase = '{$documento}',
    almrefer = '{$cod_alma_salida}',
    pedexfec = '$hoy',
    siturefe = 'PD',
    ensal = 'out',
    movref = '$movref'
    ";

    /*if ($actualizar) {
        $sql = "UPDATE pedrecab set 
        pedclase = '{$documento}', 
        almrefer = '{$cod_alma}',
        nomprove = '{$nombre_cliente}',
        codprove = '{$cliente}' 
        WHERE pedrefer = '{$id_pedido}'";
    }

    */
    $query = $db->query($sql);
    if (!$query) {
        guardar_error_log("egreso documento", $sql);
        guardar_error_log("error egreso documento", $db->error);
        $db->rollback();
        $db->close();
        retorna_resultado(422, ['error' => $db->error]);
    }
    $indice = 1;
    /*if ($actualizar) {
        $sql = "DELETE FROM pedidosindet WHERE docompra = '{$id_pedido}'";
        $db->query($sql);
        $sql = "DELETE FROM pedredet WHERE pedrefer='{$id_pedido}'";
        $db->query($sql);
    }*/
    foreach ($detalles as $detalle) {

        $posicion = $indice * 10;
        $codigo_articulo = $detalle[0];
        $descripcion_articulo = $detalle[1];
        $cantidad = $detalle[2];
        $unidad_medida = $detalle[3];
        $cajas_pallet = $detalle[4];
        $precio_unitario = $detalle[5];
        $precio_total = $detalle[6];
        $volumen = $detalle[7];
        $peso_neto = $detalle[8];
        $peso_bruto = $detalle[9];
        $id_dimension = $detalle[10];

        if (empty($codigo_articulo) || !is_numeric($cantidad) || !is_numeric($precio_unitario)) {
            continue;
        }

        $sql = "INSERT INTO pedidosedet SET 
        docompra = '{$id_pedido}',
        posnr = '{$posicion}',
        artrefer='{$codigo_articulo}',
        artdesc='{$descripcion_articulo}',
        canti='{$cantidad}',
        preuni='{$precio_unitario}',
        pretotal='{$precio_total}',
        unimed='{$unidad_medida}',
        volumen='{$volumen}',
        caj_pallet='{$cajas_pallet}',    
        pesneto='{$peso_neto}',
        pesbruto='{$peso_bruto}',    
        cod_dim='{$id_dimension}'";
        $query = $db->query($sql);

        if (!$query) {
            guardar_error_log("egreso documento", $db->error);
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }
        /*$sql = "UPDATE arti SET costo = '{$precio_unitario}', unimed = '{$unidad_medida}'
        WHERE artrefer = '$codigo_articulo'";
        $query = $db->query($sql);

        if (!$query) {
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }*/


        $sql = "INSERT INTO pedexdet SET 
        pedpos = '{$posicion}', 
        artrefer='{$codigo_articulo}',
        unimed='{$unidad_medida}',
        canpedi='{$cantidad}',
        pedexref='{$id_pedido}',
        pedexentre='{$id_pedido}'";
        $query = $db->query($sql);
        if (!$query) {
            guardar_error_log("egreso documento", $db->error);
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }

        $indice++;
    }



    $db->commit();


} catch (\Throwable $th) {
    guardar_error_log("egreso documento", $th->getMessage());

    $db->rollback();
    print_r($th->getMessage());
}
$db->close();
header('Content-type: application/json; charset=utf-8');
echo json_encode(array("results" => true, "id_pedido" => $id_pedido));
exit;
?>