<?php
require_once(__DIR__ . "/../database/SQLBuilder.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/hana.php");

define("CENTRO", "LCC1");
define("ALMACEN", "LDAL");

class IngresoDocumentosExpedicion
{

    private $db;
    private $hana;
    public function __construct()
    {
        $this->db = MysqlDB::obtenerInstancia();
        #$this->hana = HanaDB::obtenerInstancia();
    }

    public function iniciar()
    {
        print_r("Ingreso de documentos de expedicion \n");
        #$this->verificarProcesoEnCurso();
        $documentos_del_dia = $this->obtenerDocumentosExpedicion();
        $this->manejarDocumentosExpedicion($documentos_del_dia);
    }

    private function manejarDocumentosExpedicion($documentos)
    {
        $pedidos_existentes = $this->_pedidos_existentes(array_keys($documentos));
        $pedidos_nuevos = array_diff(array_keys($documentos), $pedidos_existentes);
        print_r($pedidos_nuevos);
        $this->insertarPedidosNuevos($pedidos_nuevos, $documentos);
        /*$this->actualizarPedidos($pedidos_actualizados, $documentos);
        $this->eliminarPedidos($pedidos_eliminados);*/
    }

    private function insertarPedidosNuevos($pedidos_nuevos, $documentos)
    {
        $db = MysqlDB::obtenerInstancia();
        $db->begin_transaction();
        foreach ($pedidos_nuevos as $pedido) {
            $parametros[] = $pedido;
            $parametros[] = $documentos[$pedido]['cliente'];
            $parametros[] = $documentos[$pedido]['nombre_cliente'];
            $parametros[] = $documentos[$pedido]['fecha_pedido'];
            $parametros[] = $documentos[$pedido]['hora_pedido'];
            $parametros[] = $documentos[$pedido]['tipo_pedido'];
            $parametros[] = $documentos[$pedido]['tipo_entrega'];
            $parametros[] = $documentos[$pedido]['tipo_transporte'];
            $sql = "INSERT INTO pedexcab (pedexentre, pedexcli, pedexnom, pedexfec, pedexhor, pedextip, pedexten, pedextrp) VALUES (?,?,?,?,?,?,?,?)";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sql, $parametros);
            $this->insertarDetallePedido($db, $pedido, $documentos[$pedido]['items']);
        }

