<?php
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tienePermiso("reportes", "r_asignados", "leer")) {
    echo "No tiene acceso";
    exit;
}
$pd = '';
$hoy = date('Y-m-d');
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
            .nowrap { white-space: nowrap; }
</style>
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
                                    . '<th colspan="3">Consulta Asignaciones de Pedidos<th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        <div class="col-lg-12 col-sm-12 former">
                            <form method="post" id="eForm">
                                <div style="clear:both;"></div>
                                <div class="col-lg-12 col-sm-12" ><br/>
                                   
                                    <div class="col-lg-5 col-sm-5">
                                        <label class="label">Fecha Movimiento:</label><div style="clear:both;"></div><br/>
                                        <div class="input-group input-daterange">
                                            <input autocomplete="off" type="text" name="dFecCre" id="dFecCre" value="<?php echo $hoy; ?>" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["dFecCre"]?>"/>
                                            <div class="input-group-addon"> hasta </div>
                                            <input autocomplete="off" type="text" name="hFecCre" id="hFecCre" value="<?php echo $hoy; ?>" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["hFecCre"]?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-12" ><br/><!-- comment -->                                    
                                    <div class="col-lg-2 col-sm-2" >
                                        <div class="input-group">
                                            <label class="label">Almacen:</label><div style="clear:both;"></div><br/>
                                            <select class="form-control" id="alma" name="alma"  multiple="multiple">
                                                <?php 
                                                    $sq = "select * from alma";
                                                    $rs = $db->query($sq);
                                                    while($ax = $rs->fetch_assoc()){
                                                        echo '<option value="'.$ax['almcod'].'">'.$ax['almcod'].' - '.$ax['almdes'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                   
                                  
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
                                    <th>Pedido</th>                                    
                                    <th>Terminal</th>
                                    <th>Almacen</th>
                                    <th>Fecha Asignacion</th>
                                    <th>Hora Asignacion</th>
                                    <th>Usuario</th>
                                    <th>Accion</th>
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
                                                        $sq = "select * from termi where tipac = 'RECE'"; 
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
        include 'sidebar.php';
        include 'js_in.php';
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
                var arti = $("#arti").val();
                var cli = $("#cli").val();
                var prov = $("#prov").val();
                var dFecCre = $("#dFecCre").val();
                var hFecCre = $("#hFecCre").val();
                var alma = $("#alma").val();
                var clamov = $("#clamov").val();
                var pedclase = $("#pedclase").val();
                
                if(dFecCre != ''){
                    $.ajax({ 
                        type: 'POST',
                        url: 'requests/getAsigPed.php', 
                        data:{
                            arti:arti,
                            cli:cli,
                            prov:prov,
                            dFecCre:dFecCre,
                            hFecCre:hFecCre,
                            alma:alma,
                            pedclase:pedclase,
                            clamov:clamov
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            populateDataTable('tblReg',dt); 
                        }
                    });
                }else{
                    alert("Debe ingresar el campo de fecha.");
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
    //                console.log(v.rg_bldat);
    //                    arti
    //                    artides
    //                    coprov
    //                    almacen
    //                    cliente
    //                    fecierre
    //                    horcierre
    //                    cladoc
    //                    nrodoc
    //                    canprep
    //                    posi
    //                    userprep
    //                    movref
                        var ai = $("#"+id).dataTable().fnAddData( [      
                            v.pedido,                        
                            v.tercod,
                            v.cod_alma,            
                            v.fecasig,
                            v.horasig,
                            v.usuario,
                            v.accion
                           
                            
                            
                        ]);

                    });
                    
                }
            }

            $(document).ready(function(){
                $('#repor49').addClass('active');
                $('#c9').click();
                $('#recep').addClass('active');
                


                $( "#alma" ).multiselect({
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
                $( "#clamov" ).multiselect({
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
                $( "#pedclase" ).multiselect({
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