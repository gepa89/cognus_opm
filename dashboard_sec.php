<?php
require ('conect.php');
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER,$USER,$PASS,$DB);
//$db = new mysqli($SERVER,$USER,$PASS,$DB);
//var_dump($_SESSION);
//$sq = "select * from log_contrato where prt_prov = '{$_SESSION['user_cd']}' and prt_status = 0";
//echo $sq;
//$rs = $db->query($sq);
//$tt = $rs->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            td.details-control {
                background: url('images/details_open.png') no-repeat center center;
                cursor: pointer;
                width: 28px !important;
            }
            tr.shown td.details-control {
                background: url('images/details_close.png') no-repeat center center;
            }
            .modal-header .close {
                margin-top: -22px;
            }
            .hddn{
                display: none;
            }
            div[id^='etPrev']{
                display: block;
                position: relative;
                width: 480px;
                height: 195px;
                padding: 5px;
                background: #fff;
                border: 1px dashed #dcdcdc;
                border-radius: 10px;
                overflow: hidden;
            }
            
            .vcard .v-heading {
                background: #fff;
                font-weight: 700;
            }
            .vcard .vcard-item {
                margin-left: 62px;
            }
            .vcard{                
                position: relative;
                z-index: 0;
            }
            
            .vcard.v3 {
                padding: 3px;
                border-bottom: 0px dashed #dcdcdc;
                float: right;
                position: absolute;
                bottom: 22px;
                right: 25px;
            }
            .vcard > ul {
                list-style: none;
                margin: 0 !important;
                position: relative;
                overflow: hidden;
                left: -24px;
            }
            .vcard.v1 > ul > li:first-of-type {
                font-size: 20px;
                padding: 3px;
                border-top: 0px dashed #dcdcdc;
            }
            .vcard > ul > li:first-of-type {                
                border-top: 0px dashed #dcdcdc;
            }
            
            .vcard.v3 > ul > li:first-of-type {
                font-size: 15px;
                padding: 3px;
                border: 0px dashed #dcdcdc;
            }
            .vcard > ul > li:last-of-type {
                border-bottom: 1px dashed #dcdcdc;
            }
            .vcard > ul > li {
                padding: 2px;
                border-bottom: 1px dashed #dcdcdc;
                overflow: hidden;
                font-size: 12px;
            }#barcode, #barcodeTr, .barcode {
                position: relative;
                float: right;
                top: -95px;
                right: 0;
            }
            .barcode2 {
                position: relative !important;
                float: right !important;
                top: -90px !important;
                right: 0 !important;
            }
            #toPrint{
                display: block;
                position: relative;
                bottom:0;
                right:0;
            }
            #etiquetaModal .modal-dialog .modal-content .modal-body{
                max-height: 440px !important;
                overflow: hidden !important;
            }