        $db->rollback();
    }

    private function insertarDetallePedido(&$db, $pedido, $items)
    {
        foreach ($items as $item) {
            $parametros = [
                $pedido,
                $item['posicion'],
                $item['material'],
                $item['descripcion'],
                $item['cantidad'],
                $item['unidad_medida'],
                $item['centro'],
                $item['almacen'],
                $item['grupo_articulo'],
                $item['lote']
            ];
            $sql = "INSERT INTO pedexdet (pedexentre, pedexpos, pedexmat, pedexdes, pedexcant, pedexume, pedexcen, pedexalm, pedexgpo, pedexlot) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $sqlbuilder = new MySqlQuery($db);
            $sqlbuilder->rawQuery($sql, $parametros);
        }
    }

    private function verificarProcesoEnCurso()
    {
        $db = MysqlDB::obtenerInstancia();
        $db->begin_transaction();
        $sql = "SELECT * FROM scheduled_jobs WHERE estado=true AND script='wms_ingreso_doc_expedicion'";
        $sqlbuilder = new MySqlQuery($db);
        $existe_proceso = $sqlbuilder->rawQuery($sql)->exists();
        if ($existe_proceso) {
            print_r("Proceso en curso");
            die();
        }
        $parametros = [
            true
        ];
        $sql = "UPDATE scheduled_jobs SET estado=? WHERE script='wms_ingreso_doc_expedicion'";
        $respuesta = $sqlbuilder->rawQuery($sql, $parametros)->get_result();
        $db->close();
    }

    private function obtenerDocumentosExpedicion()
    {
        $instancia_hana = HanaDB::obtenerInstancia();
        $sql = "SELECT DISTINCT
                    likp.kunag, 
                    kna1.name1,
                    likp.vbeln, 
                    likp.erdat, 
                    likp.lfart, 
                    likp.erzet,
                    likp.VSBED,
                    lips.VGBEL, 
                    lips.LFIMG, 
                    lips.MATNR, 
                    lips.ARKTX, 
                    lips.werks,
                    lips.lgort,
                    lips.POSNR,
                    lips.meins,
                    likp.VSTEL,
                    lips.matkl,
                    lips.lgpbe
                FROM sapabap1.likp
                LEFT JOIN sapabap1.lips ON likp.vbeln = lips.vbeln 
                LEFT JOIN sapabap1.kna1 ON likp.kunnr = kna1.kunnr
                LEFT JOIN sapabap1.vbfa ON vbfa.VBELV = likp.VBELN
                WHERE 
                    likp.lfart IN ('ZCON', 'ZCOS', 'ZCRE', 'ZOBQ', 'ZSCP', 'ZGAR', 'ZSVC', 'ZMOS')
                    AND lips.werks = ? and lips.lgort = ? limit 100 ";
        $resultado = HanaDB::ejecutarConsulta($instancia_hana, $sql, [CENTRO, ALMACEN]);
        $pedidos = [];
        while ($fila = odbc_fetch_object($resultado)) {
            $codigo_pedido = str_pad($fila->VBELN, 10, '0', STR_PAD_LEFT);
            if (isset($pedidos[$codigo_pedido])) {
                $pedidos[$codigo_pedido]['items'][] = [
                    'codigo_pedido' => $codigo_pedido,
                    'posicion' => $fila->POSNR,
                    'material' => $fila->MATNR,
                    'descripcion' => iconv('ISO-8859-1', 'UTF-8', $fila->ARKTX),
                    'cantidad' => $fila->LFIMG,
                    'unidad_medida' => $fila->MEINS,
                    'centro' => $fila->WERKS,
                    'almacen' => $fila->LGORT,
                    'grupo_articulo' => $fila->MATKL,
                    'lote' => $fila->LGPBE,
                ];
                continue;
            }
            $pedidos[$codigo_pedido] = [
                'codigo_pedido' => $codigo_pedido,
                'cliente' => $fila->KUNAG,
                'nombre_cliente' => $fila->NAME1,
                'fecha_pedido' => $fila->ERDAT,
                'hora_pedido' => $fila->ERZET,
                'tipo_pedido' => $fila->LFART,
                'tipo_entrega' => $fila->VSBED,
                'tipo_transporte' => $fila->VSTEL,
                'items' => [
                    [
                        'codigo_pedido' => $codigo_pedido,
                        'posicion' => $fila->POSNR,
                        'material' => $fila->MATNR,
                        'descripcion' => iconv('ISO-8859-1', 'UTF-8', $fila->ARKTX),
                        'cantidad' => $fila->LFIMG,
                        'unidad_medida' => $fila->MEINS,
                        'centro' => $fila->WERKS,
                        'almacen' => $fila->LGORT,
                        'grupo_articulo' => $fila->MATKL,
                        'lote' => $fila->LGPBE,
                    ]
                ]
            ];
        }
        return $pedidos;
    }

    private function _pedidos_existentes(array $codigo_pedidos)
    {
        $db = MysqlDB::obtenerInstancia();
        $codigo_pedido_parametro = implode("','", $codigo_pedidos);
        $codigo_pedido_parametro = "'$codigo_pedido_parametro'";

        $sql = "SELECT * FROM pedexcab WHERE pedexentre IN (?)";
        $sqlbuilder = new MySqlQuery($db);
        $pedidos = $sqlbuilder->rawQuery($sql, [$codigo_pedido_parametro])->getAll();
        $pedidos_existentes = [];
        foreach ($pedidos as $pedido) {
            $pedidos_existentes[] = $pedido['pedexentre'];
        }
        $db->close();
        return $pedidos_existentes;
    }
}

$ingreso_expedicion = new IngresoDocumentosExpedicion();
$ingreso_expedicion->iniciar();
