<?php

require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");



function guardar_pedfletes_cab($db, $actualizar, $proveedor, $clasflete, $moneda, $cod_alma, $cencod, $codsocie, $orgcompra, $puertcarga, $incote, $id_pedido, $detalles)
{
    try {
        $hoy = date('Y-m-d H:i:s');
        $hora = date("H:i:s");
        $db->begin_transaction();
        $sql = "";
        if ($actualizar) {
            $sql = "UPDATE fletecab SET 
            codprove = '{$proveedor}', 
            pedclase = '{$clasflete}',
            codmone='{$moneda}',
            codpuertor='{$puertcarga}',
            sociedad='{$codsocie}',    
            orgcompra='{$orgcompra}',
            codalma='{$cod_alma}',    
            cencod ='{$cencod}',        
            incote='{$incote}'
         
             
            WHERE idpedflete=$id_pedido";
            
   //       print $sql;  
        } else {
            $sql = "INSERT INTO fletecab SET 
            codprove = '{$proveedor}', 
            pedclase = '{$clasflete}',
            codmone='{$moneda}',
            sociedad='{$codsocie}',    
            orgcompra='{$orgcompra}',
            codpuertor='{$puertcarga}',    
            codalma='{$cod_alma}',     
            cencod ='{$cencod}',  
            situped='PD',     
            incote='{$incote}',
            
          
            horped='{$hora}',   
            fecped='{$hoy}';";
        }


        $query = $db->query($sql);
 //       print $sql;
        if (!$query) {
            guardar_error_log("insertar pedido documento", $sql);
            guardar_error_log("insertar pedido documento", $db->error);

            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }

        if (!$actualizar) {
            $id_pedido = (int) mysqli_insert_id($db);
        }

        $sql = "SELECT * from proveedores where codprove = '{$proveedor}'";
        $nombre_cliente = $db->query($sql)->fetch_assoc()['clinom'];
        $nombre_proveedor = $db->query($sql)->fetch_assoc()['nombre'];

      
        
        $query = $db->query($sql);
       // print $sql;
        if (!$query) {
            guardar_error_log("insertar pedido documento", $sql);
            guardar_error_log("insertar pedido documento", $db->error);
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }
        $indice = 1;
        if ($actualizar) {
            $sql = "DELETE FROM fletedet WHERE idpedflete = '{$id_pedido}'";
            $db->query($sql);
            
        }
        foreach ($detalles as $detalle) {

        //    $posicion = $indice * 10;
            $cantidad = $detalle[0];
            $tipconte = $detalle[1];
            $descripcion = $detalle[2];
            $numconte = $detalle[3];
            
            $observacion = $detalle[4];
            

            if (empty($cantidad) || empty($tipconte) || empty($descripcion)) {
                continue;
            }

            $sql = "INSERT INTO fletedet SET 
            idpedflete = '{$id_pedido}',
            canconte = '{$cantidad}',
            tipconte='{$tipconte}',
            detconte='{$descripcion}',
            numconte='{$numconte}',
            obseconte='{$observacion}'";
            $query = $db->query($sql);
        //    print $query;
            if (!$query) {
                guardar_error_log("insertar pedido documento", $sql);
                guardar_error_log("insertar pedido documento", $db->error);
                $db->rollback();
                $db->close();
                retorna_resultado(422, ['error' => $db->error]);
            }
        //    print $sql;
        //    $sql = "UPDATE arti SET costo = '{$precio_unitario}', unimed = '{$unidad_medida}'
        //    WHERE artrefer = '$codigo_articulo'";
        //    $query = $db->query($sql);

            if (!$query) {
                guardar_error_log("insertar pedido documento", $sql);
                guardar_error_log("insertar pedido documento", $db->error);
                $db->rollback();
                $db->close();
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

?>