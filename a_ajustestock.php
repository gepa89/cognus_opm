<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("ajustes")) {
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

        .label {
            color: #000;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
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
                        . '<th colspan="3">Ajustes de Stock</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <!--<div class="col-lg-12 col-sm-12" ><br/>-->
                            <div class="col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <input class="form-control" id="ubica" name="ubica" />
                                    <input class="form-control" type="hidden" id="byMatch" name="byMatch" />
                                    <span class="input-group-btn">
                                        <!--poner siempre la columna clave primero cuando hay mas de una-->
                                        <button class="btn btn-default" onclick="loadMatchModal('ubica','ubirefer', 'ubimapa', 1)" type="button"><span class="glyphicon glyphicon-search"></span></button>
                                    </span>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->

                            <!--<div class="col-lg-2 col-sm-2" >-->
                            <!--                                    <div class="input-group">
                                        <label class="label">Ubicación:</label><div style="clear:both;"></div><br/>
                                        <input class="form-control" id="ubica" name="ubica" />
                                    </div>-->
                            <!--</div>-->

                            <!--</div>-->

                        </form>
                        <form method="post" id="eForm">
                            <div class="col-lg-2 col-sm-2">
                                <!--<label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>-->
                                <div class="input-group pull-left">
                                    <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Cargar</button>
                                </div>
                                <div class="input-group pull-right">
                                    <button type="button" id="sndNwBtn" onclick="ckNwFields()" title="nuevo ajuste" class="form-control btn btn-success">Nuevo</button>
                                </div>
                            </div>
                        </form>
                        <form method="post" id="eForm">
                            <div class="col-lg4 col-sm-12">
                                <div class="input-group">
                                    <label class="label" style="color:#000;">Seleccionar Almacen:</label>
                                    <div style="clear:both;"></div><br />
                                    <select id="codalma" class="form-control" required>
                                        <?php
                                        $sq = "select * from alma";
                                        $rs = $db->query($sq);
                                        echo '<option value="">Seleccionar Almacen</option>';
                                        while ($ax = $rs->fetch_assoc()) {
                                            echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . utf8_encode($ax['almdes']) . '</option>';
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div style="clear:both;"></div>
                </div><br />
                <div class="row">
                    <div class="col-lg-12 col-sm-12"><br />
                        <div class="col-lg-4 col-sm-4" id="cntSel"><br />
                        </div>
                        <div style="clear:both;"></div><br />
                        <div class="col-lg-6 col-sm-6" id="cntTbl"><br />
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
        var dataaRT = tipaaRT = movaaRT = dataaRTPrev = {};

        function asReception(ped) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b>');
            $("#recepcion").val(ped);
            $('#assignRec').modal('show');
        }

        
        var table;

        function ckNwFields() {
            var ubica = $("#ubica").val();
            <?php
            echo "dataaRTPrev = " . json_encode(
                array(
                    'ubirefer' => '',
                    'artrefer' => '',
                    'preseref' => 'UN',
                    'canpresen' => 0,
                    'artdesc' => 0,
                    'stock' => 0
                )
            ) . ";";
            $tipoTrans = array();
            $sq = "select movref, movdes, ensal, descsen from artmov where pedclase = 'AJS' and ensal='in'";

            //    echo $sq;
            $rs = $db->query($sq);
            $cc = 0;
            while ($row = $rs->fetch_assoc()) {
                $tipoTrans[$row['movref']]['movref'] = $row['movref'];
                $tipoTrans[$row['movref']]['movdesc'] = $row['movdes'];
                $tipoTrans[$row['movref']]['ensal'] = $row['ensal'];
                $tipoTrans[$row['movref']]['descsen'] = $row['descsen'];
                $cc++;
            }
            echo "tipaaRT = " . json_encode($tipoTrans) . ";";
            $motAjs = array();
            $sq = "select ajuref, ajudes, ajumov from ajutip";

            //    echo $sq;
            $rs = $db->query($sq);
            $cc = 0;
            while ($row = $rs->fetch_assoc()) {
                $motAjs[$row['ajuref']]['ajuref'] = $row['ajuref'];
                $motAjs[$row['ajuref']]['ajudes'] = $row['ajudes'];
                $motAjs[$row['ajuref']]['ajumov'] = $row['ajumov'];
                $cc++;
            }
            echo "movaaRT = " . json_encode($motAjs) . ";";
            ?>

            console.log(tipaaRT);
            console.log(movaaRT);
            $("#cntTbl").empty();
            loadArtDataNw();
        }

        function ckFields() {
            var ubica = $("#ubica").val();
            var codalma = $("#codalma").val();
            if (ubica == '' && codalma == '') {
                ubica = $("#byMatch").val();
                codalma = $("#codalma").val();
            }
            $("#cntSel").empty();
            $("#cntTbl").empty();
            $.ajax({
                type: 'POST',
                url: 'requests/getArbUbic.php',
                data: {
                    ubica: ubica,
                    codalma: codalma
                },
                success: function(data) {
                    var dt = JSON.parse(data);
                    //                        //console.log(dt);
                    tipaaRT = dt.tmov;
                    movaaRT = dt.mmov;
                    var selCon = '';
                    selCon = '<select class="form-control" id="artList" name="artList" onchange="loadArtData()">';
                    selCon += '<option value="">Seleccionar</option>';
                    $.each(dt.dat, function(k, v) {
                        dataaRT[k] = v;
                        selCon += '<option value="' + k + '">' + k + ' - ' + v.artdesc + '</option>';
                    });
                    //console.log(dataaRT);
                    selCon += '</select>';
                    $("#cntSel").empty().append(selCon);
                }
            });

        }

        function loadArtData() {
            var sltd = $("#artList").val();
            var dat = dataaRT[sltd];
            cantPres = cantUni = 0;
            var lblStock = '';
            cantPres = dat.stock / dat.canpresen;
            cantUni = Math.trunc(dat.stock % dat.canpresen);
            if (cantPres == "Infinity" || cantPres == "NaN") {
                lblStock = dat.stock_formateado;
            } else {
                lblStock = dat.stock_formateado + " " + dat.preseref + "; " + cantUni + " " + 'UN';
            }

            var tvlcntnt = '';
            tvlcntnt = '<input class="form-control" id="ajUbi" name="ajUbi" type="hidden" value="' + dat.ubirefer + '"/><input class="form-control" id="ajArt" name="ajArt" type="hidden" value="' + dat.artrefer + '"/><table style="white-space: nowrap;" class="table table-bordered"><tbody>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ubicación</td><td>' + dat.ubirefer + '</td>';
            tvlcntnt += '</tr>';
            if (dat.artlotemar === "SI") {
                lblStock = "";
                tvlcntnt += '<tr>';
                tvlcntnt += '<td>Ajuste Lote</td><td><select class="form-control" id="ajuste_lote" onchange=cambioLote(this) name="ajuste_lote"/></td>';
                tvlcntnt += '</tr>';
            }
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Artículo</td><td>' + dat.artrefer + '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Stock Actual</td><td>';
            tvlcntnt += '<p id="cantidad_actual_texto">' + lblStock + '</p>';
            tvlcntnt += '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ajuste por Presentación (' + dat.preseref + ')</td><td><input class="form-control" id="ajPres" name="ajPres" type="number"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ajuste por Unidad</td><td><input class="form-control" id="ajUni" name="ajUni" type="number"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Tipo Ajuste</td><td>';
            tvlcntnt += '<select class="form-control" id="ajTip" name="ajTip" onchange="loadMotiAj()">';
            tvlcntnt += '<option value="">Seleccionar</option>';
            $.each(tipaaRT, function(k, v) {
                tvlcntnt += '<option value="' + v.movref + '">' + v.movref + ' - ' + v.movdesc + '</option>';
            });
            tvlcntnt += '</select>';
            tvlcntnt += '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Motivo Ajuste</td><td>';
            tvlcntnt += '<select class="form-control" id="ajMot" name="ajMot">';
            tvlcntnt += '<option value="">Seleccionar</option>';
            tvlcntnt += '</select>';
            tvlcntnt += '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Fecha ajuste</td>';
            tvlcntnt += '<td><input class="form-control" id="fecha_ajuste" name="fecha_ajuste" type="date"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td colspan="2"><div class="input-group"><button type="button" id="sndBtn" onclick="saveAdjs()" class="form-control btn btn-success">Guardar</button></div></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '</tbody>';
            $("#cntTbl").empty().append(tvlcntnt);
            $('#ajuste_lote').empty();
            $('#ajuste_lote').append("<option value=\"\" selected disabled> -- Seleccione -- </option>");
            $.ajax({
                type: 'POST',
                url: 'requests/getArtLote.php',
                data: {
                    articulo: dat.artrefer,
                    cod_alma: dat.cod_alma,
                    ubicacion: dat.ubirefer
                },
                success: function(data) {
                    for (const dato of data.datos) {
                        let opcion = `<option data-cantidad='${dato.cantidad}' value='${dato.artlote}'>${dato.artlote}</option>`;
                        $('#ajuste_lote').append(opcion);
                    }
                }
            });

        }

        function cambioLote(e) {
            console.log($(e).val());
            let cantidad = $('option:selected', e).data("cantidad");
            $("#cantidad_actual_texto").empty().text(cantidad);
        }

        function loadArtDataNw() {
            var ubica = $("#ubica").val();
            var codalma = $("#codalma").val();
            $("#cntSel").empty();
            var tvlcntnt = '';
            tvlcntnt = '<input class="form-control" id="ajUbi" name="ajUbi" type="hidden" value="' + ubica + '"/><input class="form-control" id="ajArt" name="ajArt" type="hidden" value=""/><table style="white-space: nowrap;" class="table table-bordered"><tbody>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ubicación</td><td><input class="form-control" onblur="addArtVal()" id="artUbi" name="artUbi" type="text" value="' + ubica + '"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Artículo</td><td><input class="form-control" onblur="addArtVal()" id="artList" name="artList" type="text"/><td></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ajuste Lote</td><td><select class="form-control" id="ajuste_lote_nuevo" onchange=cambioLote(this) name="ajuste_lote"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr style="display:none">';
            tvlcntnt += '<td>Ajuste por Presentación</td><td><input class="form-control" id="ajPres" name="ajPres" type="number"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Ajuste por Unidad</td><td><input class="form-control" id="ajUni" name="ajUni" type="number"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Tipo Ajuste</td><td>';
            tvlcntnt += '<select class="form-control" id="ajTip" name="ajTip" onchange="loadMotiAj()">';
            tvlcntnt += '<option value="">Seleccionar</option>';
            $.each(tipaaRT, function(k, v) {
                tvlcntnt += '<option value="' + v.movref + '">' + v.movref + ' - ' + v.movdesc + '</option>';
            });
            tvlcntnt += '</select>';
            tvlcntnt += '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Motivo Ajuste</td><td>';
            tvlcntnt += '<select class="form-control" id="ajMot" name="ajMot">';
            tvlcntnt += '<option value="">Seleccionar</option>';
            tvlcntnt += '</select>';
            tvlcntnt += '</td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td>Fecha ajuste</td>';
            tvlcntnt += '<td><input class="form-control" id="fecha_ajuste" name="fecha_ajuste" type="date"/></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '<tr>';
            tvlcntnt += '<td colspan="2"><div class="input-group"><button type="button" id="sndBtn" onclick="saveAdjs()" class="form-control btn btn-success">Guardar</button></div></td>';
            tvlcntnt += '</tr>';
            tvlcntnt += '</tbody>';
            $("#cntTbl").empty().append(tvlcntnt);

        }

        function addArtVal() {
            $('#ajUbi').val($('#artUbi').val());
            $('#ajArt').val($('#artList').val());
            dataaRT[$('#ajArt').val()] = dataaRTPrev;
        }

        function saveAdjs() {
            var ajUbi = $("#ajUbi").val();
            var ajArt = $("#ajArt").val();
            var ajPres = Math.abs($("#ajPres").val());
            var ajUni = Math.abs($("#ajUni").val());
            var ajTip = $("#ajTip").val();
            var ajMot = $("#ajMot").val();
            var lote = $("#ajuste_lote").val();
            var fecha_ajuste = $("#fecha_ajuste").val();
            if (dataaRT[ajArt].artlotemar === "SI" && !lote) {
                alert("Seleccione Lote");
                return;
            }
            var ajCPres = dataaRT[ajArt].canpresen;
            var ajSAct = dataaRT[ajArt].stock;
            var ajDir = dataaRT[ajArt].ajumov;
            var codalma = $("#codalma").val();
            if (ajUni != 0 || ajPres != 0) {
                if (ajTip != "") {
                    if (ajMot != "") {

                        if (codalma != "") {
                            datos = {
                                ajUbi: ajUbi,
                                ajArt: ajArt,
                                ajCPres: ajCPres,
                                ajPres: ajPres,
                                ajUni: ajUni,
                                ajTip: ajTip,
                                ajMot: ajMot,
                                ajSAct: ajSAct,
                                ajDir: ajDir,
                                lote: lote,
                                codalma: codalma,
                                fecha_ajuste: fecha_ajuste
                            };
                            $.ajax({
                                type: 'POST',
                                url: 'requests/saveAjd.php',
                                data: datos,
                                success: function(data) {
                                    var dt = JSON.parse(data);
                                    alert(dt.msg);
                                    if (dt.err == 0) {
                                        $("#cntSel").empty();
                                        $("#cntTbl").empty();
                                    }
                                }
                            });

                        } else {
                            alert("Debe indicar el Almacen.");
                        }



                    } else {
                        alert("Debe indicar motivo de ajuste.");
                    }
                } else {
                    alert("Debe indicar tipo de ajuste.");
                }
            } else {
                alert("No se puede registrar Ajuste en ceros.");
            }
        }

        function loadMotiAj() {
            var tipo = $("#ajTip").val();
            //                console.log(tipo);
            var cntnt = '';
            cntnt += '<option value=""></option>';
            $.each(movaaRT, function(k, v) {
                if (tipaaRT[tipo].ensal == v.ajumov) {
                    cntnt += '<option value="' + v.ajuref + '">' + v.ajuref + ' - ' + v.ajudes + '</option>';
                }
            });
            $("#ajMot").empty().append(cntnt);
        }

        function populateDataTable(id, data) {
            //                var table;
            //se obtiene la instancia del datatables, si no existe, se crea           
            if ($.fn.dataTable.isDataTable('#' + id)) {

                table = $('#' + id).DataTable({
                    'retrieve': true
                }).clear().draw();;
                //console.log(table);
            } else {
                table = $("#" + id).DataTable({
                    "processing": true,
                    responsive: true,
                    "bFilter": true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                        'excel'
                    ],
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todo"]
                    ],
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
                //                    //console.log(table);
            }
            $.each(data.cab, function(k, v) {
                //                //console.log(v.rg_bldat);
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
                var ai = $("#" + id).dataTable().fnAddData([
                    v.nrodoc,
                    v.posi,
                    v.arti,
                    v.artides,
                    v.coprov,
                    v.almacen,
                    v.cliente,
                    v.fecierre,
                    v.horcierre,
                    v.cladoc,
                    v.canprep,
                    v.userprep,
                    v.movref
                ]);

            });
        }

        $(document).ready(function() {
            $('#aj1').addClass('active');
            $('#c10').click();
            $('#recep').addClass('active');



            $("#alma").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });
            $("#clamov").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });
            $("#pedclase").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });


            table = $("#tblReg").DataTable({
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
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Todo"]
                ],
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


            $('.input-daterange input').datepicker({
                dateFormat: 'dd-mm-yy'
            });
            $("#selCodExp").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });

            $("#selCto").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
                        return options.length + ' selecionado(s)';
                    }
                }
            });
            $("#selSit").multiselect({
                selectAllText: 'Todos',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonText: function(options) {
                    if (options.length === 0) {
                        return 'Ninguno';
                    } else if (options.length > 0) {
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

            this.pad = function(what) {
                var s = what.toString();
                return pads.substring(0, pads.length - s.length) + s;
            };
        }
    </script>
</body>

</html>