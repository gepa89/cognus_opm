<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("externo")) {
    echo "No tiene acceso";
    exit;
}
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
    <style>
        /*            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }*/
        div.dtsp-searchPane div.dataTables_scrollBody {
            height: 70px !important;
            width: 200px !important;
        }

        .dtsp-columns-1 {
            max-width: 24% !important;
            margin: 0px !important;
        }

        .label {
            color: #000;
        }
    </style>
    <div id="maincontainer" class="clearfix">
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <?php

                    echo '<table class="table">'
                        . '<thead>'
                        . '<tr>'
                        . '<th colspan="3">Consulta Stock Ubicacion/Valor</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12"><br />
                                <div class="col-lg-3 col-sm-3">
                                    <label class="label">Cliente:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <input class="form-control" id="cliente" name="cliente" />
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default"
                                                onclick="loadMatchModal('cliente','clirefer,clinom', 'clientes', 1)"
                                                type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div>
                                <div class="col-lg-3 col-sm-3">
                                    <label class="label">Artículo:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <input class="form-control" id="articulo" name="articulo" />
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default"
                                                onclick="loadMatchModal('articulo','artrefer,artdesc', 'arti', 1)"
                                                type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-3 col-sm-3">
                                    <label class="label">Ubicacion:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <input class="form-control" id="ubicacion" name="ubicacion" />
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default"
                                                onclick="loadMatchModal('ubicacion','ubirefer', 'ubimapa', 1)"
                                                type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg4 col-sm-12"> </div>
                                <nav></nav>
                                <div class="input-group">
                                    <label class="label" style="color:#000;">Seleccionar Almacen:</label>
                                    <div style="clear:both;"></div><br />
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

                            <div class="col-lg-1 col-sm-1">
                                <label class="label" style="color:#b9cfe5 !important;">_________</label>
                                <div style="clear:both;"></div><br />
                                <div class="input-group">
                                    <button type="button" id="sndBtn" onclick="ckFields()"
                                        class="form-control btn btn-primary">Buscar</button>
                                </div>
                            </div>
                            <div class="col-lg-1 col-sm-1">
                                <label class="label" style="color:#b9cfe5 !important;">_________</label>
                                <div style="clear:both;"></div><br />
                                <div class="input-group">
                                    <img id="loading"
                                        style="position:relative; width: 40px !important; height: 40px !important;"
                                        class="hiddn" src="images/cargando1.gif" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div style="clear:both;"></div>
                </div><br />
                <div class="row">
                    <?php

                    echo '<table class="table">'
                        . '<thead>'
                        . '<tr>'
                        . '<th colspan="3"></th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Nombre</th>
                                <th>Articulo</th>
                                <th>Descripción</th>
                                <th>Ubicacion</th>
                                <th>Cantidad</th>
                                <th>Valor PYG</th>
                                <th>Valor USD</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- Modal -->
                <div class="modal fade bd-example-modal-sm" id="assignRec" tabindex="-1" role="dialog"
                    aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <form method="post" id="eForm1">
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <input type="hidden" id="recepcion" name="recepcion" class="form-control"
                                                value="" />
                                            <select class="form-control" id="terminal" name="terminal">
                                                <?php
                                                $sq = "select * from termi where tipac = 'RECE'";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['tercod'] . '">' . $ax['tercod'] . ' - ' . utf8_encode($ax['terdes']) . '</option>';
                                                    //                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <br />
                                        <div class="input-group">
                                            <button type="button" onclick="saveAssign()"
                                                class="form-control btn btn-primary">Guardar</button>
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

        function asReception(ped) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b>');
            $("#recepcion").val(ped);
            $('#assignRec').modal('show');
        }
        function saveAssign() {
            var pedido = $("#recepcion").val();
            var terminal = $("#terminal").val();
            $.ajax({
                type: 'POST',
                url: 'requests/asignar_pedido_termial.php',
                data: {
                    pedido: pedido,
                    terminal: terminal
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                }
            });
        }
        var table;
        function ckFields() {
            var flg = 0;
            var pedidos = $("#pedidos").val();
            var cliente = $("#cliente").val();
            var ubicaciones = $("#ubicacion").val();
            var articulo = $("#articulo").val();
            var codalma = $("#codalma").val();
            articulo
            $.ajax({
                type: 'POST',
                url: 'requests/getStockMonto.php',
                data: {
                    pedidos: pedidos,
                    cliente: cliente,
                    ubicaciones: ubicaciones,
                    articulo: articulo,
                    codalma: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    populateDataTable('tblReg', dt);
                }
            });
        }
        function swOC(oc, pd) {
            $.ajax({
                type: 'POST',
                url: 'alertas_oc.php',
                data: {
                    oc: oc,
                    pd: pd
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    $("#ocCont").empty().append(dt.cntn);
                }
            });
        }

        function populateDataTable(id, data) {
            //                var table;
            //se obtiene la instancia del datatables, si no existe, se crea           
            if ($.fn.dataTable.isDataTable('#' + id)) {

                table = $('#' + id).DataTable({ 'retrieve': true }).clear().draw();;
                console.log(table);
            }
            else {
                table = $("#" + id).DataTable({
                    searchPanes: {
                        viewTotal: true,
                        columns: [4]
                    },
                    columnDefs: [
                        {
                            searchPanes: {
                                show: true,
                                options: [
                                    {
                                        label: 'Con Stock',
                                        value: function (rowData, rowIdx) {
                                            return rowData[4] > 0;
                                        }
                                    },
                                    {
                                        label: 'Sin Stock',
                                        value: function (rowData, rowIdx) {
                                            return rowData[4] == 0;
                                        }
                                    }
                                ]
                            },
                            targets: [4]
                        }
                    ],
                    "processing": true, responsive: true,
                    "bFilter": true,
                    dom: 'P<"top"B<lfrtip>><"clear">',
                    buttons: [
                        'excel'
                    ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
                    "language": {
                        "processing": "Procesando...",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No se encontraron resultados",
                        "emptyTable": "Ningún dato disponible en esta tabla",
                        "info": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty": "Mostrando del 0 al 0 de un total de 0 registros",
                        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "infoPostFix": "",
                        "search": "Buscar:",
                        "Url": "",
                        "infoThousands": ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                }).clear();
                //                    console.log(table);
            }

            if (data.cab) {

                $.each(data.cab, function (k, v) {
                    //                console.log(v.rg_bldat);
                    //<th></th>
                    //<th>Pedido</th>
                    //<th>Fecha Creacion</th>
                    //<th>Hora Creacion</th>
                    //<th>fecha recepcion</th>
                    //<th>Proveedor</th>
                    //<th>Clase de Documento</th>
                    //<th>Situacion del Pedido</th>
                    //<th>Planificar</th>
                    var ai = $("#" + id).dataTable().fnAddData([
                        v.clirefer,
                        v.clinom,
                        v.artrefer,
                        v.artdesc,
                        v.ubirefer,
                        v.total,
                        v.valor,
                        v.valorus
                    ]);

                });
                table.searchPanes.rebuildPane();
            }
        }

        $(document).ready(function () {
            $('#repor2').addClass('active');
            $('#c9').click();
            if ($.fn.DataTable.fnIsDataTable(table)) {
                table.on('select.dt', () => {
                    table.searchPanes.rebuildPane(0, true);
                });

                table.on('deselect.dt', () => {
                    table.searchPanes.rebuildPane(0, true);
                });

            }


            $('#recep').addClass('active');
            //                table = $("#tblReg").DataTable({
            //                        "processing": true,
            //                    "bFilter": true,
            //                    dom: '<"top"B<lfrtip>><"clear">',
            //                    buttons: [
            //                         'excel'
            //                    ],
            //                    columnDefs: [ {
            //                        className: 'details-control',
            //                        orderable: false,
            //                        targets:   0
            //                    } ],
            //                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
            //                    "language": {
            //                        "processing":     "Procesando...",
            //                        "lengthMenu":     "Mostrar _MENU_ registros",
            //                        "zeroRecords":    "No se encontraron resultados",
            //                        "emptyTable":     "Ningún dato disponible en esta tabla",
            //                        "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
            //                        "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
            //                        "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
            //                        "infoPostFix":    "",
            //                        "search":         "Buscar:",
            //                        "Url":            "",
            //                        "infoThousands":  ",",
            //                        "loadingRecords": "Cargando...",
            //                        "paginate": {
            //                            "first":    "Primero",
            //                            "last":     "Último",
            //                            "next":     "Siguiente",
            //                            "previous": "Anterior"
            //                        },
            //                        "aria": {
            //                            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
            //                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
            //                        }
            //                    }});


            $('#eprod').addClass('active');
            $('#c1').click();
            $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });
            $("#selCodExp").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    }
                    else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });

            $("#selCto").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    }
                    else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });
            $("#selSit").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    }
                    else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });
        });
        function min_to_hour(min) {
            var zero2 = new Padder(2);
            var deci = min - Math.floor(min);
            deci = Math.floor(parseFloat(deci) * 10);
            if (deci >= 5) {
                var realmin = Math.ceil(min % 60);
            } else {
                var realmin = Math.floor(min % 60);
            }
            if (realmin > 59) {
                realmin = 0;
                var hour = Math.ceil(min / 60);
            } else {
                var hour = Math.floor(min / 60);
            }


            return zero2.pad(hour) + ":" + zero2.pad(realmin);
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