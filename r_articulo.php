<?php
require('conect.php');
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
                        . '<th colspan="3">Consulta de Artículos</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12"><br />

                                <!--                                    <div class="col-lg-3 col-sm-3" >
                                        <div class="input-group">
                                            <label class="label">Artículo:</label><div style="clear:both;"></div><br/>
                                            <input class="form-control" id="arti" name="arti" />
                                        </div>
                                    </div>-->
                                <!--<div class="col-lg-12 col-sm-12" ><br/>-->
                                <div class="col-lg-3 col-sm-3">
                                    <label class="label">Artículo:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <input class="form-control" placeholder="articulo" type="search" id="arti" name="arti" />
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <label class="label">Grupo de Artículo:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="grarti" name="grarti">
                                            <option value=""></option>
                                            <?php $sq = "select distinct  artgrup from arti";
                                            $rs = $db->query($sq);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['artgrup'] . '">' . $ax['artgrup'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <label class="label">Perfil de Series:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="peseries" name="peseries">
                                            <option value=""></option>
                                            <option value="ZCHA">ZCHA</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <label class="label">Propietario:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="priep" name="priep">
                                            <option value=""></option>
                                            <option value="LDAL">LDAL</option>
                                        </select>
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
                    <!--                        style="width:100%">
        <table width="100%"-->
                    <table class="table table-striped table-bordered" id="tblReg" style="font-size: 10px !important;">
                        <thead>
                            <tr>
                                <th>Articulo</th>
                                <th>Descripcion</th>
                                <th>unidad de Medida</th>
                                <th>Rotacion</th>
                                <th>Almacen</th>
                                <th>Perfil de Serie</th>
                                <th>Grupo de Articulo</th>
                                <th>EAN</th>
                                <th>Jerarquia</th>
                                <th>Peso</th>
                                <th>Volumen</th>
                                <th>Ancho</th>
                                <th>Largo</th>
                                <th>Presentacion</th>
                                <th>Fecha Articulo</th>
                                <th>Clase Valoracion</th>
                                <th>Cliente</th>
                                <th>Lote</th>
                                <th>Costo </th>
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
            var arti = $("#arti").val();
            var grarti = $("#grarti  option:selected").val();
            var peseries = $("#peseries  option:selected").val();
            var priep = $("#priep option:selected").val();
            let params = new URLSearchParams();
            params.set('arti', arti);
            params.set('grarti', grarti);
            params.set('peseries', peseries);
            params.set('priep', priep);
            url = 'requests/getArti.php' + '?' + params.toString();
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
                scrollX: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                },
                columns: [
                    { data: 'artrefer' },
                    { data: 'artdesc', orderable: false },
                    { data: 'unimed', orderable: false },
                    { data: 'artrot' },
                    { data: 'almcod', orderable: false },
                    { data: 'artser' },
                    { data: 'artgrup' },
                    { data: 'artean' },
                    { data: 'artjerar' },
                    { data: 'artpeso' },
                    { data: 'artvolum' },
                    { data: 'artancho' },
                    { data: 'artlargo' },
                    { data: 'presecod' },
                    { data: 'fecaut' },
                    { data: 'artval' },
                    { data: 'clirefer' },
                    { data: 'artlotemar' },
                    { data: 'costo' },
                ],
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

        $(document).ready(function () {
            $('#repor4').addClass('active');
            $('#c9').click();
            $('#recep').addClass('active');


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