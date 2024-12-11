<?php

require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");



function guardar_ocs_cab($db, $actualizar, $proveedor, $documento, $moneda, $codsocie, $cod_alma, $cencod, $orgcompra, $grupcompra, $id_pedido, $detalles)
{
    try {
        $hoy = date('Y-m-d H:i:s');
        $hora = date("H:i:s");
        $db->begin_transaction();
        $sql = "";
        if ($actualizar) {
            $sql = "UPDATE compras SET 
            codprove = '{$proveedor}', 
            pedclase = 'PCOM', 
            codmone='{$moneda}',
            sociedad='{$codsocie}',
            codalma='{$cod_alma}',     
            cencod='{$cencod}',   
            orgcompra='{$orgcompra}',
            grupcompra='{$grupcompra}'
         
             
            WHERE idoc=$id_pedido";
        } else {
            $sql = "INSERT INTO compras SET 
            codprove = '{$proveedor}', 
            pedclase = 'PCOM', 
            codmone='{$moneda}',
            sociedad='{$codsocie}',         
            codalma='{$cod_alma}',     
            situped='PD',       
            cencod='{$cencod}',  
            orgcompra='{$orgcompra}',
            grupcompra='{$grupcompra}',
            
          
            horped='{$hora}',   
            fecped='{$hoy}';";
        }


        $query = $db->query($sql);
     //   print $sql;
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
    //    print $sql;
        if (!$query) {
            guardar_error_log("insertar pedido documento", $sql);
            guardar_error_log("insertar pedido documento", $db->error);
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }
        $indice = 1;
        if ($actualizar) {
            $sql = "DELETE FROM comdet WHERE idoc = '{$id_pedido}'";
            $db->query($sql);
            
        }
        foreach ($detalles as $detalle) {

            $posicion = $indice * 10;
            $codigo_articulo = $detalle[0];
            $descripcion_articulo = $detalle[1];
            $cantidad = $detalle[2];
            $unidad_medida = $detalle[3];
            
            $precio_unitario = $detalle[4];
            $precio_total = $detalle[5];
            
            $centro = $detalle[6];
            $almacen = $detalle[7];
            $contrato = $detalle[8];
            $oferta = $detalle[9];

            if (empty($codigo_articulo) || empty($cantidad) || empty($precio_unitario)) {
                continue;
            }

            $sql = "INSERT INTO comdet SET 
            idoc = '{$id_pedido}',
            posicion = '{$posicion}',
            material='{$codigo_articulo}',
            matdesc='{$descripcion_articulo}',
            canped='{$cantidad}',
            precuni='{$precio_unitario}',
            prectotal='{$precio_total}',
            unimed='{$unidad_medida}',
                
            cencod='{$centro}',
            pedoferta='{$oferta}',
            pedcontra='{$contrato}',    
            codalma='{$almacen}'";
            $query = $db->query($sql);
       //     print $query;
            if (!$query) {
                guardar_error_log("insertar pedido documento", $sql);
                guardar_error_log("insertar pedido documento", $db->error);
                $db->rollback();
                $db->close();
                retorna_resultado(422, ['error' => $db->error]);
            }
      //      print $sql;
            $sql = "UPDATE arti SET costo = '{$precio_unitario}', unimed = '{$unidad_medida}'
            WHERE artrefer = '$codigo_articulo'";
            $query = $db->query($sql);

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