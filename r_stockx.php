<?php
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("reportes")) {
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
            
            .label {
                 color: #000; 
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
                                    . '<th colspan="3">Consulta de Stock</th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        <div class="col-lg-12 col-sm-12 former">
                            <form method="post" id="eForm">
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12" ><br/>
                                    
                                    <div class="col-lg-4 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Pedidos:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="material" name="material" />
                                        </div>
                                    </div>
<!--                                    <div class="col-lg-4 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Centro:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="centro" name="centro" />
                                        </div>
                                    </div>-->
                                    <div class="col-lg-4 col-sm-4" >
                                        <div class="input-group">
                                            <label class="label">Almacen:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="almacen" name="almacen" value="LDAL"/>
                                        </div>
                                    </div>
<!--                                    <div class="col-lg-6 col-sm-6" >
                                        <div class="input-group">
                                            <label class="label">Ubicacion:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="ubicacion" name="ubicacion" />
                                        </div>
                                    </div>-->
                                </div>
                                
                                <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Buscar</button>
                                    </div>                                    
                                </div>
                                 <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>    
                                    <div class="input-group">
                                        <img id="loading" style="position:relative; width: 40px !important; height: 40px !important;" class="hiddn" src="images/cargando1.gif"/>
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
                         <table class="table table-hover table-striped table-bordered dTableR" id="tblReg" style="font-size: 10px !important;">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Descripción</th>
                                    <th>Centro</th>
                                    <th>Almacen</th>
                                    <th>Ubicación</th>
                                    <th>EAN</th>
                                    <th>Clase Val.</th>
                                    <th>Jerarquia</th>
                                    <th>UNB</th>
                                    <th>Libre Util.</th>
                                    <th>En Entrega</th>
                                    <th>Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
    <?php         
        include 'sidebar.php';
        include 'js_in.php';
        include 'js_fn.php';
    ?>
        <script type="text/javascript">
            
            function asReception(ped){
                $(".modal-title").empty().append('Asignar pedido <br/><b>#'+ped+'</b>');
                $("#recepcion").val(ped);
                $('#assignRec').modal('show');
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
                    }
                });
            }
            var table;
            function ckFields(){
                var flg = 0;
                var material = $("#material").val();
                var centro = $("#centro").val();
                var almacen = $("#almacen").val();
                if(material != ''){
                    $.ajax({ 
                        type: 'POST',
                        url: 'requests/getStock2.php', 
                        data:{
                            material:material,
                            centro:centro,
                            almacen:almacen
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            populateDataTable('tblReg',dt); 
                        }
                    });                    
                }else{
                    alert("Debe ingresar un material");
                }
            }
            function swOC(oc,pd){
                $.ajax({ 
                    type: 'POST',
                    url: 'alertas_oc.php', 
                    data: {
                        oc:oc,
                        pd:pd
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        $("#ocCont").empty().append(dt.cntn);
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
                        "processing": true,
                        dom: '<"top"B<lfrtip>><"clear">',
                        buttons: [
                            'excel'
                        ],                    
                        "autoWidth": true,
                        "scrollX": true,
                        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "Todo"]],
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
                        },
                        "order": [[1, 'asc']]
                    } ).clear();
//                    console.log(table);
                }
                
                if(data.cab){
                    
                $.each(data.cab, function(k, v){
//                console.log(k);
//                    <th>Material</th>
//                    <th>Descripción</th>
//                    <th>Centro</th>
//                    <th>Almacen</th>
//                    <th>Ubicación</th>
//                    <th>EAN</th>
//                    <th>Clase Val.</th>
//                    <th>Jerarquia</th>
//                    <th>UNB</th>
//                    <th>Libre Util.</th>
//                    <th>En Entrega</th>
//                    <th>Disponible</th>
//    "MATNR": "LA00522",
//    "MAKTX": "GRAMPA OMEGA 3\/4 - PROW",
//    "WERKS": "LCC1",
//    "LGORT": "LDAL",
//    "LGPBE": "",
//    "EAN11": "",
//    "BWTAR": "GRAVADO_PT",
//    "PRDHA": "FEFIJGP000ASIAFUZH",
//    "MEINS": "ST",
//    "LABST": 14500,
//    "OMENG": 0,
//    "DISPO": 14500
                    var ai = $("#"+id).dataTable().fnAddData( [      
                        v.MATNR,
                        v.MAKTX,
                        v.WERKS,
                        v.LGORT,
                        v.LGPBE,
                        v.EAN11,
                        v.BWTAR,
                        v.PRDHA,
                        v.MEINS,
                        v.LABST,
                        v.OMENG,
                        v.DISPO
                    ]);

                });
                }
            }

            $(document).ready(function(){
                $('#repor1').addClass('active');
                $('#c9').click();
                $('#recep').addClass('active');
                table = $("#tblReg").DataTable({
                        "processing": true,
                        "processing": true,
                        dom: '<"top"B<lfrtip>><"clear">',
                        buttons: [
                            'excel'
                        ],                    
                        "autoWidth": true,
                        "scrollX": true,
                        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "Todo"]],
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
                        },
                        "order": [[1, 'asc']]
                    } );
            
                
                $('#eprod').addClass('active');
                $('#c1').click();
                $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });
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
