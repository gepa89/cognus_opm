<?php

require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("maquinista")) {
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

        #tblmaq tbody tr td:first-of-type {
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
                        . '<th colspan="5">Trabajos de Maquinistas</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="col-lg-2 col-sm-4">
                                    <div class="input-group">
                                        <label class="label"># Pedido:</label>
                                        <div style="clear:both;"></div><br />
                                        <input class="form-control" id="desPed" name="desPed"
                                            value="<?php echo $pd; ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-4">
                                    <div class="input-group">
                                        <label class="label">Proveedor:</label>
                                        <div style="clear:both;"></div><br />
                                        <input class="form-control" id="desPrv" name="desPrv"
                                            value="<?php echo $prv; ?>" />
                                    </div>
                                </div><!-- comment -->
                                <div class="col-lg-2 col-sm-4">
                                    <div class="input-group">
                                        <label class="label">Articulos:</label>
                                        <div style="clear:both;"></div><br />
                                        <input class="form-control" id="desArt" name="desArt"
                                            value="<?php echo implode(',', $arArt); ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Almacen:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="codalma" name="codalma[]" multiple="multiple">
                                            <?php
                                            $sql = "select * from alma";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . $ax['almdes'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12"><br />
                                <div class="col-lg-5 col-sm-5">
                                    <label class="label">Fecha Creación:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group input-daterange">
                                        <input autocomplete="off" type="text" name="dFecCre" id="dFecCre"
                                            class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar"
                                            value="<?php echo (@$_POST["dFecCre"]) ? @$_POST["dFecCre"] : date("d-m-Y", strtotime("now")); ?>" />
                                        <div class="input-group-addon"> hasta </div>
                                        <input autocomplete="off" type="text" name="hFecCre" id="hFecCre"
                                            class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar"
                                            value="<?php echo (@$_POST["hFecCre"]) ? @$_POST["hFecCre"] : date("d-m-Y", strtotime("now")); ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Clase:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selCto" name="selCto[]" multiple="multiple">

                                            <?php
                                            $sql = "select distinct pedclase from pedexcab where pedclase <> null or pedclase <> ''";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['pedclase'] . '">' . $ax['pedclase'] . '</option>';
                                            }
                                            ?>
                                            <!--                                                <option value="EUB">EUB</option>
                                                <option value="ZUB">ZUB</option>
                                                <option value="ZCL">ZCL</option>
                                                <option value="ZNB">ZNB</option>
                                                <option value="ZCON">ZCON</option>
                                                <option value="ZCRE">ZCRE</option>
                                                <option value="ZCOS">ZCOS</option>
                                                <option value="REPO">REPO</option>-->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Situación:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selSit" name="selSit[]" multiple="multiple">
                                            <?php
                                            $sql = "select * from situped";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['siturefe'] . '">' . $ax['siturefe'] . ' - ' . $ax['situdes'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Cod. Envío:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selCod" name="selCod[]" multiple="multiple">
                                            <?php
                                            $sql = "select distinct codenv from pedexcab where codenv <> null or codenv <> ''";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['codenv'] . '">' . $ax['codenv'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
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
                        . '<th colspan="5"></th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <!-- table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg" style="font-size: 12px !important;">
                       -->
                    <table width="100%" class="table table-hover table-striped table-bordered  table-condensed"
                        id="tblmaq" style="font-size: 12px !important;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Pedido</th>
                                <th>OC Referencia</th>
                                <th>Fecha Creacion</th>
                                <th>Hora Creacion</th>
                                <th>Clas.Doc.</th>
                                <th>Sit.Ped</th>
                                <th>Almacen</th>
                                <th>Planificar</th>
                                <th>Enviar</th>
                                <th>Acción</th>
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
                                            <input type="text" readonly id="selected_almacen" class="form-control"
                                                value="" />
                                            <select class="form-control" id="terminal" name="terminal">
                                                <?php
                                                $sq = "select * from termi where tipac <> 'RECE'";
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
        function asReceptionRe(ped, tipo, almacen) {
            zon = "todos";
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b>');
            $("#recepcion").val(ped);
            $.ajax({
                type: 'POST',
                url: 'requests/get_terminal_zone.php',
                data: {
                    zon: zon,
                    tip: tipo,
                    alma: almacen
                }, success: function (data) {
                    $("#terminal").empty().append(data);
                    $("#selected_almacen").val(almacen);
                    $('#assignRec').modal('show');
                }
            });

        }
        function saveAssign() {
            var pedido = $("#recepcion").val();
            var terminal = $("#terminal").val();
            var codalma = $("#selected_almacen").val();
            $.ajax({
                type: 'POST',
                url: 'requests/asignar_pedido_termial.php',
                data: {
                    pedido: pedido,
                    terminal: terminal,
                    almacen: codalma
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
                url: 'requests/getMaquinista.php',
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
                    populateDataTable('tblmaq', dt);
                }
            });
        }
        function sendExpedition(pedido, pedubicod) {
            $.ajax({
                type: 'POST',
                url: 'requests/send_to_sap.php',
                data: {
                    pedido: pedido,
                    pedubicod: pedubicod
                },
                success: function(data) {
                    var dt = data;
                    alert(dt.msg);
                    if (dt.error == 0) {
                        $("#sndBtn").click();
                    }
                },
                error: function(data) {
                    console.log(data);
                    var dt = JSON.parse(data);
                    alert(dt.msg);

                }
            });
        }

        function populateDataTable(id, data) {
            //                var table;
            //se obtiene la instancia del datatables, si no existe, se crea           
            if ($.fn.dataTable.isDataTable('#' + id)) {

                table = $('#' + id).DataTable({ 'retrieve': true }).clear().draw();;
                //                    console.log(table);
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
                    v.pedubicod,
                    v.pedrefer,
                    v.fecubi,
                    v.horubi,
                    v.pedclase,
                    v.situped,
                    v.cod_alma,
                    v.pedaction,
                    v.pedsendi,
                    v.pedactio
                ]);
            });
        }

        function format(d) {
            console.log(d);
            var cntnt = '<table class="table table-hover table-striped w-full" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';

            cntnt += '<thead>';
            cntnt += '<th></th>';
            cntnt += '<th>Pos.</th>';
            cntnt += '<th>Cod. Art.</th>';
            cntnt += '<th>Descripcion</th>';
            cntnt += '<th>Cant. Ped.</th>';
            cntnt += '<th>Cant. Ubi.</th>';
            cntnt += '<th>Cant. Pend.</th>';
            cntnt += '<th>Multiref.</th>';
            cntnt += '<th>Muelle</th>';
            cntnt += '<th>Usuario</th>';
            cntnt += '<th>Fecha</th>';


            cntnt += '</thead>';
            cntnt += '<tbody>';
            $.each(d, function (k, v) {
                cntnt += "<tr>";
                cntnt += '<td style="width:20px !important;"></td>';
                cntnt += '<td>' + v.posubi + '</td>';
                cntnt += '<td>' + v.artrefer + '</td>';
                cntnt += '<td>' + v.artdesc + '</td>';
                cntnt += '<td>' + v.cantiu + '</td>';
                cntnt += '<td>' + v.canubi + '</td>';
                cntnt += '<td>' + v.canupen + '</td>';
                cntnt += '<td>' + v.etnum + '</td>';
                cntnt += '<td>' + v.muelle + '</td>';
                cntnt += '<td>' + v.usuario + '</td>';
                cntnt += '<td>' + v.fecha + '</td>';

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

            table = $("#tblmaq").DataTable({
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


            $('#tblmaq tbody').on('click', '.details-control', function () {
                var table = $("#tblmaq").DataTable({ 'retrieve': true });
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

            $('#maqui5').addClass('active');
            $('#c5').click();
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