<?php
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
//$config = [
//    'ashost' => '192.168.10.10',
//    'sysnr'  => '00',
//    'client' => '300',
//    'user'   => 'COMPRASC',
//    'passwd' => 'chacomer',
//    'trace'  => SapConnection::TRACE_LEVEL_OFF,
//];
$config = [
    'ashost' => '192.168.10.125',
    'sysnr'  => '00',
    'client' => '300',
    'user'   => 'comprasc',
    'passwd' => 'chacomer',
    'trace'  => SapConnection::TRACE_LEVEL_OFF,
];
$c = new SapConnection($config);
//echo $_POST['of'];
if(isset($_POST['of'])){
    $data = $file = array();

    $c = new SapConnection($config);

    $f = $c->getFunction('GOS_API_GET_ATTA_LIST');
    $options = [
        'rtrim' => true
    ];
    $result = $f->invoke([            
        'IS_OBJECT' => array('INSTID' => $_POST['of'], 'TYPEID' => 'BUS2010', 'CATID' =>'BO')
    ],$options);
    $data = $result;
    if(isset($data["ET_ATTA"])){
        $ct = 0;
        foreach($data["ET_ATTA"] as $k => $v){
            $ax = explode('.',$v["FILENAME"]);
            $filename[$ct]['file_id'] = $v["ATTA_ID"];
            $filename[$ct]['file_ext'] = $ax[1];
            $ct++;
        }
    }
    echo json_encode(array('files'=>$filename));
}


