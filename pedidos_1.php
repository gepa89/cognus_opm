<?php
require ('conect.php');
include 'src/adLDAP.php';
use SAPNWRFC\Connection as SapConnection;
use SAPNWRFC\Exception as SapException;
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = str_pad($_REQUEST['pd'], 10, '0', STR_PAD_LEFT);
$db = new mysqli($SERVER,$USER,$PASS,$DB);
// var_dump($_POST);

$bloqueo = 0;
$motivo = '';
//revisa bloqueo de pedido
$sq = "select log_status,log_motivo from log_ped_status where log_pedido = '{$pd}'";
//echo $sq;
$rp = $db->query($sq);
while($ax = $rp->fetch_assoc()){    
    if($ax['log_status']){
        $bloqueo = $ax['log_status'];
        $motivo = $ax['log_motivo'];
    }
}


if($pd>= 4800000000){
    $sqhan = "select a.ebeln, a.werks, a.lgort, a.reslo, a.bukrs, a.werks, b.lgobe from sapabap1.ekpo a 
            left join sapabap1.t001l b on b.lgort = a.lgort and b.werks = a.werks
            where a.ebeln = '{$pd}' and a.bukrs in ('6000')";
}else{
    $sqhan = "select a.vstel, e.kunnr, a.vkorg, d.vgbel, b.name1, b.ort01, b.regio, b.stras, c.bezei, e.vsbed,f.vtext, g.lifnr, h.name1 as transp from sapabap1.likp a 
                left join sapabap1.lips d on d.vbeln = a.vbeln
                left join sapabap1.vbak e on e.vbeln = d.vgbel
                inner join sapabap1.kna1 b on e.kunnr = b.kunnr 
                left join sapabap1.t005u c on b.regio = c.bland
                left join sapabap1.tvsbt f on f.vsbed = e.vsbed and f.spras = 'S' and f.mandt = '300'
                left join sapabap1.vbpa g on g.vbeln = d.vgbel and parvw = 'SP'
                left join sapabap1.lfa1 h on h.lifnr = g.lifnr 
            where a.vbeln = '{$pd}'";
}
$rst = odbc_exec($prd, $sqhan);

