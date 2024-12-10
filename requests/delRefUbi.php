<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
//$db->autocommit(false);
$rowId = $_POST['id'];

$sqc = "SELECT 
artubiref.artrefer,
artubiref.ubirefer,
sum(stockubi.canti) as total 
FROM artubiref 
LEFT JOIN stockubi on artubiref.ubirefer=stockubi.ubirefer and artubiref.artrefer=stockubi.artrefer
WHERE refid='{$rowId}'

group by stockubi.ubirefer, stockubi.artrefer,stockubi.cod_alma 
HAVING SUM(stockubi.canti) = 0.000 ";
$resultado = $db->query($sqc);

$resul2 = $resultado->fetch_assoc();
if ($resultado->num_rows > 0) {
  
        $sq = "DELETE FROM artubiref WHERE refid = {$rowId}";
        if($db->query($sq)){        
            $msg = 'Registro Borrado';
            $err = 0;
            $db->commit();        
        }else{
        $msg = 'Error.\n'.$msgx;        
        $err = 1;
    }
    
              
} else {
  $msg = 'No se puede borrar registro, Material con Stock en Ubicacion';  
}
    

    echo json_encode(array('msg' => $msg, 'err' => $err));
    exit();
