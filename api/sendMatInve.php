<?php require ('../conect2.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante

//                params.put("cantidad", vlInven);
//                params.put("ubicacion", ubica);
//                params.put("material", mat);
//                params.put("usuario", usuario);
//                params.put("diferencia", diferencia);
//                Log.d("params", params.toString());



$cantidad = $_POST['cantidad'];//cant ingresada en input
$ubicacion = ($_POST['ubicacion']);
$material = ($_POST['material']);
$usuario = ($_POST['usuario']);
$diferencia = ($_POST['diferencia']);//diferencia entre stock y cantidad pedida


if(isset($material)){
    $ck_id = "select invedoc from invenpic where invedoc >= 3000000000 order by invedoc desc limit 1";
    $ck_rs = $db->query($ck_id);
    if($ck_rs->num_rows == 0){
        $id_doc = 3000000000;
    }else{
        $ckd_id = $ck_rs->fetch_assoc();
        $id_doc = $ckd_id['invedoc']+1;
    }
                
    $dff = $diferencia - $cantidad;
                
//    reviso si pallet esta vacio
    $sqIns = "insert into invenpic set ubirefer = '{$ubicacion}',artrefer = '{$material}',canfisi = {$cantidad},canubic = {$diferencia},operef = '{$usuario}', fecinvepic = now(), horinvepic = now(), invetipo = 'INPI', invedoc = {$id_doc}, diferencia = {$dff}";
    
    print_r($sqIns);
    
        if($db->query($sqIns)){        

            $response["error"] = FALSE;
            $response['mensaje'] = "Inventario registrado correctamente. ";
        }else{
            $response["error"] = TRUE;
            $response['mensaje'] = "Error al registrar invnetario.";
        }
    }else{
        $response["error"] = TRUE;
        $response['mensaje'] = "Material no ingresado.";
    }
    
    echo json_encode($response);


