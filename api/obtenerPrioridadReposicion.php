<?php
require('../conect.php');
require_once(__DIR__ . "/../logger/logger.php");
function retorna_resultado($datos)
{
    echo json_encode($datos);
    exit;
}
header('Content-type: application/json; charset=utf-8');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$terminal = strtoupper($_POST["terminal"]);

$sql_terminal = "SELECT almrefer,assig_ped.pedido, assig_ped.pedcajas FROM termi 
left join assig_ped on assig_ped.tercod = termi.tercod and termi.almrefer=assig_ped.cod_alma 
WHERE assig_ped.st='0' and termi.tercod = \"$terminal\" LIMIT 1";
$datos = $db->query($sql_terminal)->fetch_assoc();
guardar_info_log(__FILE__, $sql_terminal);
$codalma = $datos["almrefer"];
$pedido = $datos["pedido"];
$pedido_con_presentacion = $datos["pedcajas"] === "1";
$sql = "SELECT clas_doc FROM prioridades WHERE clas_doc IN ('REPO','MAQUI','EXPEMAQUI') AND cod_alma = '$codalma'  ORDER BY ord_pri ASC";
$resultado = $db->query($sql);
$response = array();
if ($resultado->num_rows == 0) {
    retorna_resultado(array("prioridad" => "ninguna"));
}
$filtro_pedido = "";
while ($obj = $resultado->fetch_object()) {
    switch ($obj->clas_doc) {
        case 'MAQUI':
            if (!empty($pedido)) {
                $filtro_pedido = " AND pedubicab.pedubicod = '$pedido'";
            }
            $sq = "select DISTINCT etnum AS multireferencia, muelle 
                from pedubicab 
                inner join pedubidet ON pedubicab.pedubicod = pedubidet.pedubicod
                where pedubicab.pedclase ='UB' and pedubicab.situped in ('PD','PP' )
                and pedubicab.cod_alma = '$codalma' {$filtro_pedido}
                ";
            guardar_info_log(__FILE__, $sq);
            $maqui = $db->query($sq);
            if ($maqui->num_rows == 0) {
                break;
            }
            $datos = $maqui->fetch_all(MYSQLI_ASSOC);
            retorna_resultado(array("prioridad" => $obj->clas_doc, "datos" => $datos));
            break;
        case 'REPO':
            if (!empty($pedido)) {
                $filtro_pedido = " AND pedexcab.pedexentre = '$pedido'";
            }
            $sq =
                "
                select COUNT(*) as cantidad 
                from pedexcab 
                inner join assig_ped on assig_ped.pedido = pedexcab.pedexentre 
                where pedexcab.pedclase ='REPO' and pedexcab.siturefe in ('PD','PP' ) and pedexcab.almrefer = '$codalma'
                {$filtro_pedido} ";
            $repo = $db->query($sq)->fetch_object();
            if ($repo->cantidad == 0)
                break;
            retorna_resultado(array("prioridad" => $obj->clas_doc));
            break;
        case 'EXPEMAQUI':

            if (!empty($pedido)) {
                if (!$pedido_con_presentacion) {
                    break;
                }
                $filtro_pedido = " AND pedexcabcajas.pedexentre = '$pedido'";
            }
            $sq =
                "
                    select COUNT(*) as cantidad 
                    from pedexcabcajas 
                    inner join assig_ped on assig_ped.pedido = pedexcabcajas.pedexentre 
                    where pedexcabcajas.siturefe in ('PD','PP') and pedexcabcajas.almrefer = '$codalma'
                    ";
            $repo = $db->query($sq)->fetch_object();
            if ($repo->cantidad == 0)
                break;
            retorna_resultado(array("prioridad" => $obj->clas_doc));
            break;
    }
}
retorna_resultado(array("prioridad" => "ninguna"));