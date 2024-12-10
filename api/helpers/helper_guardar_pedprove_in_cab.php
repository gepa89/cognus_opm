<?php

require_once(__DIR__ . "/../../logger/logger.php");
require_once(__DIR__ . "/../../utils/respuesta.php");



function guardar_pedproves_in_cab($db, $actualizar, $proveedor, $documento, $moneda, $cod_alma, $codsocie, $orgcompra, $grupocompra, $crt, $factura, $id_pedido, $detalles, $usuario)
{
    try {
        $hoy = date('Y-m-d H:i:s');
        $hora = date("H:i:s");
        $db->begin_transaction();
        $sql = "";
        if ($actualizar) {
            $sql = "UPDATE pedidosincab SET 
            codprove = '{$proveedor}', 
            clasdoc = '{$documento}', 
            codmone='{$moneda}',
            cod_alma='{$cod_alma}',
            codsocie='{$codsocie}',
            orgcompra='{$orgcompra}',
            grupcompra='{$grupocompra}',    
            usuario='{$usuario}',    
            crt='{$crt}',
            factura='{$factura}'     
            WHERE docompra=$id_pedido";
        } else {
            $sql = "INSERT INTO pedidosincab SET 
            codprove = '{$proveedor}', 
            clasdoc = 'PED',
            codmone='{$moneda}',
            cod_alma='{$cod_alma}',
            codsocie='{$codsocie}',
            orgcompra='{$orgcompra}',
            grupcompra='{$grupocompra}',    
            situped= 'PD',
            usuario='{$usuario}',
            crt='{$crt}',    
            factura='{$factura}',  
            horcre='{$hora}',   
            fecre='{$hoy}';";
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

        $sql = "INSERT into pedrecab set 
        pedrefer = '{$id_pedido}',
        pedclase = 'PROF',
        pedresitu = 'PD', 
        pedrefec = '{$hoy}',
        pedrehor = '{$hoy}',
        almrefer = '{$cod_alma}',
        codsocie='{$codsocie}',
        orgcompra='{$orgcompra}',    
        pedrefeclle = '$hoy',
        pedrehorlle = '$hoy',
        codmone='{$moneda}',
        nomprove = '{$nombre_proveedor}',
        ensal = 'in',
        movref = 'C01',
        codprove = '{$proveedor}'";

        if ($actualizar) {
            $sql = "UPDATE pedrecab set 
            pedclase = '{$documento}', 
            almrefer = '{$cod_alma}',
            nomprove = '{$nombre_proveedor}',
            codsocie='{$codsocie}',
            orgcompra='{$orgcompra}',        
            codprove = '{$proveedor}',
            codmone='{$moneda}'
            WHERE pedrefer = '{$id_pedido}'";
        }
        
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
            $sql = "DELETE FROM pedidosindet WHERE docompra = '{$id_pedido}'";
            $db->query($sql);
            $sql = "DELETE FROM pedredet WHERE pedrefer='{$id_pedido}'";
            $db->query($sql);
        }
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
           
            $peso_bruto = $detalle[8];
            $centro = $detalle[9];
            $almacen = $detalle[10];

            if (empty($codigo_articulo) || empty($cantidad) || empty($precio_unitario)) {
                continue;
            }

            $sql = "INSERT INTO pedidosindet SET 
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
          
            pesbruto='{$peso_bruto}',    
            cencod='{$centro}',
            cod_alma='{$almacen}'";
            $query = $db->query($sql);

            if (!$query) {
                guardar_error_log("insertar pedido documento", $sql);
                guardar_error_log("insertar pedido documento", $db->error);
                $db->rollback();
                $db->close();
                retorna_resultado(422, ['error' => $db->error]);
            }
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


            $sql = "INSERT INTO pedredet SET 
            pedpos = '{$posicion}', 
            artrefer='{$codigo_articulo}',
            unimed='{$unidad_medida}',
            canpedi='{$cantidad}',
            precio='{$precio_unitario}',    
            prectot='{$precio_total}',    
            pedrefer='{$id_pedido}'";
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