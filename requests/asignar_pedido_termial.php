<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
require_once(__DIR__ . "/../utils/validadores.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$db->autocommit(false);
$db->begin_transaction();
$fecha = date("Y-m-d");
$hora = date("H:i:s");
$usuario = $_SESSION['user'];
try {
    //crear hash del estante
    $pedido = $_POST['pedido'];
    $terminal = $_POST['terminal'];
    $codalma = $_POST['almacen'];
    $tiene_division = $_POST['tiene_division'];
    $tabla = "pedexcab";
    if ($tiene_division) {
        $tabla = "pedexcabcajas";
    }
    guardar_info_log("asignar pedido", json_encode($_POST));

    $validacion = validar_campos($_POST, ["pedido", "terminal", "almacen"]);
    if (!$validacion['valido']) {
        $db->close();
        echo json_encode(array('msg' => $validacion['mensaje'], 'err' => 1));
        exit();
    }
    $sq = "select canped,terzonpre,tipac from termi where tercod = '{$terminal}' and almrefer='$codalma' limit 1";
    $rs2 = $db->query($sq);
    $cant = $rs2->fetch_assoc();
    $zona = $cant['terzonpre'];
    $tipo_terminal = $cant['tipac'];
    //echo $ca;
//cantidad asignada a la terminal

    $sc = "select COUNT(tercod) as total from assig_ped where tercod = '{$terminal}' and cod_alma = '{$codalma}'  and st = 0";
    $rs = $db->query($sc);
    $cant_as = $rs->fetch_assoc();
    guardar_info_log(__FILE__, $sc);
    guardar_info_log(__FILE__, $sq);

    if ((int) $cant_as['total'] < (int) $cant['canped']) {
        $sc = "select pedclase from $tabla where pedexentre = '{$pedido}' and almrefer = '{$codalma}' ";
        //    echo $sc;
        $rs = $db->query($sc);
        $claPedi = $rs->fetch_assoc();
        if ($claPedi['pedclase'] != 'REPO') {
            $campo_division = $tiene_division ? ", pedcajas=1" : "";
            $si = "insert into assig_ped set pedido = '{$pedido}', fecasig = '{$fecha}', usuario = '{$usuario}', horasig = '{$hora}',
            zona='{$zona}', 
            tercod = '{$terminal}', st = 0, cod_alma = '{$codalma}' $campo_division";
            if ($db->query($si)) {
                $msg = 'Pedido asignado correctamente 1';
                $err = 1;
            } else {
                $msg = 'Error al asignar pedido';
                $err = 1;
            }
        } else {
            $sc = "SELECT sum(c.canti) as stock from pedexdet a 
            inner join $tabla b on a.pedexentre = b.pedexentre 
            inner join stockubi c on c.artrefer = a.artrefer
            inner join ubimapa d on d.ubirefer = c.ubirefer and d.cod_alma = c.cod_alma and d.ubitipo = 'RE'
            where b.pedexentre = '{$pedido}' GROUP BY a.artrefer, c.ubirefer HAVING sum(c.canti) > 0";
            $rs = $db->query($sc);
            $cant_ped = $rs->fetch_assoc();

            if ((int) $cant_ped['stock'] > 0) {
                $resultado = verificar_asignacion($db, $pedido, $terminal, $codalma, $zona, $tipo_terminal);
                guardar_info_log("resultado verificacion", json_encode($resultado));
                $msg = $resultado['msg'];
                $err = $resultado['err'];
            } else {
                $msg = 'Error al asignar pedido. Material sin Stock';
                $err = 1;
            }
        }
    } else {
        guardar_info_log("asignar terminal", "cant_total< total_ped");
        $msg = 'Terminal saturada';
        $err = 1;
    }
    $db->commit();
} catch (\Throwable $th) {
    $db->rollback();
    print_r($th->getMessage());
}
$db->close();

echo json_encode(array('msg' => $msg, 'err' => $err));
exit();

function assignar_terminal($db, $pedido, $terminal, $codalma, $tiene_division = false, $zona)
{
    $msg = "";
    $err = 0;
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $campo_division = $tiene_division ? ", pedcajas=1" : "";

    $si = "insert into assig_ped set pedido = '{$pedido}', zona='{$zona}', 
    tercod = '{$terminal}', fecasig = '{$fecha}', horasig = '{$hora}', usuario = '{$usuario}', cod_alma = '{$codalma}', st = 0 {$campo_division}";
    if ($db->query($si)) {
        $msg = 'Pedido asignado correctamente 2';
        $err = 1;
    } else {
        $msg = 'Error al asignar pedido';
        $err = 1;
    }
    return array("msg" => $msg, "err" => $err);
}

function verificar_asignacion($db, $pedido, $terminal, $codalma, $zona, $tipo_terminal)
{
    guardar_info_log("asignar pedido", "verificar asignacion");
    /*$sql = "SELECT clas_doc FROM prioridades WHERE cod_alma='{$codalma}' AND cod_env IS NULL ORDER BY ord_pri ASC LIMIT 1";
    $query = $db->query($sql);
    $resultado = $query->fetch_assoc();
    guardar_info_log("tipo documento", $resultado['clas_doc']);*/
    if ($tipo_terminal == 'REPO') {
        guardar_info_log("asignar pedido", "REPO");
        return assignar_terminal($db, $pedido, $terminal, $codalma, false, $zona);
    }
    $sql = "SELECT COUNT(*) AS cantidad FROM pedmuelle WHERE pedstatus=0 AND cod_alma='{$codalma}'";
    $query = $db->query($sql);
    $resultado = $query->fetch_assoc();
    guardar_info_log("asignar pedido", json_encode($resultado));
    // trabajo maquinista pendiente, no asigna nuevo recurso
    if ($resultado['cantidad'] > 0) {
        return array("msg" => "Terminal saturada", "err" => 1);
    }
    //asignar terminal por que no hay reposicion y trabajo maquinista
    return assignar_terminal($db, $pedido, $terminal, $codalma, false, $zona);
}