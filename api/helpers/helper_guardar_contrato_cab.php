<?php

require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");



function guardar_contratos_cab($db, $actualizar, $proveedor, $documento, $moneda,$codsocie, $grupcompra, $orgcompra,$cencod, $cod_alma, $id_pedido, $detalles)
{
    try {
        $hoy = date('Y-m-d H:i:s');
        $hora = date("H:i:s");
        $db->begin_transaction();
        $sql = "";
        if ($actualizar) {
            $sql = "UPDATE contcab SET 
            codprove = '{$proveedor}', 
            pedclase = 'PCON', 
            codmone='{$moneda}',
            sociedad='{$codsocie}',
            orgcompra='{$orgcompra}',
            cencod='{$cencod}',
            codalma='{$cod_alma}',    
            situped = 'PD',
            grupcompra='{$grupcompra}'
         
             
            WHERE idcontra=$id_pedido";
        } else {
            $sql = "INSERT INTO contcab SET 
            codprove = '{$proveedor}', 
            pedclase = 'PCON', 
            codmone='{$moneda}',
            sociedad='{$codsocie}',    
            situped = 'PD',
            orgcompra='{$orgcompra}',
            grupcompra='{$grupcompra}',
            cencod='{$cencod}',
            codalma='{$cod_alma}',    
           
          
            horped='{$hora}',   
            fecped='{$hoy}';";
        }


        $query = $db->query($sql);
      // print $sql;
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
      //  print $sql;
        if (!$query) {
            guardar_error_log("insertar pedido documento", $sql);
            guardar_error_log("insertar pedido documento", $db->error);
            $db->rollback();
            $db->close();
            retorna_resultado(422, ['error' => $db->error]);
        }
        $indice = 1;
        if ($actualizar) {
            $sql = "DELETE FROM contdet WHERE idcontra = '{$id_pedido}'";
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

            if (empty($codigo_articulo) || empty($cantidad) || empty($precio_unitario)) {
                continue;
            }

            $sql = "INSERT INTO contdet SET 
            idcontra = '{$id_pedido}',
            posicion = '{$posicion}',
            material='{$codigo_articulo}',
            matdesc='{$descripcion_articulo}',
            canped='{$cantidad}',
            precuni='{$precio_unitario}',
            prectotal='{$precio_total}',
            unimed='{$unidad_medida}',
                
            cencod='{$centro}',
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
          //  print $sql;
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