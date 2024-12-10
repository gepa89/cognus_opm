<?php
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("inventario")) {
    echo "No tiene acceso";
    exit;
}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
/*            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }*/
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                position: relative;
                padding-left: 30px;
                cursor: pointer;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
                content: "-";
                background-color: #d33333;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
                top: 33%;
                left: 5px;
                height: 1em;
                width: 1em;
                margin-top: -5px;
                display: block;
                position: absolute;
                color: white;
                border: .15em solid white;
                border-radius: 1em;
                box-shadow: 0 0 0.2em #444;
                box-sizing: content-box;
                text-align: center;
                text-indent: 0 !important;
                font-family: "Courier New",Courier,monospace;
                line-height: 1em;
                content: "+";
                background-color: #31b131;
            }
/*            tr.shown td .details-control {
                background: url('details_close.png') no-repeat center center;
            }*/
            .label {
                 color: #000; 
            }
            #tblReg tbody tr td:first-of-type{
                width:100px;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <?php

                            echo '<table class="table">'
                                . '<thead>'
                                    . '<tr>'
                                    . '<th colspan="3">Reporte de Inventario</th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        <div class="col-lg-12 col-sm-12 former">
                            <form method="post" id="eForm">
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12" ><!--
                                    <div class="col-lg-2 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Proveedor:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="desPrv" name="desPrv" value="<?php echo $prv;?>"/>
                                        </div>
                                    </div> comment 
                                    <div class="col-lg-2 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Articulos:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="desArt" name="desArt" value="<?php echo implode(',',$arArt);?>"/>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-lg-12 col-sm-12" ><br/>
                                    
