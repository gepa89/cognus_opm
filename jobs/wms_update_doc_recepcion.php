<?php $shell = true;
require_once(__DIR__ . '/../conect.php');
require_once(__DIR__ . "/../hanaDB.php");
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sqhan = "SELECT *
            FROM (
                SELECT DISTINCT
                    nast.OBJKY,
                    nast.erdat,
                    nast.eruhr,
                    ekpo.werks,
                    ekpo.lgort,
                    ROW_NUMBER() OVER (PARTITION BY ekko.ebeln, ekko.bsart, ekko.lifnr, ekpo.matnr, ekpo.reslo, ekpo.ebelp ORDER BY nast.erdat DESC,nast.eruhr DESC) AS RowNum
                FROM sapabap1.nast
                INNER JOIN sapabap1.ekko ON sapabap1.ekko.ebeln = sapabap1.nast.OBJKY
                INNER JOIN sapabap1.ekpo ON sapabap1.ekpo.ebeln = sapabap1.ekko.ebeln
                WHERE nast.erdat = TO_CHAR(CURRENT_DATE, 'YYYYMMDD')
                    AND ekpo.loekz NOT IN ('L', 'S')
                    AND ekko.bsart IN ('ZCL', 'ZNB', 'ZUB', 'EUB', 'ZID')
                    AND ekpo.werks = 'CHEL'
                    AND ekpo.lgort = 'CD11'
                    AND nast.kschl = 'NEU'
                    AND nast.parvw = 'SC'
            ) AS temp
            WHERE temp.RowNum = 1;
            ";
/*traer solo posiciones del pedido sin duplicar, entonces hay que validar para traer
la ultima fecha y hora, erdat y eruhr
   * Luego hay que validar los pedidos en pedrecab si esta en pd y si la fecha y hora el query sap es mayor o igual al que esta en mysql
   * si es mayor se procede a actualizar pedrecab y pedrede, eliminar todo y hacer el insert nuevamente
   * si es igual no hacer ningun cambio en mysql
   
   ** select pedrefer,fecmod, hormod
        from pedrecab
        WHERE pedrefer='4300161661'
        and pedresitu ='PD'
    */

//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
while ($rw = odbc_fetch_object($rst)) {
    $fecha = date("Ymd", strtotime($rw->ERDAT));
    $hora = date("H:i:s", strtotime($rw->ERUHR));
    $sql = "SELECT
                pedrecab.*,
                assig_ped.pedido
            FROM
                pedrecab
            LEFT JOIN assig_ped ON
                assig_ped.pedido = pedrecab.pedrefer
            WHERE
                pedrecab.pedrefer = '{$rw->OBJKY}'
                AND (pedrecab.fecmod < '{$fecha}' OR ( pedrecab.fecmod = '{$fecha}' AND pedrecab.hormod < '{$hora}'))
                AND pedrecab.pedresitu = 'PD'
                AND assig_ped.pedido IS NULL;";
    $resultado = $db->query($sql);
    if ($resultado->num_rows > 0) {
        // Recorrer los resultados
        while ($fila = $resultado->fetch_object()) {
            $eliminado = eliminarPedido($db, $fila->pedrefer, $fila->almrefer);
            if (!$eliminado) {
                throw new Exception("Error al eliminar registros", 1);
            }
        }
    } else {
        echo "No se encontraron resultados.";
    }
}
$db->close();
odbc_close($prd);

function eliminarPedido(&$db, $pedrefer, $almacen)
{
    $sql = "DELETE FROM pedrecab WHERE pedresitu='PD' AND pedrefer='{$pedrefer}' AND almrefer='{$almacen}'";
    $res = $db->query($sql);
    if (!$res) {
        print_r($sql);
        echo "error eliminiacion";
        return false;
    }

    $sql = "DELETE FROM pedredet WHERE pedrefer='{$pedrefer}'";
    $res = $db->query($sql);
    if (!$res) {
        print_r($sql);
        echo "error eliminiacion";
        return false;
    }
    return true;
}