<?php
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("consultas")) {
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
                                    . '<th colspan="3">Consulta de Recursos Activos</th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        
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
                        <table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg" style="white-space:pre;" >
                            <thead>
                                <tr>
                                    <th>Terminal</th>
                                    <th>Descripción</th>
                                    <th>Operario</th>
                                    <th>Zonas</th>                                 
                                    <th>Ped. Asignados</th>
                                    <th>Cant. Máxima</th>
                                    <th>Almacen</th>
                                    <th>Tipo Acción</th> 
                                    <th>Acción</th> 
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-lg"  id="viewDeatails" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body"> 
                                    <table class="table table-hover table-striped table-bordered  table-condensed" id="tblasig" >
                                        <thead>
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Cant. sku</th>
                                                <th>Cliente</th>
                                                <th>Multireferencia</th>
                                                <th>Acción</th>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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
        include 'sidebar.php';
        include 'js_in.php';
    ?>
        <script type="text/javascript">
            function delAssign(ped,term){
                var r=confirm("Desea anular asignación de pedido #"+ped+" de la terminal "+term);
                if(r==true){
                    $.ajax({ 
                        type: 'POST',
                        url: 'requests/delAssignTermi.php', 
                        data: {
                            ped:ped,
                            term:term
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.error == false){
                                getResources();
                            }
                        }
                    });
                }
                
            }
            function delSession(user,term){
                var r=confirm("Desea cerrar sesión la terminal "+term);
                if(r==true){
                    $.ajax({ 
                        type: 'POST',
                        url: 'api/logout.php', 
                        data: {
                            user:user,
                            terminal:term
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.error == false){
                                getResources();
                            }
                        }
                    });
                }
                
            }
            function ckAssign(term){
                $("#viewDeatails .modal-title").empty().append('Pedidos Asignados a '+term);
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/getPedTermi.php', 
                    data: {
                        term:term
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        
                        var cntnt = '';
                        $.each(dt.cab, function(k,v){
                            console.log(v);
                            cntnt += '<tr>';
                            cntnt += '<td>'+v.pedido+'</td><td>'+v.cantsku+'</td><td>'+v.cliente+'</td><td>'+v.multiref+'</td><td>'+v.accion+'</td>';
                            cntnt += '</tr>';
                        });
                        $("#tblasig tbody").empty().append(cntnt);
                    }
                });
                $('#viewDeatails').modal('show');
            }
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
                var pedidos = $("#pedidos").val();
                var ubicaciones = $("#ubicacion").val();
                var articulo = $("#articulo").val();
                articulo
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/getStockUbi.php', 
                    data:{
                        pedidos:pedidos,
                        ubicaciones:ubicaciones,
                        articulo:articulo
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        populateDataTable('tblReg',dt); 
                    }
                });
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
                        "bFilter": true,responsive:true,
                        dom: '<"top"B<lfrtip>><"clear">',
                        buttons: [
                             'excel'
                        ],
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
//                    tercod
//ope
//terdes
//canmax
//almrefer
//tipac
//zonas
//asigped
                    var ai = $("#"+id).dataTable().fnAddData( [      
                        v.tercod,    
                        v.terdes,        
                        v.ope,
                        v.zonas,
                        v.asigped,
                        v.canmax,            
                        v.almrefer,
                        v.tipac,
                        v.accion
                    ]);

                });
            }
            function getResources(){
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/getResources.php', 
                    data:{},
                    success: function (data) {
                        var dt = JSON.parse(data);
                        populateDataTable('tblReg',dt); 
                    }
                });
            }
            $(document).ready(function(){
                $('#cons1').addClass('active');
                $('#c11').click();
//                $('#recep').addClass('active');
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
            
                getResources();
                setInterval(function () { getResources(); }, 60000);
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
