<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("maestros")) {
    echo "No tiene acceso";
    exit;
}

$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
    <style>
        td.details-control {
            background: url('images/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('images/details_close.png') no-repeat center center;
        }

        .table>tbody>tr>th {

            width: 150px !important;
        }

        .table>tbody>tr>td>input {

            width: 100% !important;
        }

        .hiddn {
            display: none;
        }
    </style>
    <div id="maincontainer" class="clearfix">
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="row">
                            <?php

                            //                var_dump($_POST);
                            echo '<table class="table">'
                                . '<thead>'
                                . '<tr>'
                                . '<th colspan="2">Seguimiento de Embarques.</th>'
                                . '</tr>'
                                . '</thead>'
                                . '<tbody>'
                                . '<tr>'
                                . '</tr>'
                                . '</tbody>'
                                . '</table>'; ?>
                            <div style="clear:both;"></div><br />

                            <form id="filter">
                                <!--                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group input-daterange">
                                        <input type="text" id="field1" name="field1" class="form-control" placeholder="Material" />
                                    </div>
                                </div>-->
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <input type="text" id="field1" name="field1" class="form-control"
                                            placeholder="Contrato Marco" />
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default"
                                                onclick="loadMatchModal('field1','artrefer,artdesc', 'arti', 1)"
                                                type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                               
                                <div class="col-lg-1 col-sm-1">
                                    <div class="input-group">
                                        <button class="form-control btn btn-primary" type="button"
                                            onclick="chkFlds()">Buscar</button>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <div class="input-group">
                                        <img id="loading"
                                            style="position:relative; width: 55% !important; height: 55% !important;"
                                            class="hiddn" src="images/cargando1.gif" />
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div><br />
                            <?php

                            //                var_dump($_POST);
                            echo '<table class="table">'
                                . '<thead>'
                                . '<tr>'
                                . '<th colspan="2"></th>'
                                . '</tr>'
                                . '</thead>'
                                . '<tbody>'
                                . '<tr>'
                                . '</tr>'
                                . '</tbody>'
                                . '</table>'; ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-sm-4 col-md-4" style="border: 1px solid #ADD8E6; padding: 15px; box-shadow: 0 4px 8px rgba(173, 216, 230, 0.5);">
                                <h3 class="heading" style="text-shadow: 2px 2px 4px rgba(0, 255, 255, 0.6);"><strong>Detalle Financiero</strong></h3>
                                <div class="row" style="margin-left: 5px;">
                                    <div id="tabl">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    
                                                    <div class="row" style>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Año</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Sociedad:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Cond.Pago:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div> 
                                                     
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Contrato Marco</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Orden de Compra:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                         <div class="col-sm-4">
                                                            <div class="form-group">
                                                              <label for="buscar">Nro.Embarque</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div> 
                                                <div class="row">
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                              <label for="buscar">Nro.Invoice</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-5">
                                                            <div class="form-group">
                                                              <label for="buscar">Nro.Proforma:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                         
                                                     </div> 
                                                <h3 class="heading"><strong>Fecha de Pago al Proveedor </strong></h3><!-- comment -->
                                                <div class="row">       
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe Invoice</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe.Proforma:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                         
                                                     </div> 
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fec.Previs.Cancelacion</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe Cancelacion:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                         <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fec.Pago:</label>
                                                             <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div> 

                                                   
                                                    <br><br>
                                                   <h3 class="heading" style="text-shadow: 2px 2px 4px rgba(0, 255, 255, 0.6);"><strong>Datos Despachante y Aduana</strong></h3>

                                                     <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                              <label for="buscar">Nombre Despachante</label>
                                                              <select id="codalma" class="form-control">
                                                                <?php
                                                                $sq = "select * from despachantes";
                                                                $rs = $db->query($sq);
                                                                echo '<option value="">Seleccionar</option>';
                                                                while ($ax = $rs->fetch_assoc()) {
                                                                  echo '<option value="' . $ax['codespa'] . '">' . $ax['codespa'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                                                                }
                                                                ?>
                                                              </select>
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-5">
                                                            <div class="form-group">
                                                              <label for="buscar">Aduana</label>
                                                              <select id="codalma" class="form-control">
                                                                <?php
                                                                $sq = "select * from alma";
                                                                $rs = $db->query($sq);
                                                                echo '<option value="">Seleccionar</option>';
                                                                while ($ax = $rs->fetch_assoc()) {
                                                                  echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . utf8_encode($ax['almdes']) . '</option>';
                                                                }
                                                                ?>
                                                              </select>
                                                            </div>
                                                          </div>
                                                          </div>
                                                   <h3 class="heading"><strong>Pagos y Anticipos a Despachantes</strong></h3><!-- comment -->
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Anticipo Despachante</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fecha Pago:</label>
                                                              <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                                                            </div>
                                                          </div>  
                                                     </div>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Complemento</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fecha Pago:</label>
                                                              <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                                                            </div>
                                                          </div>  
                                                     </div> 
                                                   <h3 class="heading"><strong>Pagos y Anticipos a Aduana</strong></h3><!-- comment -->
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Anticipo Aduana</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fecha Pago:</label>
                                                              <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                                                            </div>
                                                          </div>  
                                                     </div>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Complemento</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Importe</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Moneda</label>
                                                              <input type="text" id="mnInNroser" name="mnInNroser" class="form-control"
                                                                placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-3">
                                                            <div class="form-group">
                                                              <label for="buscar">Fecha Pago:</label>
                                                              <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                                                            </div>
                                                          </div>  
                                                     </div> 
                                                     <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="form-group">
                                                              <label for="buscar">Observaciones:</label>
                                                              <textarea id="mnInLot" name="mnInLot" class="form-control" placeholder="Agregue las Observaciones" rows="4" cols="50"></textarea>
                                                            </div>
                                                          </div>
                                                         
                                                          </div> 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-sm-3 col-md-3" style="border: 1px solid #ADD8E6; padding: 15px; box-shadow: 0 4px 8px rgba(173, 216, 230, 0.5);">
                                <h3 class="heading" style="text-shadow: 2px 2px 4px rgba(0, 255, 255, 0.6);"><strong>Datos Proveedor</strong></h3>
                                <div class="row" style="margin-left: 5px;">
                                    <div id="tabl">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                  
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                           <div class="form-group">
                                                              <label for="buscar">Proveedor:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-6">
                                                            <div class="form-group">
                                                              <label for="buscar">Nombre Proveedor:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div>
                                                      <div class="row">
                                                        <div class="col-sm-2">
                                                           <div class="form-group">
                                                              <label for="buscar">Org.Comp:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-2">
                                                           <div class="form-group">
                                                              <label for="buscar">Grup.Comp:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-2">
                                                           <div class="form-group">
                                                              <label for="buscar">Incoterm:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                           <div class="col-sm-2">
                                                            <div class="form-group">
                                                              <label for="buscar">Sector:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div> 
                                                        <div class="row">
                                                        <div class="col-sm-4">
                                                           <div class="form-group">
                                                              <label for="buscar">Fabrica:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                         
                                                         
                                                           <div class="col-sm-6">
                                                            <div class="form-group">
                                                              <label for="buscar">Localidad:</label>
                                                              <input type="text" id="mnInLot" name="mnInLot" class="form-control"
                                                            placeholder="" value="" />
                                                            </div>
                                                          </div>
                                                     </div>  
                                                      <div class="row">
                                                        <div class="col-sm-8">
                                                           <div class="form-group">
                                                              <label for="buscar">Descripcion Mercaderia:</label>
                                                             <textarea id="mnInLot" name="mnInLot" class="form-control" placeholder="Agregue las Observaciones" rows="4" cols="50"></textarea>
                                                            </div>
                                                          </div>

                                                     </div> 
                                                <br><br>
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-4 col-md-4">
                                <div class="slider">

                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="modal fade bd-example-modal-lg" id="modalData" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" style="overflow-x:auto;">
                                    </div>

                                    <div class="modal-footer">
                                        <!--<button type="button" onclick="clsDetail()" id="btnClsPk" class="btn btn-default"><i class="icon-adt_trash"></i></button>-->
                                        <!--<button type="button" disabled="disabled" id="btnPk" onclick="btnVlnSv()" class="btn btn-primary">Guardar</button>-->
                                        <!--                                <button type="button" disabled="disabled" id="btnPk" onclick="btnVlnPk()" class="btn btn-primary">Picking</button>
                                                <button type="button" disabled="disabled" id="btnAnl" onclick="btnVlnAnl()" class="btn btn-warning">Anulación</button>-->
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <?php
    include 'modal_match.php';
    include 'sidebar.php';
    include 'js_in.php';
    ?>
    <script>
        function swModal(id) {
            var art = $("#mnInArt").val();
            var codalma = $("#codalma").val();
            if (art != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/getDatModal.php',
                    data: {
                        id: id,
                        art: art,
                        codalma: codalma
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        if (!dt.err) {
                            var mytable = '<table class="table table-hover table-striped table-bordered  table-condensed"><thead>' + dt.hdr + '</thead><tbody>';

                            $.each(dt.cab, function (key, value) {
                                mytable += "<tr>";
                                $.each(value, function (k, v) {
                                    mytable += "<td>" + v + "</td>";
                                });
                                mytable += "</tr>";
                            });
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-body").empty().append(mytable);
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-title").empty().append(dt.tit);
                            $('#modalData').modal('show');
                        } else {
                            alert(dt.msg);
                        }
                    }
                });
            } else {
                alert("Debe consultar un artículo.");
            }
            //                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
        }
        $(document).ajaxStart(function () {
            $("#loading").removeClass('hiddn');
        });
        $(document).ajaxComplete(function () {
            $("#loading").addClass('hiddn');
        });
        $(document).ready(function () {
            $("#field1").focus();
            $('#segui2').addClass('active');
            $('#seguimiento').click();
            $('#field1').scannerDetection({
                onComplete: function () {
                    chkFlds();
                }
            });
        });
        var slider;
        function loadFlex() {
            if (typeof (slider) == 'object') {
                //                    console.log(typeof(slider));
                slider.reloadSlider();
            } else {
                slider = $('.slider').bxSlider({
                    preloadImages: 'all'
                });
            }

        };
        function chkFlds() {
            var mat = $('#field1').val();
            var codalma = $('#codalma').val();
            var desc = "";
            $.ajax({
                type: 'POST',
                url: 'requests/getDataEmbarque.php',
                data: {
                    mat: mat,
                    desc: desc,
                    codalma: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    if (!dt.err) {
                        var cntul = '';
                        var mats = dt.dat[0];
                        console.log(mats.artdesc);
                        $("#mnInArt").val(mats.artrefer);
                        $("#mnInDesc").val(mats.artdesc);
                        $("#mnInAbc").val(mats.artrot);
                        $("#mnInEan").val(mats.artean);
                        $("#mnInGrar").val(mats.artgrup);
                        $("#mnInNroser").val(mats.artser);
                        $("#mnInAbc").val("");
                        $("#mnInProp").val(mats.almcod);
                        $("#mnInNom").val(mats.clinom);
                        $("#mnInUm").val(mats.unimed);

                    } else {
                        alert(dt.msg);
                    }
                }
            });
        }
    </script>
</body>

</html>