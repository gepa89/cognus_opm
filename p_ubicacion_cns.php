<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("configuraciones")) {
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
                        . '<th colspan="3">Consulta de Ubicaciones</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12"><br />
                                <div class="col-lg-4 col-sm-xs">

                                    <div class="col-lg4 col-sm-12">
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
                                </div>
                                <div class="col-lg-4 col-sm-xs">

                                    <div class="col-lg4 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Filtrar:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" class="form-control" id="buscar" name="buscar">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-12"><br />
                                    <div class="col-lg-4 col-sm-xs">
                                        <label class="label">Estantes:</label>
                                        <div style="clear:both;"></div><br />
                                        <div class="input-group ">
                                            <input type="text" name="estDesde" id="estDesde" class="form-control"
                                                value="<?php echo @$_POST["estDesde"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('estDesde','ubcod', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                            <div class="input-group-addon"> hasta </div>
                                            <input type="text" name="estHasta" id="estHasta" class="form-control"
                                                value="<?php echo @$_POST["estHasta"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('estHasta','ubcod', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-xs-12">
                                        <label class="label">Huecos:</label>
                                        <div style="clear:both;"></div><br />
                                        <div class="input-group ">
                                            <input type="text" name="huecDesde" id="huecDesde" class="form-control"
                                                value="<?php echo @$_POST["huecDesde"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('huecDesde','ubdes', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                            <div class="input-group-addon"> hasta </div>
                                            <input type="text" name="huecHasta" id="huecHasta" class="form-control"
                                                value="<?php echo @$_POST["huecHasta"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('huecHasta','ubhas', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-xs-12">
                                        <label class="label">Niveles:</label>
                                        <div style="clear:both;"></div><br />
                                        <div class="input-group ">
                                            <input type="text" name="nivDesde" id="nivDesde" class="form-control"
                                                value="<?php echo @$_POST["nivDesde"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('nivDesde','ubniv', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                            <div class="input-group-addon"> hasta </div>

                                            <input type="text" name="nivHasta" id="nivHasta" class="form-control"
                                                value="<?php echo @$_POST["nivHasta"] ?>" />
                                            <span class="input-group-btn">
                                                <!--poner siempre la columna clave primero cuando hay mas de una-->
                                                <button class="btn btn-default"
                                                    onclick="loadMatchModal('nivHasta','ubniv', 'ubica', 1)"
                                                    type="button"><span
                                                        class="glyphicon glyphicon-search"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <!--                                    <div class="col-lg-3 col-sm-3">
                                        <label class="label">Sub Niveles:</label><div style="clear:both;"></div><br/>
                                        <div class="input-group ">
                                            <input type="text" name="sbnDesde" id="sbnDesde" class="form-control"  value="<?php echo @$_POST["sbnDesde"] ?>"/>
                                            <div class="input-group-addon"> hasta </div>
                                            <input type="text" name="sbnHasta" id="sbnHasta" class="form-control"  value="<?php echo @$_POST["sbnHasta"] ?>"/>
                                        </div>
                                    </div>                        -->
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="ckFields()"
                                            class="form-control btn btn-primary">Buscar</button>
                                        <button type="button" id="descargar_reporte" disabled class="form-control btn btn-success">Descargar
                                            Reporte</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <br><br><br>
                        <table class="table table-hover table-striped table-bordered table-condensed" id="tblReg">
                            <thead>
                                <tr>
                                    <th>Ubicación</th>
                                    <th>Z. Preparación</th>
                                    <th>Z. Almacenaje</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Situación</th>
                                    <th>Grupo.Ubicacion</th>
                                    <th>Dimension</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal -->
                <div class=" modal fade bd-example-modal-sm" id="assignRec" tabindex="-1" role="dialog"
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
        var params = null;

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
        var table = null;
        function ckFields() {
            var flg = 0;
            var codalma = $("#codalma").val();
            var estDesde = $("#estDesde").val();
            var estHasta = $("#estHasta").val();
            var huecDesde = $("#huecDesde").val();
            var huecHasta = $("#huecHasta").val();
            var nivDesde = $("#nivDesde").val();
            var nivHasta = $("#nivHasta").val();
            var sbnDesde = $("#sbnDesde").val();
            var sbnHasta = $("#sbnHasta").val();
            var buscar = $("#buscar").val();
            params = new URLSearchParams();
            params.set('codalma', codalma);
            params.set('estDesde', estDesde);
            params.set('estHasta', estHasta);
            params.set('huecDesde', huecDesde);
            params.set('huecHasta', huecHasta);
            params.set('nivDesde', nivDesde);
            params.set('nivHasta', nivHasta);
            params.set('buscar', buscar);
            $("#descargar_reporte").removeAttr('disabled');

            url = 'requests/getLocation.php' + '?' + params.toString();
            if (table != null) {
                table.destroy();
                table = null;
            }
            table = $('#tblReg').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                ajax: url,
                searching: false,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                },
                columns: [
                    { data: 'ubirefer' },
                    { data: 'zoncodpre', orderable: false },
                    { data: 'zoncodalm', orderable: false },
                    { data: 'ubitipo' },
                    { data: 'ubiestad', orderable: false },
                    { data: 'ubisitu' },
                    { data: 'tipoubi' },
                    { data: 'dimension' },
                ],
            });
            /*$.ajax({
                type: 'POST',
                url: 'requests/getLocation.php',
                data: {
                    codalma: codalma,
                    estDesde: estDesde,
                    estHasta: estHasta,
                    huecDesde: huecDesde,
                    huecHasta: huecHasta,
                    nivDesde: nivDesde,
                    nivHasta: nivHasta,
                    sbnDesde: sbnDesde,
                    sbnHasta: sbnHasta
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    populateDataTable('tblReg', dt);
                }
            });*/
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

        $(document).ready(function () {
            $('#param4').addClass('active');
            $('#c7').click();
            $('#recep').addClass('active');

            $('#eprod').addClass('active');
            $('#c1').click();
            $("#descargar_reporte").click(function (e) {
                location.href = 'requests/exportar_ubicaciones.php?' + params.toString();

            })
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