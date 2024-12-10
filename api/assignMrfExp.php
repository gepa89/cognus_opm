<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");
guardar_custom_log(__FILE__, $_POST);

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();

//crear hash del estante
$mref = strtoupper(trim($_POST['mref']));
$mexp = strtoupper(trim($_POST['mexp']));
$term = strtoupper($_POST['term']);
$existen_rutas_faltantes = existen_rutas_disponibles($term);

$response = array();
$sq = "select ubitipo, cod_alma from ubimapa where ubirefer = '{$mexp}' limit 1";
$rx = $db->query($sq);
$mx = $rx->fetch_assoc();
if ($mx['ubitipo'] == 'ME') {
    $sq3 = "select pedido,cod_alma from ped_multiref where multiref = '{$mref}' and codst = 0 ";
    $rs3 = $db->query($sq3);

    $rs3x = $rs3->fetch_assoc();
    if ($rs3->num_rows > 0) {
        $cod_alma = $rs3x['cod_alma'];

        $sql = "SELECT * from pedexdetcajas inner join capaubi on capaubi.artrefer=pedexdetcajas.artrefer where pedexentre = ? limit 1";
        $sqlbuilder = new MySqlQuery($db);
        $fila = $sqlbuilder->rawQuery($sql, [$rs3x['pedido']])->getOne();
        $tipo_almacenaje = $fila != null ? "PS" : "";
        $sql = "SELECT * FROM mref_exp WHERE mref = '{$mref}' AND ubirefer = '{$mexp}' AND pedido = '{$rs3x['pedido']}'";
        $sqlbuilder = new MySqlQuery($db);
        $fila = $sqlbuilder->rawQuery($sql)->getOne();
        if (!$fila) {
            $estado_ubicacion = $existen_rutas_faltantes ? 0 : 1;
            $sq = "INSERT into mref_exp set mref = '{$mref}',ubirefer = '{$mexp}',pedido = '{$rs3x['pedido']}',ubiest = $estado_ubicacion, cod_alma='{$cod_alma}'";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sq);
        } else {
            if (!$existen_rutas_faltantes && $tipo_almacenaje == 'PS') {
                $sql = "Update mref_exp set ubiest = 0 where mref = '{$mref}' AND ubirefer = '{$mexp}' AND pedido = '{$rs3x['pedido']}'";
                $sqlbuilder = new MySqlQuery($db);
                $sqlbuilder->rawQuery($sql);
            }
        }
        $sqlbuilder = new MySqlQuery($db);
        $sq2 = "update ubimapa set ubisitu = 'LL' where ubirefer = '{$mexp}' AND cod_alma='{$cod_alma}'";
        $sqlbuilder->rawQuery($sq2);

        $sqCk = "SELECT
            pedexdet.artrefer,
            pedexdet.pedexentre,
            pedexdet.canprepa,
            pedexdet.expst,
            pedexdet.tienekit, 
            pedexdet.idpadre 
            from pedexdet
            inner join pedexcab on pedexdet.pedexentre = pedexcab.pedexentre
            where pedexcab.pedexentre = '{$rs3x['pedido']}'";
        $rsCk = $db->query($sqCk);
        if (!$rsCk) {
            print_r($db->error);
        }
        $pedStUnidad = 'CE';

        while ($fila = $rsCk->fetch_object()) {
            if ((int) $fila->expst == 0) {
                $pedStUnidad = 'PP';
            }
            if ($fila->tienekit == 'SI') {
                continue;
            }
            if (!empty($fila->idpadre)) {
                $sql = "UPDATE pedexdet SET expst=1, canprepa='{$fila->canprepa}' 
                    WHERE artrefer='{$fila->idpadre}' and pedexentre='{$fila->pedexentre}' 
                    and expst=0";
                $sqlbuilder = new MySqlQuery($db);
                $sqlbuilder->rawQuery($sql);
            }
        }
        $sq4 = "update pedexcab set siturefe = '{$pedStUnidad}', 
            pedexfeclle = now(), pedexhorlle = now() where pedexentre = '{$rs3x['pedido']}'";
        $sqlbuilder = new MySqlQuery($db);
        $sqlbuilder->rawQuery($sq4);

        $sqCk = "SELECT
            pedexdetcajas.artrefer,
            pedexdetcajas.expst 
            from pedexdetcajas
            inner join pedexcabcajas on pedexdetcajas.pedexentre = pedexcabcajas.pedexentre
            where pedexcabcajas.pedexentre = '{$rs3x['pedido']}'";
        //                echo $sq2;
        $rsCk = $db->query($sqCk);
        $pedStPresentacion = 'CE';
        while ($fila = $rsCk->fetch_object()) {
            if ((int) $fila->expst == 0) {
                $pedStPresentacion = 'PP';
                break;
            }
        }
        $sq4 = "update pedexcabcajas set siturefe = '{$pedStPresentacion}', pedexfeclle = now(), pedexhorlle = now() where pedexentre = '{$rs3x['pedido']}'";
        $sqlbuilder = new MySqlQuery($db);
        $sqlbuilder->rawQuery($sq4);

        if (!$existen_rutas_faltantes) {
            $sql = "update ped_multiref set codst = 1 where multiref = '{$mref}' and codst = 0 ";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sql);

            $sql = "update assig_ped set st = 1 where pedido = '{$rs3x['pedido']}'";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sql);
        }

        $sql = "SELECT * FROM assig_ped WHERE pedido = '{$rs3x['pedido']}' AND st = 0 AND pedcajas is null";
        $sqlbuilder = new MySqlQuery($db);
        $fila = $sqlbuilder->rawQuery($sql)->getOne();
        if ($fila) {
            $sql = "UPDATE assig_ped SET st = 1 WHERE pedido = '{$rs3x['pedido']}' AND st = 0 AND pedcajas is null";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sql);
        }
        $sq7 = "select count(pedido) as total from assig_ped where st = 0 and tercod = '{$term}' and ex_st = 1";
        $rs4 = $db->query($sq7);
        $rs4x = $rs4->fetch_assoc();
        $response["cantpd"] = $rs4x['total'];
        $db->commit(); //comit
        if ($pedStUnidad == "CE" && $pedStPresentacion == "CE") {
            $filters = array(
                "pedido" => $rs3x['pedido']
            );
            $curl = curl_init();
            $streamVerboseHandle = fopen('php://temp', 'w+');
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "http://192.168.136.31/wmsd/requests/send_to_sap_ex.php",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 13,
                    CURLOPT_STDERR => $streamVerboseHandle,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => ($filters)
                )
            );
            /*$result = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Request Error:' . curl_error($curl);
            }*/
            curl_close($curl);
        }
        $response["error"] = FALSE;
        $response['mensaje'] = "Datos guardados.";
    } else {
        $db->rollback();
        $response["error"] = TRUE;
        $response['mensaje'] = "No se pudo guardar ubicacion. Multi refencia no asignada";
    }
} else {
    $response["error"] = TRUE;
    $response['mensaje'] = "Muelle no válida.";
}
//$db->rollback();
$db->close();
echo json_encode($response);
exit;

function existen_rutas_disponibles($terminal)
{
    $url = 'http://192.168.136.31/wmsd/api/obtenerPedidosPorCajasMaquinista.php';

    $data = [
        'terminal' => $terminal,
    ];

    // Initialize a cURL session
    $ch = curl_init($url);

    // Convert the data array to URL-encoded query string
    $postData = http_build_query($data);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, 1); // Specify that this is a POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Attach the POST data
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string instead of outputting it directly
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: ' . strlen($postData)
    ));

    // Execute the request and capture the response
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        echo "cURL Error: $error";
        exit;
    }
    $respuesta = json_decode($response, true);
    guardar_custom_log(__FILE__, $respuesta);
    // Close the cURL session
    curl_close($ch);
    return !$respuesta['error'];
}
