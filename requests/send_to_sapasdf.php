<?php

require ('../conect.php');

use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;

//QAS
$config = [
    'ashost' => '192.168.10.125',
    'sysnr'  => '00',
    'client' => '300',
    'user'   => 'wmschac',
    'passwd' => 'chacomer',
    'trace'  => SapConnection::TRACE_LEVEL_OFF,
];
$c = new SapConnection($config); 


$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
$pd = $_POST['pedido'];
//echo $pd;
$pdstr = '';
if($pd != ''){
    $sq = "select a.*,b.*,c.artval, d.artserie from pedrecab a 
            inner join pedredet b on a.pedrefer = b.pedrefer 
            inner join arti c on b.artrefer = c.artrefer
            left join serati d on b.artrefer = d.artrefer and a.pedrefer = d.artped
            where a.pedrefer = '{$pd}' and a.pedresitu = 'UB'";
    $rs = $db->query($sq);
    $cc =0;
    while($ax = $rs->fetch_assoc()){
        $data[$cc]['POSICION'] = ''. str_pad($ax['pedpos'], 5 , '0', STR_PAD_LEFT);
        $data[$cc]['MATERIAL'] = ''.$ax['artrefer'];
        $data[$cc]['CENTRO'] = 'LCC1';
        $data[$cc]['ALMACEN_RECE'] = 'LDAL';
        $data[$cc]['PEDIDO'] = ''.$pd;
        $data[$cc]['CLASE_VALORACION'] = ''.$ax['artval'];
        $data[$cc]['CANTIDAD'] = (float)$ax['canprepa'];
        $data[$cc]['ALMACEN_SUMI'] = ''.$ax['pedalemi'];
        if($ax['artserie'] != ''){
            $data[$cc]['CHASIS'] = ''.$ax['artserie'];
        }
        
        $cc++;
    }
    
    try{
        $f = $c->getFunction('ZBM_MM_ENTRADA_MERCANCIA');
        echo "<br/>---------------------------------------<br/>";
        $options = [
            'rtrim' => true
        ];
        $result = $f->invoke([    
            'PEDIDO' => ''.$pd,
            'DETALLES' => $data
        ],$options);
        $ant = '';
        var_dump($result);
    }catch(SapException $ex) {
        echo 'aqui';
        var_dump($ex);
    } 
}

echo json_encode(array( 'cab' => $cabeceras));

exit();