<!--                                    <div class="col-lg-2 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Material:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="inMat" name="inMat" value="<?php echo $pd;?>"/>
                                        </div>
                                    </div>-->
                                    <div class="col-lg-3 col-sm-3">
                                        <label class="label">Material:</label><div style="clear:both;"></div><br/>
                                        <div class="input-group">
                                            
                                            <input class="form-control" id="inMat" name="inMat" value="<?php echo $pd;?>"/>
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default" onclick="loadMatchModal('inMat','artrefer,artdesc', 'arti', 1)" type="button"><span class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                        </div><!-- /input-group -->
                                    </div><!-- /.col-lg-6 -->
                                    <div class="col-lg-5 col-sm-5">
                                        <label class="label">Fecha Creación:</label><div style="clear:both;"></div><br/>
                                        <div class="input-group input-daterange">
                                            <input autocomplete="off" type="text" name="dFecCre" id="dFecCre" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["dFecCre"]?>"/>
                                            <div class="input-group-addon"> hasta </div>
                                            <input autocomplete="off" type="text" name="hFecCre" id="hFecCre" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["hFecCre"]?>"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2" >
                                        <div class="input-group">
                                            <label class="label">Tipo:</label><div style="clear:both;"></div><br/>
                                            <select class="form-control" id="selCto" name="selCto[]"  multiple="multiple">
                                                
                                                <?php 
                                                    $sql = "select distinct invetipo from invenpic";
                                                    $rs = $db->query($sql);
                                                    while($ax = $rs->fetch_assoc()){
                                                        echo '<option value="'.$ax['invetipo'].'">'.$ax['invetipo'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
<!--                                    <div class="col-lg-2 col-md-2 col-sm-2" >
                                        <div class="input-group">
                                            <label class="label">Situación:</label><div style="clear:both;"></div><br/>
                                            <select class="form-control" id="selSit" name="selSit[]"  multiple="multiple">
                                                <?php 
//                                                    $sql = "select * from situped";
//                                                    $rs = $db->query($sql);
//                                                    while($ax = $rs->fetch_assoc()){
//                                                        echo '<option value="'.$ax['siturefe'].'">'.$ax['siturefe'].' - '.$ax['situdes'].'</option>';
//                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>    <div class="col-lg-2 col-md-2 col-sm-2" >
                                        <div class="input-group">
                                            <label class="label">Cod. Envío:</label><div style="clear:both;"></div><br/>
                                            <select class="form-control" id="selCod" name="selCod[]"  multiple="multiple">
                                                <?php 
//                                                    $sql = "select distinct codenv from pedexcab where codenv <> null or codenv <> ''";
//                                                    $rs = $db->query($sql);
//                                                    while($ax = $rs->fetch_assoc()){
//                                                        echo '<option value="'.$ax['codenv'].'">'.$ax['codenv'].'</option>';
//                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>                                    -->
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Buscar</button>
                                    </div>
                                </div>
                            </form> 
                        </div><div style="clear:both;"></div>
                    </div><br />
                    <div class="row">
                    <?php

                        echo '<table class="table">'
                            . '<thead>'
                                . '<tr>'
                                . '<th colspan="3"></th>'
                                . '</tr>'
                            . '</thead>'
                        . '</table>';?>
                        <table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg" style="font-size: 12px !important;">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Material</th>
                                    <th>Descripción</th>
                                    <th>Ubicación</th>
                                    <th>Stock en Ubic.</th>
                                    <th>Cant. Ingresada</th>
                                    <th>Diferencia</th>
                                    <th>Operario</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-sm"  id="assignRec" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="eForm1">
                                        <div class="col-lg-12 col-sm-12" >
                                            <div class="input-group">
                                                <input type="hidden" id="recepcion" name="recepcion" class="form-control" value=""/>
                                                <select class="form-control" id="terminal" name="terminal">
                                                    <?php
                                                        $sq = "select * from termi where tipac <> 'RECE'"; 
                                                        $rs = $db->query($sq);
                                                        echo '<option value="">Seleccionar</option>';
                                                        while($ax = $rs->fetch_assoc()){
                                                            echo '<option value="'.$ax['tercod'].'">'.$ax['tercod'].' - '. utf8_encode($ax['terdes']).'</option>';
//                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <br/>
                                            <div class="input-group">
                                                <button type="button" onclick="saveAssign()" class="form-control btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </form>  
                                    <div style="clear:both;"></div>
                                </div>
                            </div>

                          </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
    <?php 
        include 'modal_match.php';
        include 'sidebar.php';
        include 'js_in.php';
    ?>
        <script type="text/javascript">
            function asReception(ped, zon){
                $(".modal-title").empty().append('Asignar pedido <br/><b>#'+ped+'</b> en Zona <b>'+zon+'</b>');
                $("#recepcion").val(ped);
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/get_terminal_zone.php', 
                    data: {
                        zon:zon
                    },success: function (data) {
                        $("#terminal").empty().append(data);
                        $('#assignRec').modal('show');
                    }
                });
                
            }
            function asReceptionRe(ped, zon){
                $(".modal-title").empty().append('Asignar pedido <br/><b>#'+ped+'</b> en Zona <b>'+zon+'</b>');
                $("#recepcion").val(ped);
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/get_terminal_zone.php', 
                    data: {
                        zon:zon
                    },success: function (data) {
                        $("#terminal").empty().append(data);
                        $('#assignRec').modal('show');
                    }
                });
                
            }
            function saveAssign(){
                var pedido = $("#recepcion").val();
                var terminal = $("#terminal").val();
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/asignar_pedido_termial.php', 
                    data: {
                        pedido:pedido,
                        terminal:terminal
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        $("#sndBtn").click();
                    }
                });
            }
            var table;
            function ckFields(){
                var flg = 0;
//                inMat
//                dFecCre
//                hFecCre
//                selCto
                var inMat = $("#inMat").val();
                var dFecCre = $("#dFecCre").val();
                var hFecCre = $("#hFecCre").val();
                var selCto = $("#selCto").val();
                
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/getReportInv.php', 
                    data:{
                        inMat:inMat,
                        dFecCre:dFecCre,
                        hFecCre:hFecCre,
                        selCto:selCto,
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        populateDataTable('tblReg',dt); 
                    }
                });
            }
            function sendExpedition(pedido){
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/send_to_sap_ex.php', 
                    data: {
                        pedido:pedido
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.error == 0){
                            $("#sndBtn").click();
                        }
                    }
                });
            }

            function populateDataTable(id, data) {                
//                var table;
                //se obtiene la instancia del datatables, si no existe, se crea           
                if ( $.fn.dataTable.isDataTable( '#'+id ) ) {
                    
                    table = $('#'+id).DataTable({'retrieve':true}).clear().draw();;
                    console.log(table);
                }
                else {                    
                    table = $("#"+id).DataTable({
                        "processing": true,
                    "bFilter": true,responsive:true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                         'excel'
                    ],
                    columnDefs: [ {
                        className: 'details-control',
                        orderable: false,
                        targets:   0
                    } ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
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
                    }}).clear();
//                    console.log(table);
                }
                $.each(data.cab, function(k, v){
//                console.log(v.rg_bldat);
                    //<th>Documento</th>
                    //<th>Material</th>
                    //<th>Descripción</th>
                    //<th>Ubicación</th>
                    //<th>Stock en Ubic.</th>
                    //<th>Cant. Ingresada</th>
                    //<th>Diferencia</th>
                    //<th>Operario</th>
                    //<th>Fecha</th>
                    //<th>Hora</th>
                    //<th>Tipo</th>
                    var ai = $("#"+id).dataTable().fnAddData( [  
                        v.invedoc,            
                        v.artrefer,
                        v.artdesc,
                        v.ubirefer,
                        v.canubic,
                        v.canfisi,
                        v.diferencia*(-1),
                        v.operef,
                        v.fecinvepic,
                        v.horinvepic,
                        v.invetipo
                    ]);

//                    var tblLoop = document.getElementById(id);
//
//                    var rowLength = tblLoop.rows.length;
////                    console.log(rowLength);
//                    for(var i=0; i<rowLength; i++){
//                        var rowx = tblLoop.rows[i];
//                        var rowidx = tblLoop.rows[i].rowIndex;
//
//                        console.log(rowidx);
//                        if(i > 0 && i <= rowLength){
//    //                        console.log(i);
//                            var idx = rowidx;
//                            var tr = $("#"+id).closest('table').children('tbody').children('tr:nth-child('+idx+')');
//                            var dataTableRow = table.row( tr );
//                            var cellValAll = rowx.cells[1].outerHTML;
//                            var cellVal = cellValAll.replace('<td>', "").replace('</td>', "").trim();
////                            console.log(cellVal);
//                            dataTableRow.child( format(data.det[cellVal]) );
//                        }
//                    }
                });
            }

            $(document).ready(function(){
                
                table = $("#tblReg").DataTable({
                        "processing": true,
                    "bFilter": true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                         'excel'
                    ],
                    columnDefs: [ {
                        className: 'details-control',
                        orderable: false,
                        targets:   0
                    } ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
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
                    }});
            
                
                $('#tblReg tbody').on('click', '.details-control', function () {
                    var table = $("#tblReg").DataTable({'retrieve':true});
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
                    var rowx = table.row( tr ).data();
                    console.log("llamada");
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child.show();
                        tr.addClass('shown');

                    }
                });
                
                $('#inven2').addClass('active');
                $('#c8').click();
                $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });
                
                $( "#selCod" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
                $( "#selCodExp" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
                
                $( "#selCto" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
                $( "#selSit" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
            });
            function min_to_hour(min){
                var zero2 = new Padder(2);
                var deci = min - Math.floor(min);
                deci = Math.floor(parseFloat(deci)*10);
                if(deci >= 5){
                    var realmin = Math.ceil(min % 60);
                }else{
                    var realmin = Math.floor(min % 60);
                }
                if(realmin > 59){
                    realmin = 0;
                    var hour = Math.ceil(min / 60);
                }else{
                    var hour = Math.floor(min / 60);
                }
                
                
                return zero2.pad(hour)+":"+zero2.pad(realmin);
            }
             function Padder(len, pad) {
                if (len === undefined) {
                  len = 1;
                } else if (pad === undefined) {
                  pad = '0';
                }

                var pads = '';
                while (pads.length < len) {
                  pads += pad;
                }

                this.pad = function (what) {
                  var s = what.toString();
                  return pads.substring(0, pads.length - s.length) + s;
                };
              }  
        </script>
    </body>
</html>