while ($rw = odbc_fetch_object($rst)){
    if($pd>= 4800000000){
        $data['vstel'] = '';
        $data['kunnr'] = $rw->LGORT;
        $data['vgbel'] = '';
        $data['name1'] = '';
        $data['lifnr'] = '';
        $data['ort01'] = '';
        $data['regio'] = '';
        $data['stras'] = '';
        $data['bezei'] = '';
        $data['vsbed'] = '';
    }else{
        $data['vstel'] = $rw->VSTEL;
        $data['kunnr'] = $rw->KUNNR;
        $data['vgbel'] = $rw->VGBEL;
        $data['name1'] = $rw->NAME1;
        $data['lifnr'] = $rw->TRANSP;
        $data['ort01'] = $rw->ORT01;
        $data['regio'] = $rw->REGIO;
        $data['stras'] = $rw->STRAS;
        $data['bezei'] = $rw->BEZEI;
        $data['vsbed'] = $rw->VSBED." - ".$rw->VTEXT;
    }
    
}
try{
     $b= new SapConnection($config);
//    echo $data['vgbel']." - ";
    $f = $b->getFunction('ZREAD_TEXT');
    
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $result = $f->invoke([    
        'VID' => 'Z003',
        'VLANGUAGE' => 'S',
        'VNAME' => $data['vgbel'],
        'VOBJECT' => 'VBBK'
    ]);
    $ant = '';
    if(count($result["VLINES"])){
        foreach($result["VLINES"] as $k => $km){
            $data['msg'] .= utf8_encode(trim($km["TDLINE"]));
        }
    }
}catch(SapException $ex) {
//    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
//        echo $msg;
}
try{
     $b= new SapConnection($config);
//    echo $data['vgbel']." - ";
    $f = $b->getFunction('ZREAD_TEXT');
    
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $result = $f->invoke([    
        'VID' => 'Z001',
        'VLANGUAGE' => 'S',
        'VNAME' => $data['vgbel'],
        'VOBJECT' => 'VBBK'
    ]);
    $ant = '';
//    var_dump($result);
    if(count($result["VLINES"])){
        foreach($result["VLINES"] as $k => $km){
            $data['msg_ft'] .= utf8_encode(trim($km["TDLINE"]));
        }
    }
}catch(SapException $ex) {
//    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
//        echo $msg;
}
try{
     $b= new SapConnection($config);
//    echo $data['vgbel']." - ";
    $f = $b->getFunction('ZREAD_TEXT');
    
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $result = $f->invoke([    
        'VID' => 'Z002',
        'VLANGUAGE' => 'S',
        'VNAME' => $data['vgbel'],
        'VOBJECT' => 'VBBK'
    ]);
    $ant = '';
    if(count($result["VLINES"])){
        foreach($result["VLINES"] as $k => $km){
            $data['msg_ni'] .= utf8_encode(trim($km["TDLINE"]));
        }
    }
}catch(SapException $ex) {
//    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
//        echo $msg;
}
try{
     $b= new SapConnection($config);
//    echo $data['vgbel']." - ";
    $f = $b->getFunction('ZREAD_TEXT');
    
//    $f->setParameterActive('ARCHIVE_HANDLE', false);
//    $f->setParameterActive('VCLIENT', false);
    $result = $f->invoke([    
        'VID' => 'ZPRO',
        'VLANGUAGE' => 'S',
        'VNAME' => $data['vgbel'],
        'VOBJECT' => 'VBBK'
    ]);
    $ant = '';
//    var_dump($result);
    if(count($result["VLINES"])){
        foreach($result["VLINES"] as $k => $km){
            $data['msg_pro'] .= utf8_encode(trim($km["TDLINE"]));
        }
    }
}catch(SapException $ex) {
//    echo "<pre>";var_dump($ex);echo "</pre>";
        $err = 1;
        $msg = 'Error: ' . $ex->getMessage() . PHP_EOL;
//        echo $msg;
}
$pos = 1;
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width"  onload="startTime()">
        <style>
            td.details-control {
                background: url('images/details_open.png') no-repeat center center;
                cursor: pointer;
                width: 28px !important;
            }
            tr.shown td.details-control {
                background: url('images/details_close.png') no-repeat center center;
            }
            i.splashy-remove{
                cursor: pointer;
            }
            td.dt-nowrap { white-space: nowrap }
            .vcard > ul {
                list-style: none;
                margin-left: -45px;
                overflow: hidden;
            }
            .vcard.vc2 > ul {
                columns: 2;
            }
            .vcard.vc3 > ul {
                columns: 3;
            }
            
            .vcard.vc3 > ul > li {
                font-size: 11px !important;
                border-top: 1px dashed #dcdcdc;
                border-bottom: 0px dashed #dcdcdc;
            }
            .chat_msg_date {
                float: right;
            }
            .modal-header .close {
                margin-top: -22px;
            }
            .main_content li {
                line-height: 15px !important;
            }
            input {
                font-size: 10px !important;
            }
            
            .modal-header.lockdwn {
                background: red !important;
                color: white !important;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <!-- Button trigger modal -->
<!--                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                      Launch static backdrop modal
                    </button>-->
                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg bg-danger" role="document">
                        <div class="modal-content">
                            <div class="modal-header lockdwn">
                                <h5 class="modal-title" id="staticBackdropLabel">Bloqueo</h5>
  <!--                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>-->
                            </div>
                            <div class="modal-body  bg-danger">
                                <div class="card text-white bg-danger mt-3 p-2">
                                    OPERACIÓN BLOQUEADA - Debe ingresar código de autorizador para continuar.
                                </div><br/>
                                <div class="card text-white bg-danger mt-3 p-2" id="lbl_motivo">
                                </div><br/><br/>
                                <div class="row">                                    
                                    <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                        <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                            <input type="password" style="display:none;"  autocomplete="off" />
                                            <input type="text"  autocomplete="off" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="cod_super" name="cod_super" style="text-security:disc; -webkit-text-security:disc;" placeholder="Cód. de Autorización">
                                        </div> 
                                        
                                        <div class="col-lg-3 col-sm-3 col-xs-12 form-group">
                                            <div class="bg-danger">
                                            <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
                                                <button type="button" id="btn_unlock" onclick="unlock_document('<?PHP echo $pd;?>', 0)" class="btn btn-danger">Liberar</button>
                                            </div>
                                        </div> 
                                    </div>                                    
                                </div>
                            </div>
                            
                        </div>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <div class="vcard">
                                <ul>
                                    <li class="v-heading" style="display:block;">
                                        <b>Entrega #<?php echo $pd?> <span class="chat_msg_date"><i><?php echo date('d-m-Y', strtotime('now'))?></i> <i id="txt"></i></span></b>
                                    </li>
                                </ul>
                            </div>
                            <div class="vcard vc3">
                                <ul>
                                    <li>
                                            <span class="item-key">C. Cliente</span>
                                            <div class="vcard-item"><?php echo $data['kunnr']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Nombre</span>
                                            <div class="vcard-item"><?php echo $data['name1']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">C. Envío</span>
                                            <div class="vcard-item"><?php echo $data['vsbed']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Localidad</span>
                                            <div class="vcard-item"><?php echo $data['regio']." - ".$data['bezei'] ?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Dirección</span>
                                            <div class="vcard-item"><?php echo $data['stras']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Transporte</span>
                                            <div class="vcard-item"><?php echo $data['lifnr']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Z. Envío</span>
                                            <div class="vcard-item"><?php echo $data['ort01']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">__</span>
                                            <div class="vcard-item">__</div>
                                    </li>
                                    <li>
                                            <span class="item-key">Inst. Factura</span>
                                            <div class="vcard-item"><?php echo $data['msg_ft']?></div>
                                    </li>
                                    <li>
                                            <span class="item-key">Inst. Embarque</span>
                                            <div class="vcard-item"><?php echo $data['msg']?></div>
                                    </li>                                    
                                    <li>
                                            <span class="item-key">Inst. No Imprimible</span>
                                            <div class="vcard-item"><?php echo $data['msg_ni']?></div>
                                    </li>                                   
                                    <li>
                                            <span class="item-key">Inst. Promocional</span>
                                            <div class="vcard-item"><?php echo $data['msg_pro']?></div>
                                    </li>
                                </ul>
                            </div>
<!--                            <div class="vcard">
                                <ul>
                                    <li>
                                            <span class="item-key">Z. Envío</span>
                                            <div class="vcard-item">test</div>
                                    </li>
                                    <li>
                                            <span class="item-key">Inst. Factura</span>
                                            <div class="vcard-item">test</div>
                                    </li>
                                    <li>
                                            <span class="item-key">Inst. Embarque</span>
                                            <div class="vcard-item">test</div>
                                    </li>
                                </ul>
                            </div>-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <div class="col-sm-4 col-md-4">
                                    <input autocomplete="on" class="form-control" id="articulo" name="articulo" type="text" placeholder="Artículo">
                                    <input  id="pexp" name="pexp" type="hidden" value="<?php echo $data['vstel']?>">
                                    <input  id="flag_input" name="flag_input" type="hidden" value="0">
                            </div>
                            <div class="col-sm-2 col-md-2">
                                    <input autocomplete="on" class="form-control" id="cantidad" name="cantidad" type="number" placeholder="Cantidad" value="1">
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <?php 
                                    $qry = "select ca_caja as abierto from log_cajas where ca_emp = '{$pd}' and ca_st = 0 order by ca_caja desc limit 1";
//                                    echo $qry;
                                    $rs = $db->query($qry);
                                    $rsx = $rs->fetch_assoc();
                                    if(is_null($rsx["abierto"])){
                                        $qryl = "select ca_caja as last from log_cajas where ca_emp = '{$pd}' order by ca_caja desc limit 1";
                                        $rsl = $db->query($qryl);
                                        $rsxl = $rsl->fetch_assoc();
                                        if(is_null($rsxl['last'])){
                                            $caja = 1;
                                        }else{
                                            $caja = $rsxl['last']+1;
                                        }
                                        
                                    }else{
                                        $caja = $rsx["abierto"];
                                    }
                                    $cjLbl = str_pad($caja, 5, '0', STR_PAD_LEFT);
                                ?>
                                <input autocomplete="on" class="form-control" id="caja" name="caja" disabled="disabled" type="text" value="CAJA-<?php echo $cjLbl?>">
                            </div>                   
                            <div class="col-sm-2 col-md-2">
                                <input autocomplete="on" value="Cerrar Caja" style="" onclick="closeBox()" class="btn form-control btn-default btn_ex" type="button">
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <input autocomplete="on" value="Abrir Caja" style="" onclick="openBox()" class="btn form-control btn-default btn_ex" type="button">
                            </div>
                            
                        </div>                            
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <table id="example" class="display table table-striped table-responsive table-hover" style="width:100%; font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Pos</th>
                                        <th>Entrega</th>                                        
                                        <th>Material</th>                                        
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>UM</th>
                                        <th>Caja</th>
                                        <th>Peso</th>
                                        <th>Ubicación</th>
                                        <th>Bultos</th>
                                        <th>Desc. Caja</th>
                                        <th>Sel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    
                                    
                                        $status = array(1 => "Cantidad insuficiente",
                                                        2 => "Cantidad en exceso",
                                                        3 => "No preparado.",
                                                        4 => "Producto equivocado",
                                                        5 => "No tiene EAN",
                                                        6 => "EAN incompleto o incorrecto.",
                                                        7 => "No lee EAN sistema nuevo",
                                                        8 => "No lee EAN sistema nuevo y ADAIA");
                                        $qry = "select * from log_material a left join row_status c on a.lg_c_emp = c.err_ent and a.lg_c_pos_in = c.err_pos left join log_cajas b on a.lg_c_caja = b.ca_caja and a.lg_c_emp = b.ca_emp where a.lg_c_emp = '{$pd}' order by lg_ts asc";
//                                        echo $qry;
                                        $rs = $db->query($qry);
                                        while($aux = $rs->fetch_assoc()){
//                                            lg_c_emp
//                                            lg_c_mat
//                                            lg_c_cant
//                                            lg_c_caja
//                                            lg_c_caja_st
//                                            lg_c_caja_ps
//                                            lg_c_caja_ubi
//                                            lg_c_caja_bul
//                                            lg_ts
//                                            lg_c_user

                                            echo '<tr id="'.$aux['lg_id'].'">';
                                            echo '<td><i class="splashy-remove"></i>'."</td>";
                                            echo "<td>".$aux['lg_c_pos_in']."</td>";
                                            echo "<td>".$aux['lg_c_emp']."</td>";
                                            echo "<td>".$aux['lg_c_mat']."</td>";
                                            echo "<td>".$aux['lg_c_mat_desc']."</td>";
                                            echo "<td>".$aux['lg_c_cant']."</td>";
                                            echo "<td>".$aux['lg_c_um']."</td>";
                                            echo "<td>CAJA-".str_pad($aux['ca_caja'], 5, '0', STR_PAD_LEFT)."</td>";
                                            echo "<td>".$aux['ca_peso']."</td>";
                                            echo "<td>".$aux['ca_ubi']."</td>";
                                            echo "<td>".$aux['ca_bulto']."</td>";
                                            echo "<td>".$aux['ca_desc']."</td>";                                            
                                            echo "<td>".'<select class="form-control" onchange="saveStRow('."'".$aux['lg_c_emp']."','".$aux['lg_c_pos_in']."'".')" id="sel_'.$aux['lg_c_emp'].'_'.$aux['lg_c_pos_in'].'">'.
                                                    '<option>'.'</option>';
                                                    foreach($status as $k => $v){
                                                        $sel = '';
                                                        if($k == $aux['err_val']){
                                                            $sel = 'selected="selected"';
                                                        }
                                                        echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
                                                    }
                                            echo '</select>'."</td>";
                                            echo "</tr>";
                                            $pos = $aux['lg_c_pos_in']+1;
                                        }
                                    ?>
                                </tbody>
<!--                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Pos</th>
                                        <th>Entrega</th>
                                        <th>Material</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Caja</th>
                                        <th>Peso</th>
                                        <th>Ubicación</th>
                                        <th>Bultos</th>
                                        <th>Desc. Caja</th>
                                    </tr>
                                </tfoot>-->
                            </table>
                            <input autocomplete="on" type="hidden" id="cCounter" value="<?php echo $pos?>">
                        </div>                            
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <div class="col-sm-4 col-md-4"></div>                 
                            <div class="col-sm-2 col-md-2">
                                <input autocomplete="on" value="Verificar" style="" onclick="checkDoc()" class="btn form-control btn-default btn_ex" type="button">
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <input autocomplete="on" value="Cerrar Documento" style="" id="closeDoc" disabled="disabled" onclick="closeDoc()" class="btn form-control btn-default btn_ex" type="button">
                            </div>
                            <div class="col-sm-4 col-md-4"></div>                 
                        </div>                            
                    </div>
                </div>
            </div>
            <div  class="modal fade bd-example-modal-lg" id="closeBoxModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form">
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="peso" placeholder="Peso">
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="ubicacion" placeholder="Ubicación">
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                            <input autocomplete="on" type="number" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="bulto" placeholder="Bulto">
                                    </div>
                                    <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="descripcion" placeholder="Descripción">
                                    </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="clBox()" class="btn btn-primary">Cerrar Caja</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div  class="modal fade bd-example-modal-lg" id="openBoxModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form">
                                    <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                        <select  class="col-lg-12 col-sm-12 col-xs-12 form-control" id="selCaja">
                                            <!--<option>Cajas</option>-->
                                            <?php 
                                                $qry = "select * from log_cajas where ca_st = 1 and ca_emp = '{$pd}'";
                                                $rsoc = $db->query($qry);
                                                while($ax = $rsoc->fetch_assoc()){
                                                    echo '<option value="'.$ax['ca_caja'].'">CAJA-'.str_pad($ax['ca_caja'], 5, '0', STR_PAD_LEFT).'</option>';
                                                }
                                            ?>
                                                
                                        </select>
                                    </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="opnBox()" class="btn btn-primary">Abrir Caja</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        include 'sidebar.php';
        include 'js_in.php';
    ?>
        <script>
            var t;
//            function delRow(id){
//                t.row( $(this).parents('tr[id="'+id+'"]') );
//                var rowNode = row.node();
//                row.remove();
//            }
////            
            function padLeft(nr, n, str){
                return Array(n-String(nr).length+1).join(str||'0')+nr;
            } 
           function closeDoc(){
                var exp = $("#pexp").val();
                $.ajax({ 
                    url: 'cerrar_docu.php', 
                    type: 'POST',
                    data: {
                        exp: exp,
                        codigo:'<?php echo $pd?>',
                        usuario:'<?php echo $_SESSION["user"]?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 1){
                            alert(dt.msg);
                        }else{    
                            alert(dt.msg);
                            window.location = 'dashboard.php';
                        }
                        
                    }
                });
            }
            function checkDoc(){                
                $.ajax({ 
                    url: 'check_doc.php', 
                    type: 'POST',
                    data: {
                        codigo:'<?php echo $pd?>',
                        usuario:'<?php echo $_SESSION["user"]?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var flg = false;
                        var cntnt = '';
                        $.each(dt.dat, function(idx, vx){
                            if(vx.err == 1){
                                flg = true;
                                cntnt += vx.msg+' \n';
                            }
                        });
                        if(flg == true){
                            alert(cntnt);
                            $("#closeDoc").attr('disabled', true);
                        }else{
                            alert('Se puede cerrar el documento');
                            $("#closeDoc").attr('disabled', false);
                        }
                        
                    }
                });
            }
            function clBox(){
                var caja = $('input[id="caja"]').val();
                var peso = $('input[id="peso"]').val();
                var bulto = $('input[id="bulto"]').val();
                var ubicacion = $('input[id="ubicacion"]').val();
                var descripcion = $('input[id="descripcion"]').val();
                var flg = 0;
                if(peso == '' || bulto == '' || ubicacion == ''){
                    flg = 1;
                    alert("Debe completar los campos obligatorios")
                }
                if(flg == 0){
                    $.ajax({ 
                        url: 'close_box.php', 
                        type: 'POST',
                        data: {
                            dl_id:caja,
                            peso:peso,
                            bulto:bulto,
                            ubicacion:ubicacion,
                            descripcion:descripcion,
                            ent:'<?php echo $pd?>',
                            usuario:'<?php echo $_SESSION["user"]?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            if(dt.err == 1){
                                alert(dt.msg);
                            }else{    
                                alert(dt.msg);
                                var ls = 'CAJA-'+dt.res;
                                $('#caja').val(ls);
                                $('#closeBoxModal').modal('hide');
                                window.location = 'pedidos.php?pd=<?php echo $pd?>';
                            }
                        }
                    });
                }                
            }
            function opnBox(){
                var caja = $('#selCaja').val();
                var flg = 0;
                if(caja == ''){
                    flg = 1;
                    alert("Debe seleccionar una caja")
                }
                if(flg == 0){
                    $.ajax({ 
                        url: 'open_box.php', 
                        type: 'POST',
                        data: {
                            dl_id:caja,
                            pedido:'<?php echo $pd?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            if(dt.err == 1){
                                alert(dt.msg);
                            }else{    
                                alert(dt.msg);
                                var ls = 'CAJA-'+dt.res;
                                $('#caja').val(ls);
                                $('#openBoxModal').modal('hide');
                            }
                        }
                    });
                }                
            }
            function openBox(){
               
                $(".modal-title").empty().append('Abrir caja')
                $('input[id="peso"]').val('');
                $('input[id="bulto"]').val('');
                $('input[id="ubicacion"]').val('');
                $('input[id="descripcion"]').val('');
                $('#openBoxModal').modal('show');
            }
            function closeBox(){
                var caja = $('input[id="caja"]').val();
                $(".modal-title").empty().append('Cerrar '+caja)
                $('#closeBoxModal').modal('show');
            }
            function lock_vw(msg){
                $("#lbl_motivo").empty().append(msg);
                
                $('#staticBackdrop').modal('show');
                $('#cod_super').focus();
            }
            function lock_document(pd, msg){
                $.ajax({ 
                    url: 'lock_doc.php', 
                    type: 'POST',
                    data: {
                        pd:pd,
                        msg:msg
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 0){
                            lock_vw(dt.msg);
                        }
                    }
                });
            }
            function unlock_document(pd, tp){
                var auth = $("#cod_super").val();
                if(auth == ''){
                    alert("Debe leer el código de autozante para liberar.");
                }else{
                    $.ajax({ 
                        url: 'unlock_doc.php', 
                        type: 'POST',
                        data: {
                            pd:pd,
                            auth:auth,
                            tp:tp
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            
                            if(dt.err == 0){           
                                $("#cod_super").val('');
                                $('#staticBackdrop').modal('hide');
                                $('input[id="articulo"]').val('');
                                $('input[id="articulo"]').focus();
                            }else{
                                alert(dt.msg);
                            }
                        }
                    });      
                }
            }
            $(document).ready(function() {
                var bloqueo = '<?PHP echo $bloqueo; ?>';
                var motivo = '<?PHP echo $motivo; ?>';
                if(bloqueo == 1){
                    lock_vw(motivo);
                }
                $('#articulo').focus();
                $('#articulo').scannerDetection({
                    onComplete:function(){
                        $('#articulo').focus();
                        var value = $('input[id="cantidad"]').val();
                        
                        if(value != ''){
                            $('input[id="flag_input"]').attr('value','1');
                            adRow();
                        }else{
                            alert('Debe ingresar cantidad');
                        }
                        $('#articulo').val('');
                        $('#articulo').focus();
                    }
                });
                $('#cod_super').scannerDetection({
                    onComplete:function(){
                        
                        $('#btn_unlock').focus();
                        unlock_document('<?PHP echo $pd;?>', 1);
                        //$('#btn_unlock').click();
                       
                    }
                });
                t = $('#example').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
                    "order": [[ 1, "desc" ]],
                    "language": {
                        "processing":     "Procesando...",
                        "lengthMenu":     "Mostrar _MENU_ registros",
                        "zeroRecords":    "No se encontraron resultados",
                        "emptyTable":     "Ningún dato disponible en esta tabla",
                        "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
                        "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "infoPostFix":    "",
                        "search":         "Buscar:",
                        "Url":            "",
                        "infoThousands":  ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first":    "Primero",
                            "last":     "Último",
                            "next":     "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                });
                $(document).on( 'click', 'i.splashy-remove', function () {
                    var rowDel = $(this).parents('tr').attr('id');
                    var dtrow = t.row( $(this).parents('tr') );
                    $.ajax({ 
                        url: 'del_row.php', 
                        type: 'POST',
                        data: {
                            dl_id:rowDel,
                            usuario:'<?php echo $_SESSION["user"]?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            if(dt.err == 1){
                                alert(dt.msg);
                            }else{                 
                                console.log(dtrow);
                                dtrow.remove().draw();
                            }
                        }
                    });
                    
                } );
            } );
            function adRow(){
                var counter = 1;
                var mat = $('input[id="articulo"]').val();
                var cant = $('input[id="cantidad"]').val();
                var caja = $('input[id="caja"]').val();
                var flag = $('input[id="flag_input"]').val();
                var ent = '<?php echo $pd?>';
                var pos_in = $("#cCounter").val();
                $.ajax({ 
                    url: 'validar_mat.php', 
                    type: 'POST',
                    data: {
                        codigo:ent,
                        material:mat,
                        cantidad:cant,
                        pos_in:pos_in,
                        caja: caja,
                        flag: flag,
                        usuario:'<?php echo $_SESSION["user"]?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var pos = $("#cCounter").val();
                        if(dt.err == 1){
                            //alert(dt.msg);
                            lock_document('<?php echo $pd?>', dt.msg)
                            //lock_vw(dt.msg);
                            $('input[id="articulo"]').val('');
                            //$('input[id="articulo"]').focus();
                        }else{            
                            //<th>Entrega</th>
                            //<th>Material</th>
                            //<th>Descripción</th>
                            //<th>Cantidad</th>
                            //<th>Caja</th>
                            //<th>Peso</th>
                            //<th>Ubicación</th>
                            //<th>Bultos</th>
                            t.row.add( [
                                dt.dat.btn,
                                dt.dat.Pos,
                                dt.dat.Ent,
                                dt.dat.Mat,
                                dt.dat.Des,
                                dt.dat.Can,
                                dt.dat.um,
                                dt.dat.Caj,
                                dt.dat.Pes,
                                dt.dat.Ubi,
                                dt.dat.Bul,
                                dt.dat.DsCa,
//                                - Cantidad insuficiente
//                                - Cantidad en exceso
//                                - No preparado.
//                                - Producto equivocado
//                                - No tiene EAN
//                                - EAN incompleto o incorrecto.
//                                - No lee EAN sistema nuevo
//                                - No lee EAN sistema nuevo y ADAIA

                                '<select class="form-control" onchange="saveStRow('+"'"+dt.dat.Ent+"','"+dt.dat.Pos+"'"+')" id="sel_'+dt.dat.Ent+'_'+dt.dat.Pos+'">'+
                                        '<option>'+'</option>'+
                                        '<option value="1">'+"Cantidad insuficiente"+'</option>'+
                                        '<option value="2">'+"Cantidad en exceso"+'</option>'+
                                        '<option value="3">'+"No preparado."+'</option>'+
                                        '<option value="4">'+"Producto equivocado"+'</option>'+
                                        '<option value="5">'+"No tiene EAN"+'</option>'+
                                        '<option value="6">'+"EAN incompleto o incorrecto."+'</option>'+
                                        '<option value="7">'+"No lee EAN sistema nuevo"+'</option>'+
                                        '<option value="8">'+"No lee EAN sistema nuevo y ADAIA"+'</option>'+
                                '</select>'
                            ] ).node().id = dt.dat.ID;
                            t.draw( false );                            
                            $("#cCounter").val(parseInt(pos)+1);
                        }
                        $('input[id="flag_input"]').attr('value','0');
                        $('input[id="articulo"]').val('');
                        $('input[id="cantidad"]').val('1');
                        $('input[id="articulo"]').focus();
                    }
                });
            }
            function saveStRow(ent, pos){
                var val = document.getElementById("sel_"+ent+"_"+pos+"").value;
                $.ajax({ 
                    url: 'save_row_st.php', 
                    type: 'POST',
                    data: {
                        ent:ent,
                        pos:pos,
                        val:val,
                        usuario:'<?php echo $_SESSION["user"]?>'
                    },
                    success: function (data) {
                        
                    }
                });
            }
            function startTime() {
                var today = new Date();
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();
                m = checkTime(m);
                s = checkTime(s);
                document.getElementById('txt').innerHTML =
                h + ":" + m + ":" + s;
                var t = setTimeout(startTime, 500);
            }
            function checkTime(i) {
                if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
                return i;
            }
            $('input[id="articulo"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var value = $('input[id="articulo"]').val();
                    if(value != ''){
                        $('input[id="cantidad"]').focus();
                    }else{
                        alert('Debe ingresar el Cód. de Material o EAN');
                    }
                }
            });
            $('input[id="cantidad"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var artv = $('input[id="articulo"]').val();
                    if(artv != ''){
                        var value = $('input[id="cantidad"]').val();
                        if(value != ''){
                            adRow();
                        }else{
                            alert('Debe ingresar cantidad');
                        }
                    }else{
                        $('input[id="articulo"]').focus();
                    }
                    
                }
            });
        </script>
    </body>
</html>
<?php require ('closeconn.php');?>