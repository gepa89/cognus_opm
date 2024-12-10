<?php
require ('../conect.php');
//echo '<pre>';var_dump($_POST);echo '</pre>';
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//crear hash del estante


function getDataOnBd($id, $db){
    $sq = "select artrefer, ubirefer, ubitipo, refid from artubiref where refid = {$id}";
    $rs = $db->query($sq);
    return array_values($rs->fetch_assoc());
}
function getMasterData($art, $db){
    $sq = "select 
            arti.*,
            artubiref.ubirefer,
            artubiref.ubitipo,
            ubimapa.ubisitu
            from 
            arti
            left join artubiref on artubiref.artrefer = arti.artrefer
            left join ubimapa on artubiref.ubirefer = ubimapa.ubirefer
            where arti.artrefer = '{$art}'";
    $rs = $db->query($sq);
    $data = array();
    while($ax = $rs->fetch_assoc()){
        $data[$ax['ubirefer']] = $ax;
    }
    return $data;
}
function validarUbiDest($ubi,$tip, $db){
    $sq = "select 
            ubimapa.ubirefer, 
            ubimapa.ubitipo, 
            ubimapa.ubisitu,
            artubiref.artrefer,
            artubiref.refid
            from ubimapa 
            left join artubiref on artubiref.ubirefer = ubimapa.ubirefer
            where ubimapa.ubirefer = '{$ubi}' and ubimapa.ubitipo = '{$tip}'";
//            echo $sq;
    $rs = $db->query($sq);
//    var_dump($rs->num_rows);
    if($rs->num_rows > 0){        
        while($ax = $rs->fetch_assoc()){
            if($ax['ubitipo'] == 'PI' && $ax['artrefer'] != ''){
                $response['err'] = true;
                $response['msg'] = "Ubicación no disponible";
            }else{
                $response['err'] = false;
                $response['msg'] = "";
            }
        }
    }else{
        $response['err'] = true;
        $response['msg'] = "Ubicación no existe";
    }
    return $response;
}
function validarUbiOrig($ubi,$art, $db){
    $sq = "select 
            ubimapa.ubirefer, 
            ubimapa.ubitipo, 
            ubimapa.ubisitu,
            artubiref.artrefer,
            artubiref.refid,
            stockubi.canti
            from ubimapa 
            left join artubiref on artubiref.ubirefer = ubimapa.ubirefer
						left join stockubi on stockubi.artrefer = artubiref.artrefer and stockubi.ubirefer = ubimapa.ubirefer
            where ubimapa.ubirefer = '{$ubi}'";
    $rs = $db->query($sq);
    if($rs->num_rows > 0){        
        while($ax = $rs->fetch_assoc()){
            if($ax['ubisitu'] == 'LL' && $ax['canti'] > 0){
                return false;
            }else{
                return true;
            }
        }
    }else{
        return true;
    }
}

$lerr = array();
$row = $_POST['data'];
//$db->autocommit(false);
$msgx = '';
    foreach($row as $k => $v){ 
        
        $art = trim(strtoupper($v[0]));
        $ubi = str_replace('-', '', trim(strtoupper($v[1])));
        $tipo = trim(strtoupper($v[2]));
                
        $onbd = getDataOnBd($v[3], $db);//obtiene el registro en bd de que corresponde a la fila     
//        var_dump($onbd);
        $arrDiff = array_diff_assoc($v, $onbd);
        if($arrDiff){
            $mater = getMasterData($art, $db);//obtiene el registro maestro en bd de que corresponde a la fila
            $origen = validarUbiOrig($onbd[1],$art, $db);
            if($origen){ //si la ubicación actual está vacia
                if($ubi == ''){ //si se borra la ubicación de destino intenta eliminar si es que está vacia
                    $del = "delete from artubiref where refid = {$v[3]}";
                    if(!$db->query($del)){ //no se pudo elinar el registro
//                        echo $del;
                        $msg = 'Error al guardar Configuración.';
                        $fgil = $k+1;
                        $msgx .= "Fila ".$fgil.': No se puede eliminar registro \n';
                        $lerr[] = 1;
                        continue;
                    }
                }else if($ubi != $mater[$onbd[1]]['ubirefer']){ //si se cambia la ubicación de destino
                    $destino = validarUbiDest($ubi,$tipo, $db); 
                    if($destino['err'] == 1){
                        $msg = 'Error al guardar Configuración.';
                        $fgil = $k+1;
                        $msgx .= "Fila ".$fgil.': '.$destino['msg'].' \n';
                        $lerr[] = 1;
                        continue;
                    }else{
                        $sqin = "update artubiref set ubirefer = '{$ubi}', ubitipo = '{$tipo}' where refid = {$v[3]}";
//                        echo $sqin;
                        $db->query($sqin);
                    }
                }
            }else{//la ubicación de origen no está vacia, entonces no se debe cambiar
                $msg = 'Error al guardar Configuración.';
                $fgil = $k+1;
                $msgx .= "Fila ".$fgil.': Ubicación de origen con Stock \n';
                $lerr[] = 1;
                continue;
            }
//            var_dump($lerr);
//            echo "hay diferencias";
        }else{
//            echo "no hay diferencias";
            continue;
        }
    }
    
    if(in_array(1, $lerr)){
        $msg = 'Error al guardar Configuración.\n'.$msgx;        
        $err = 1;
    }else{
        $db->commit();
        $msg = 'Configuración guardada';
        $err = 0;
    }
    

    echo json_encode(array('msg' => $msg, 'err' => $err));
    exit();
