<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require(__DIR__ . '/../../conect.php');
require_once(__DIR__ . "/../../logger/logger.php");
function asignar_trabajo_maquinista($db, $terminal, $multireferencia, $ubicacion_pedido)
{

    $db->set_charset("utf8");
    //$db->autocommit(false);

    $respuesta = array("estado" => "exito", "mensaje" => "");
    $db->begin_transaction();
    try {
        $sql = "SELECT almrefer,terzonpre from termi where tercod = \"{$terminal}\" LIMIT 1";

        $query = $db->query($sql);
        if ($query->num_rows == 0) {
            $respuesta['estado'] = "falla";
            $respuesta['mensaje'] = "No existe Terminal";
            return $respuesta;
        }
        $resultado = $query->fetch_object();
        $zona = $resultado->terzonpre;
        $ahora = date("Y-m-d H:i:s");
        $select = "SELECT pedubidet.*, pedubicab.cod_alma 
            FROM pedubidet 
            inner join pedubicab ON pedubicab.pedubicod = pedubidet.pedubicod 
            WHERE pedubidet.etnum=\"{$multireferencia}\" 
            AND pedubicab.cod_alma = \"{$resultado->almrefer}\" and pedubicab.pedubicod=\"$ubicacion_pedido\" LIMIT 1";
        guardar_sql_log($select);
        $res = $db->query($select);
        if ($res->num_rows == 0) {
            //$db->rollback();
            $db->close();
            $respuesta['estado'] = "falla";
            $respuesta['mensaje'] = "No existe datos de multireferencia";
            return $respuesta;
        }
        $resultado = $res->fetch_assoc();
        $pedido = $resultado['pedubicod'];
        $cod_alma = $resultado['cod_alma'];
        /*$sql = "INSERT INTO assig_ped SET tercod=\"{$terminal}\", 
                zona=\"{$zona}\",
                pedido=\"{$pedido}\", 
                cod_alma=\"{$cod_alma}\", 
                st=0, ex_st=0";
        $db->query($sql);*/
        $sql = "REPLACE INTO ped_multiref SET terminal=\"{$terminal}\",zona='$zona', pedido=\"{$pedido}\", cod_alma=\"{$cod_alma}\", 
        multiref=\"{$multireferencia}\", mrts=\"{$ahora}\", codst=0";
        $res = $db->query($sql);
        if (!$res) {
            $mensaje = $db->error;
            $respuesta['estado'] = "falla";
            $respuesta['mensaje'] = $mensaje;
            $db->rollback();
            $db->close();
            return $respuesta;
        }
        $db->commit();
    } catch (\Throwable $th) {
        $respuesta['estado'] = "falla";
        $db->rollback();
        print_r($th);
        guardar_error_log("guardar trabajo maquinista", $th->getMessage());
    }
    $db->close();
    return $respuesta;
}
