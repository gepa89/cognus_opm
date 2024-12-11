<?php

require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("empaque")) {
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
        .btn-fnt-size {
            font-size: 9px !important;
            padding: 2px !important;
        }

        .details-control {
            background: url('details_open.png') no-repeat center center;
            cursor: pointer;
            width: 40px !important;
            height: 40px !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
            content: "-";
            background-color: #d33333;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
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
            font-family: "Courier New", Courier, monospace;
            line-height: 1em;
            content: "+";
            background-color: #31b131;
        }

        tr.shown td .details-control {
            background: url('details_close.png') no-repeat center center;
        }

        .label {
            color: #000;
        }

        #tblEmp tbody tr td:first-of-type {
            width: 100px;
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
                        . '<th colspan="3">Pedidos Para Empaque</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="col-lg-2 col-sm-4">
                                    <div class="input-group">
                                        <label class="label">Ingrese Multireferencia:</label>
                                        <div style="clear:both;"></div><br />
                                        <input class="form-control" id="desPed" name="desPed"
                                            value="<?php echo $pd; ?>" />
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
                    <!-- table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg" style="font-size: 12px !important;">
                       -->
                    <table width="100%" class="table table-hover table-striped table-bordered  table-condensed"
                        id="tblEmp" style="font-size: 12px !important;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Ped.Ventas</th>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Nombre</th>
                                <th>Clas.Doc.</th>
                                <th>fecha Creacion</th>
                                <th>Hora Creacion.</th>
                                <th>Almacen</th>
                                <th>Sit.Ped</th>

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
        function asReception(ped, zon, alma, tipo) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b> En <b>' + alma + '</b>');
            $("#recepcion").val(ped);
            $.ajax({
                type: 'POST',
                url: 'requests/get_terminal_zone.php',
                data: {
                    zon: zon,
                    tip: tipo,
                    alma: alma
                }, success: function (data) {
                    $("#terminal").empty().append(data);
                    $('#assignRec').modal('show');
                }
            });

        }
        function asReceptionRe(ped, zon, $almacen) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b>');
            $("#recepcion").val(ped);
            $.ajax({
                type: 'POST',
                url: 'requests/get_terminal_zone.php',
                data: {
                    zon: zon,
                    alma: $almacen
                }, success: function (data) {
                    $("#terminal").empty().append(data);
                    $('#assignRec').modal('show');
                }
            });

        }
        function saveAssign() {
            var pedido = $("#recepcion").val();
            var terminal = $("#terminal").val();
            var codalma = $("#codalma").val();
            $.ajax({
                type: 'POST',
                url: 'requests/asignar_pedido_termial.php',
                data: {
                    pedido: pedido,
                    terminal: terminal,
                    codalma: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    $("#sndBtn").click();
                }
            });
        }
        var table;
        var details;
        function ckFields() {
            var flg = 0;
            var desPed = $("#desPed").val();
            var desPrv = $("#desPrv").val();
            var desArt = $("#desArt").val();
            var dFecCre = $("#dFecCre").val();
            var hFecCre = $("#hFecCre").val();
            var selCto = $("#selCto").val();
            var selSit = $("#selSit").val();
            var selCod = $("#selCod").val();
            var codalma = $("#codalma").val();

            $.ajax({
                type: 'POST',
                url: 'requests/getReportEmpa.php',
                async: false,
                data: {
                    desPed: desPed,
                    desPrv: desPrv,
                    desArt: desArt,
                    dFecCre: dFecCre,
                    hFecCre: hFecCre,
                    selCto: selCto,
                    selCod: selCod,
                    selSit: selSit,
                    codalma: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    populateDataTable('tblEmp', dt);
                }
            });
        };


        function populateDataTable(id, data) {
            //                var table;
            //se obtiene la instancia del datatables, si no existe, se crea           
            if ($.fn.dataTable.isDataTable('#' + id)) {

                table = $('#' + id).DataTable({ 'retrieve': true }).clear().draw();;
            }
            else {
                table = $("#" + id).DataTable({
                    "processing": true,
                    "bFilter": true, responsive: true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                        'excel'
                    ],
                    columnDefs: [{
                        className: 'details-control',
                        orderable: false,
                        targets: 0
                    }],
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
            var counter = 0;
            details = data.det;
            $.each(data.cab, function (k, v) {
                var ai = $("#" + id).dataTable().fnAddData([
                    '<span class="details-control"></span>',
                    v.pedexref,
                    v.pedexentre,
                    v.clirefer,
                    v.clinom,
                    v.pedclase,
                    v.pedexfec,
                    v.pedexhor,
                    v.almrefer,
                    v.siturefe

                ]);
            });
        }

        function format(d) {
            var cntnt = '<table class="table table-hover table-striped w-full" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';

            cntnt += '<thead>';
            cntnt += '<th></th>';
            cntnt += '<th>Pos.</th>';
            cntnt += '<th>Cod. Art.</th>';
            cntnt += '<th>Descripcion</th>';
            cntnt += '<th>Cant. Ped.</th>';
            cntnt += '<th>Cant. Prep.</th>';
            cntnt += '<th>Cant. Pend.</th>';
            cntnt += '<th>Fecha</th>';
            cntnt += '<th>Hora</th>';
            cntnt += '<th>Usuario</th>';


            cntnt += '</thead>';
            cntnt += '<tbody>';
            $.each(d, function (k, v) {
                cntnt += "<tr>";
                cntnt += '<td style="width:20px !important;"></td>';
                cntnt += '<td>' + v.pedpos + '</td>';
                cntnt += '<td>' + v.artrefer + '</td>';
                cntnt += '<td>' + v.artdesc + '</td>';
                cntnt += '<td>' + v.canpedi + '</td>';
                cntnt += '<td>' + v.canprepa + '</td>';
                cntnt += '<td>' + v.canpendi + '</td>';
                cntnt += '<td>' + v.fechcie + '</td>';
                cntnt += '<td>' + v.horcie + '</td>';
                cntnt += '<td>' + v.usuario + '</td>';

                if (v.usuario != null) {

                    cntnt += '<td>' + v.fecha + '</td>';
                    cntnt += '<td>' + v.usuario + '</td>';
                } else {
                    cntnt += '<td></td>';
                    cntnt += '<td></td>';
                }
                cntnt += "</tr>";
            })
            cntnt += '</tbody>';
            cntnt += '</table>';
            //            console.log(cntnt);
            // `d` is the original data object for the row
            return cntnt;
        }
        $(document).ready(function () {

            table = $("#tblEmp").DataTable({
                "processing": true,
                "bFilter": true,
                dom: '<"top"B<lfrtip>><"clear">',
                buttons: [
                    'excel'
                ],
                columnDefs: [{
                    className: 'details-control',
                    orderable: false,
                    targets: 0
                }],
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
            });


            $('#tblEmp tbody').on('click', '.details-control', function () {
                var table = $("#tblEmp").DataTable({ 'retrieve': true });
                var tr = $(this).closest('tr');
                //                    var dataTableRow = table.row( tr );
                var row = table.row(tr);
                var rowx = table.row(tr).data();
                
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child(format(details[rowx[1]]));
                    row.child.show();
                    tr.addClass('shown');

                }
            });

            $('#empa3').addClass('active');
            $('#e2').click();
            $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });

            $("#selCod").multiselect({
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
            $("#codalma").multiselect({
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