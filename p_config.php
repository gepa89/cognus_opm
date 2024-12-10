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

        #tblReg tbody tr td:first-of-type {
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
                        . '<th colspan="3">Configuración de recorrido</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div style="clear:both;"></div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Almacen:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="codalma" name="codalma">
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
                                <div class="col-lg-12">
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="ckAsig">
                                            </span>
                                            <input disabled="disabled" type="text" class="form-control"
                                                value="Asignación Automática">
                                        </div><!-- /input-group -->
                                    </div><!-- /.col-lg-6 -->
                                </div><!-- /.col-lg-6 -->
                                <div style="clear:both;"></div><br />
                                <div class="col-lg-12">
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="ckSpex">
                                            </span>
                                            <input disabled="disabled" type="text" class="form-control"
                                                value="Separar Pedidos Expedicion ">
                                        </div><!-- /input-group -->
                                    </div><!-- /.col-lg-6 -->
                                </div><!-- /.col-lg-6 -->
                                <div style="clear:both;"></div><br />
                                <div class="col-lg-12">
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <label class="label">Preparación:</label>
                                            <div style="clear:both;"></div><br />
                                            <select class="form-control" id="selPrep">
                                                <option value="">Seleccionar</option>
                                                <option value="A1">Por Hora</option>
                                                <option value="A2">Por Franja</option>
                                                <option value="A3">Por Zona</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <label class="label">Lectura:</label>
                                            <div style="clear:both;"></div><br />
                                            <select class="form-control" id="selLect">
                                                <option value="">Seleccionar</option>
                                                <option value="B1">EAN</option>
                                                <option value="B2">Ubicación</option>
                                                <option value="B3">EAN / Ubicación</option>
                                            </select>
                                        </div>
                                    </div><!-- comment -->
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="ckFields()"
                                            class="form-control btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                    <div style="clear:both;"></div>
                </div><br />

            </div>
        </div>
    </div>
    <div style="clear:both;"></div>
    <?php
    include 'sidebar.php';
    include 'js_in.php';
    ?>
    <script type="text/javascript">
        var table;
        function ckFields() {
            var flg = 0;
            var ckAsig = $("#ckAsig").prop('checked');
            var ckSpex = $("#ckSpex").prop('checked');
            var selPrep = $("#selPrep").val();
            var codalma = $("#codalma").val();
            var selLect = $("#selLect").val();
            $.ajax({
                type: 'POST',
                url: 'requests/saveConf.php',
                data: {
                    ckAsig: ckAsig,
                    ckSpex: ckSpex,
                    selPrep: selPrep,
                    codalma: codalma,
                    selLect: selLect
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                }
            });
        }
        function loadConfig() {
            $.ajax({
                type: 'POST',
                data: { codalma: $("#codalma").val() },
                url: 'requests/getConf.php',
                success: function (data) {
                    var dt = JSON.parse(data);
                    console.log(dt);
                    if (dt.asig == 1) {
                        $("#ckAsig").prop('checked', true);
                    } else {
                        $("#ckAsig").prop('checked', false);
                    }
                    if (dt.estaparam == 1) {
                        $("#ckSpex").prop('checked', true);
                    } else {
                        $("#ckSpex").prop('checked', false);
                    }

                    $("#selPrep").val(dt.ruta);
                    $("#selLect").val(dt.lectura);

                }, error: function (e) {
                    console.log(e);
                }
            });
        }
        $(document).ready(function () {
            $('#param7').addClass('active');
            $('#c7').click();
            loadConfig();
            $("#codalma").change(function () {
                loadConfig();
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