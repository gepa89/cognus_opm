<?php

require ('../conect.php');

$db = new mysqli($SERVER,$USER,$PASS,$DB);
$sq = "SELECT 
        a.pedido, 
        b.tipac, 
        ped_multiref.multiref,
        if(
                b.tipac = 'RECE', 
                (SELECT count(1) as total from pedredet c where a.pedido = c.pedrefer group by c.pedrefer	) ,
                (SELECT count(1) as total from pedexdet c where a.pedido = c.pedexentre group by c.pedexentre	) 
        ) as cantsku,

        if(
                b.tipac = 'RECE', 
                (SELECT pedalemi from pedrecab d where a.pedido = d.pedrefer group by d.pedrefer	) ,
                (SELECT clirefer from pedexcab d where a.pedido = d.pedexentre group by d.pedexentre	) 
        ) as cliente

        FROM assig_ped a 
        inner join termi b on a.tercod = b.tercod
        LEFT JOIN ped_multiref on a.pedido=ped_multiref.pedido
        where a.tercod = '{$_POST['term']}' and a.st = 0";
//    echo $sq;
$rs = $db->query($sq);
$cc = 0;
while($row = $rs->fetch_assoc()){
    $cabeceras[$cc]['pedido'] = $row['pedido'];
    $cabeceras[$cc]['cantsku'] = $row['cantsku'];
    $cabeceras[$cc]['cliente'] = $row['cliente'];
    $cabeceras[$cc]['multiref'] = $row['multiref'];
    $cabeceras[$cc]['accion'] = '<a title="Ver Asignaciones" href="javascript:void(0);" onclick="delAssign('."'".$row['pedido']."'".",'".$_POST['term']."'".')"><span style="font-size:14px" class="glyphicon glyphicon-remove"></span></a>';
    $cc++;
}


echo json_encode(array( 'cab' => $cabeceras));

exit();
