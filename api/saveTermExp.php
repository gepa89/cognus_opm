
<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante
$terminal = ($_POST['data']);
$dat = json_decode($terminal, true);
$response = array();
$err = array();
foreach($dat as $k => $v){
    $sq = "insert into regexced set 
        pedido='{$v['pedido']}', 
            material='{$v['material']}', 
                cantidad='{$v['cantidad']}', 
                    fecha = now()";
     if(!$db->query($sq)){
         $err[] = 1;
     }
}

    if(in_array(1,$err)){
       $response["error"] = TRUE;
       $response['mensaje'] = "Error al guardar pedido.".$sq;
    }else{
        $response['mensaje'] = "Datos guardados";
        $response["error"] = FALSE;
    } 
echo json_encode($response);
 include '/var/www/html/closeconn.php';
