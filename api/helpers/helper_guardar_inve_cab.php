<?php
require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");
function
guardar_inve_cab($db, $actualizar,  $cod_alma, $tipo, $id_pedido, $detalles, $usuario)
{
    print_r("aca3");
    $db->begin_transaction();
    try {
        $fecha = date('Y-m-d');
        $hora = date("H:i:s");
        $db->begin_transaction();

        $sql = "INSERT INTO pedinvecab SET 
            situinve='PD',
            clasdoc='INVE',
            codalma='{$cod_alma}',
            fecinve='{$fecha}',
            horinve='{$hora}',
            userinve='{$usuario}';";



        $query = $db->query($sql);

        if (!$query) {
            guardar_error_log("insertar pedido documento", $sql);
            guardar_error_log("insertar pedido documento", $db->error);

            $db->rollback();
            $db->close();
            print_r($sql);
            retorna_resultado(422, ['error' => $db->error]);
        }

        if (!$actualizar) {
            $pedcod = mysqli_insert_id($db);
            $id_pedido = "INVE-$pedcod";
            $sql = "UPDATE pedinvecab SET pedinve = '$id_pedido' WHERE pedcod='$pedcod'";
            $query = $db->query($sql);
            if (!$query) {
                guardar_error_log("insertar pedido pedinvendet", $sql);
                guardar_error_log("insertar pedcod", $db->error);
                $db->rollback();
                $db->close();
                print_r($sql);
                retorna_resultado(422, ['error pedcod' => $db->error]);
            }
        }

        $indice = 1;
        if ($actualizar) {
            $sql = "DELETE FROM pedidosindet WHERE docompra = '{$id_pedido}'";
            $db->query($sql);
        }
        foreach ($detalles as $detalle) {
            $posicion = $indice * 10;
            $codigo_articulo = $detalle[0];
            $descripcion_articulo = $detalle[1];
            $stock = $detalle[2];
            $ubicacion = $detalle[3];
            $tipo = $detalle[4];

            if (empty($codigo_articulo) || empty($ubicacion) || empty($tipo)) {
                continue;
            }

            $sql = "INSERT INTO pedinvedet SET 
            pedinve = '{$id_pedido}',
            posdoc = '{$posicion}',
            artrefer='{$codigo_articulo}',
            artdesc='{$descripcion_articulo}',
            ubirefer='{$ubicacion}',
            ubitipo='{$tipo}',
            canubi1='{$stock}'";
            $query = $db->query($sql);

            if (!$query) {

                guardar_error_log("insertar pedido pedinvendet", $sql);
                guardar_error_log("insertar pedido pedinvendet", $db->error);
                $db->rollback();
                $db->close();
                print_r($sql);
                retorna_resultado(422, ['error' => $db->error]);
            }

            $indice++;
        }



        $db->commit();
    } catch (\Throwable $th) {
        $db->rollback();
        guardar_error_log("helper incab", $th->getMessage());
        throw new Exception("Error Processing Request", 1);
    }
    return $id_pedido;
}