/*            #manifiestoModal .modal-dialog .modal-content .modal-body #mncontent{
                max-height: 300px !important;
                overflow-y: auto !important;
            }*/
            #prMnArea, #prBlArea{
                display:none;
            }
            .blEntTbl{
                display: table;
                width: 100%;
                
            }
            .blEntTbl > li{
                display:inline-block;
                text-align: center;
                width:15%;
                -webkit-box-shadow: inset 0px 1px 0px 0px #fff;
                box-shadow: inset 0px 1px 0px 0px #fff;
                text-align: center;
                list-style: none;
                padding: 5px 0; 
                margin: 0 5px 10px;
                background: #f9f9f9;
                background: -moz-linear-gradient(top, #f9f9f9 0%, #efefef 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9f9f9), color-stop(100%,#efefef));
                background: -webkit-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: -o-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: -ms-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#efefef',GradientType=0 );
                border: 1px solid #e2e2e2;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                -ms-border-radius: 6px;
                border-radius: 6px;
            }
            .blInClose{
                display: block;
                position: relative;
                float:right;
                top:0;
                right: 5px;
            }
            .sidebar_info2 {
                width: 180px;
                position: relative;
                left: 30px;
                bottom: 10px;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">     
                            
                            
                            <div class="row">
                                <div class="col-lg-4 col-sm-4 col-xs-12">
                                    <h3 class="heading">Entrega</h3>
                                    <div class="col-lg-12 col-sm-12 col-xs-12">
                                        <?php // var_dump($_SESSION);?>
                                        <div class="form-horizontal">
                                            <div class="col-lg-12">
                                                <div class="form-group">                                                
                                                    <input autocomplete="on" type="text"  class="form-control" id="inputEntrega" placeholder="Entrega">
                                                </div>
                                            </div>
                                            <div class="col-lg-offset-4 col-lg-12">
                                                <button type="button" onclick="sndEntrega()" class="btn btn-default btn_ex">Controlar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-8 col-xs-12">
                                    <h3 class="heading">Herramientas</h3>
                                    <ul class="dshb_icoNav clearfix">
                                        <li><a href="javascript:void(0)" onclick="swModal('vl02nModal')" style="background-image: url(img/gCons/delete-item.png);">VL02N</a></li>
                                        <li><a href="javascript:void(0)" onclick="swModal('contabilizarModal')" style="background-image: url(img/gCons/copy-item.png);">Contabilización</a></li>
                                        <li><a href="javascript:void(0)" onclick="swModal('etiquetaModal')" style="background-image: url(img/gCons/connections.png);">Etiquetas</a></li>
                                        <li><a href="javascript:void(0)" onclick="swModal('etTransfModal')" style="background-image: url(img/gCons/container.png);"> Transferencia</a></li>
                                        <li><a href="javascript:void(0)" onclick="swModal('closeBoxModal')" style="background-image: url(img/gCons/connected.png);"> Bultos</a></li>
                                        <li><a href="javascript:void(0)" onclick="swModal('tiemposModal')" style="background-image: url(img/gCons/arrow-round.png);">Tiempos</a></li>
                                    </ul>
                                </div>
                                
                            </div>
                            <br/><br/><br/><br/><br/><br/>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-xs-12">
                                    <div class="sidebar_info2" style="width: 551px !important; margin:0 auto;">
                                        <ul class="list-unstyled">
                                            <li style="width: 30.108% !important; margin-right:15px !important;position:relative; display:inline-block !important;">
                                                <span id='lblPed2' class="act act-success"></span>
                                                <strong>Pedidos</strong>
                                            </li>
                                            <li style="width: 30.108% !important; margin-right:15px !important;position:relative; display:inline-block !important;">
                                                <span id='lblMat2' class="act act-success"></span>
                                                <strong>Materiales</strong>
                                            </li>
                                            <li style="    border-bottom: 1px dashed #ccc;width: 30.108% !important; margin-right:15px !important;position:relative; display:inline-block !important;">
                                                <span id='lblCant2' class="act act-success"></span>
                                                <strong>Cantidad</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-lg" id="closeBoxModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
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
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="btTransf" placeholder="Transferencia">
                                    </div>
                            </div>
                            <div style="clear:both;"></div>
                            <div id="btContents">
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" onclick="clBox()" class="btn btn-primary">Cerrar Caja</button>-->
                            <button type="button" onclick="clsBulto()" class="btn btn-default"><i class="icon-adt_trash"></i></button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
                <div  class="modal fade bd-example-modal-sm" id="tiemposModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Registro de tiempo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                        <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                            <?php
                                                $qr_get ="select * from log_cierre where ct_user = '{$_SESSION['user']}' and ct_st = 1 order by ct_ts desc limit 1";
//                                                echo $qr_get;
                                                $rr = $db->query($qr_get);
                                                $rx = $rr->fetch_assoc();
                                                $lst_ent = $rx['ct_empaque'];
//                                                var_dump($rx);
                                            ?>
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="inTimeEntrega" value="<?php echo $rx['ct_empaque']?>" placeholder="Entrega">
                                        </div>
                                        <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="inTimeCentro" value="" placeholder="Expedición">
                                        </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="btnTimes()" class="btn btn-primary">Registrar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-lg" id="vl02nModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalle de Entrega</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                        <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                            
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 youcol-xs-12 form-control" id="vlnEntrega" value="" placeholder="Entrega">
                                        </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12 col-xs-12 form-group" id="tbl_cnt" style="display:none;">
                                    <input autocomplete="on" type="hidden" id="vlnHdnEntrega" >
                                        <table class="table table-bordered table-striped" id="smpl_tbl">
                                            <thead>
                                                <tr>
                                                    <th class="table_checkbox"><input autocomplete="on" id="select_rows_all" name="select_rows_all" class="select_rows" data-tableid="smpl_tbl" type="checkbox"></th>
                                                    <th>Posición</th>
                                                    <th>Material</th>
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Picking</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clsDetail()" class="btn btn-default"><i class="icon-adt_trash"></i></button>                                
                                <button type="button" disabled="disabled" id="btnPk" onclick="btnVlnSv()" class="btn btn-primary">Guardar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-sm" id="contabilizarModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Contabilización de Entrega</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                        <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                            <?php
                                                $qr_get ="select * from log_cierre where ct_user = '{$_SESSION['user']}' and ct_st = 1 order by ct_ts desc limit 1";
//                                                echo $qr_get;
                                                $rr = $db->query($qr_get);
                                                $rx = $rr->fetch_assoc();
                                                $lst_ent = $rx['ct_empaque'];
//                                                var_dump($rx);
                                            ?>
                                            <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="inEntrega" value="<?php echo $rx['ct_empaque']?>" placeholder="Entrega">
                                        </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="btnContab()" class="btn btn-primary">Contabilzar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-lg" id="etiquetaModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Etiquetas </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etEntrega" value="<?php echo $lst_ent?>" placeholder="Entrega">
                                    </div>
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="number" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etCantidad" value="" placeholder="Cantidad">
                                    </div>
                                    <div class="formSep col-sm-6 col-md-6 col-xs-12">
                                        <label>Cliente</label>
                                        <input autocomplete="on" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etCliente" type="text">
                                        <input autocomplete="on"  id="etCodCliente" type="hidden">
                                        <input autocomplete="on"  id="etEmpresa" type="hidden">
                                    </div>
                                    <div class="formSep col-sm-6 col-md-6 col-xs-12">
                                        <label>Localidad</label>
                                        <input autocomplete="on" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etLocalidad" type="text">
                                    </div>
                                    <div class="formSep col-sm-6 col-md-6 col-xs-12">
                                        <label>Zona Envio</label>
                                        <input autocomplete="on" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etZonaEnvio" type="text">
                                    </div>
                                    <div class="formSep col-sm-6 col-md-6 col-xs-12">
                                        <label>Transporte</label>
                                        <input autocomplete="on" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTransporte" type="text">
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12 col-xs-12 hddn" id="vwEtiqueta">
                                    <div id="etPrev">                                        
                                        <div class="vcard v1">
                                            <ul>
                                                <li class="v-heading" id="etpvCliente">
                                                </li>
                                                <li>
                                                        <span class="item-key">TRANSP.</span>
                                                        <div class="vcard-item" id="etpvTransp"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">ZONA E.</span>
                                                        <div class="vcard-item" id="etpvZona"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">CIUDAD</span>
                                                        <div class="vcard-item" id="etpvCiudad"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">ENTREGA</span>
                                                        <div class="vcard-item" id="etpvEntrega"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">COD.CLI</span>
                                                        <div class="vcard-item" id="etpvCodCLi"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">FECHA</span>
                                                        <div class="vcard-item" id="etpvFecha"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">C.BULTOS</span>
                                                        <div class="vcard-item" id="etpvBulto"></div>
                                                </li>
                                            </ul> 
                                        </div>
                                        <div id="barcode"></div>
                                        <div class="vcard v3">
                                            <ul>
                                                <li class="v-heading" id="etpvEmpresa">
                                                    CHACOMER S.A.E.
                                                </li>
                                            </ul>                                            
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div id="toPrint" class=""></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clsEtiqueta()" class="btn btn-default"><i class="icon-adt_trash"></i></button>
                                <button type="button" id="previewEt" disabled="disabled" onclick="viewEtiqueta()" class="btn btn-primary"><i class="splashy-zoom"></i> Vista Previa</button>
                                <button type="button" id="printEt" disabled="disabled"  onclick="impEtiqueta()" class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-lg" id="etTransfModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Etiquetas para Transferencia</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTrEntrega" value="" placeholder="Entrega">
                                    </div>
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTrCant" value="" placeholder="Cantidad">
                                    </div>
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTrTANSP" value="" placeholder="Transpore">
                                    </div>
                                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTrCIUDAD" value="" placeholder="Ciudad">
                                    </div>
                                    <input autocomplete="on" id="etTrEntrega2" type="hidden">
                                    <input autocomplete="on" id="etTrCant2" type="hidden">
                                    <input autocomplete="on" id="etTrLGORT" type="hidden">
                                    <input autocomplete="on" id="etTrRESLO" type="hidden">
                                    <input autocomplete="on" id="etTrEMP" type="hidden">
                                    <input autocomplete="on" id="etTrBUKRS" type="hidden">
                                    <input autocomplete="on" id="etTrLGOBE" type="hidden">                                    
                                    <input autocomplete="on" id="etTrBULKS" type="hidden">
                                </div>
                                <div style="clear:both;"></div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 hddn" id="vwEtiquetTra">
                                    <div id="etTrPrev">                                        
                                        <div class="vcard v1">
                                            <ul>
                                                <li class="v-heading" id="etTrpvCliente">
                                                </li>                                                
                                                <li>
                                                        <div class="v-heading" id="etTrpvLGOBE"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">Transporte</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvTANSP"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">Ciudad</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvCIUDAD"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">Alm. Emisor</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvRESLO"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">Nro. Pedido</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvBUKRS"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">FECHA</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvFecha"></div>
                                                </li>
                                                <li>
                                                        <span class="item-key">C.BULTOS</span>
                                                        <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvBulto"></div>
                                                </li>
                                            </ul> 
                                        </div>
                                        <div id="barcodeTr"></div>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div id="toPrintTr" class=""></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clsEtiquetTra()" class="btn btn-default"><i class="icon-adt_trash"></i></button>
                                <button type="button" id="previewEtTr" disabled="disabled" onclick="viewEtiquetTra()" class="btn btn-primary"><i class="splashy-zoom"></i> Vista Previa</button>
                                <button type="button" id="printEtTr" disabled="disabled"  onclick="impEtiquetTra()" class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div  class="modal fade bd-example-modal-lg" id="manifiestoModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Manifiesto de Carga</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="mnEntrega" value="" placeholder="Entrega">
                                        <input autocomplete="on" type="hidden" id="mnNroManifiesto" value="">
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="mnChofer" value="" placeholder="Chofer">
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="mnChapa" value="" placeholder="Chapa">
                                    </div>
                                    <div class="col-lg-12 col-sm-12 col-xs-12 form-group">
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="mnObs" value="" placeholder="Observación">
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12 col-xs-12" id="mncontent">
                                    
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clsManifiesto()" id="clsBtnMn" class="btn btn-default"><i class="icon-adt_trash"></i></button>
                                <button type="button" onclick="btnSvManifiesto()" class="btn btn-primary">Guardar</button>
                                <button type="button" onclick="impManifiesto()" id="printMn" disabled="disabled" class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal fade bd-example-modal-lg" id="boletaModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Generar boleta</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form">
                                    <div class="col-lg-4 col-sm-4 col-xs-12 form-group">                                            
                                        <input autocomplete="on" type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" id="blEntrega" value="" placeholder="Entrega">
                                        <input autocomplete="on" type="hidden" id="blNroManifiesto" value="">
                                    </div>
                                    <div class="col-lg-12 col-sm-12 col-xs-12 form-group">                                            
                                        <ul class="blEntTbl ">
                                        </ul>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12 col-xs-12" id="blcontent">
                                    
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clsBoleta()" id="clsBtnBl" class="btn btn-default"><i class="icon-adt_trash"></i></button>
                                <button type="button" id="previewBl" disabled="disabled" onclick="viewBoleta()" class="btn btn-primary"><i class="splashy-zoom"></i> Vista Previa</button>
                                <button type="button" id="printBl" disabled="disabled"  onclick="impBoleta()" class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="prMnArea">
                    
                </div>
                <div id="prBlArea">
                    
                </div>
            </div>
        </div>
    <?php 
        include 'sidebar.php';
        include 'js_in.php';
        include 'js_fn.php';
    ?>
    </body>
    <script>
        $(document).ready(function() {
                loadProdUsr();
                setInterval(function(){
                    loadProdUsr();
                },300000);
                
            });
    </script>
</html>
