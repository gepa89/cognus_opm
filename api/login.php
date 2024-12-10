<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
header('Content-Type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
if ($db->connect_errno) {
    $respuesta = array("error" => true, "message" => $db->error);
    guardar_error_log("login", json_encode($respuesta));
    echo json_encode($respuesta);
    exit();
}
guardar_info_log("login", "conectado");
//crear hash del estante
$user = $_POST['user'];
$terminal = strtoupper($_POST['terminal']);


if (isset($user)) {
    $response = array();
    $usr = strtoupper($user);
    $sq = "select pr_user, pr_nombre, pr_apellido, pr_rol from usuarios where pr_user = '{$usr}'";
    $rs = $db->query($sq);
    $usuario = $rs->fetch_assoc();
    if (!$usuario) {
        $response["error"] = TRUE;
        $response['message'] = "Usuario no existe";
        echo json_encode($response);
        exit;
    }
    $sq = "select * from termi_assign where ope = '{$usr}'";
    $rs = $db->query($sq);
    if ($rs->num_rows == 0) {
        $sqt = "select * from termi where tercod = '{$terminal}'";
        $rster = $db->query($sqt);
        if ($rster->num_rows > 0) {
            $terar = $rster->fetch_assoc();

            $sq1 = "select * from termi_assign where tercod = '{$terminal}'";
            $rs1 = $db->query($sq1);
            if ($rs1->num_rows == 0) {
                $sins = "insert into termi_assign set ope = '{$usr}', tercod = '{$terminal}'";
                if ($db->query($sins)) {
                    $sinsx = "insert into termilog set operef = '{$usr}', tercod = '{$terminal}', fecencen = now(), horencen = now()";
                    $db->query($sinsx);


                    $response['user']['prt_user'] = $usr;
                    $response['user']['prt_user_name'] = $usuario['pr_nombre'] . ' ' . $usuario['pr_apellido'];
                    $response['user']['prt_terminal'] = $terminal;
                    $response['user']['prt_tipo_terminal'] = $terar['tipac'];
                    $response["error"] = FALSE;
                } else {
                    $response["error"] = TRUE;
                    $response['message'] = "Error al acceder";
                }
            } else {
                $response["error"] = TRUE;
                $response['message'] = "Terminal ya tiene operario asignado";
            }
        } else {
            $response["error"] = TRUE;
            $response['message'] = "Terminal no existe";
        }
    } else {
        $response["error"] = TRUE;
        $response['message'] = "Usuario ya tiene terminal asignada";
    }

    echo json_encode($response);
}
