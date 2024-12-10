<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("maestros")) {
    echo "No tiene acceso";
    exit;
}

$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
    <style>
        td.details-control {
            background: url('images/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('images/details_close.png') no-repeat center center;
        }

        .table>tbody>tr>th {

            width: 150px !important;
        }

        .table>tbody>tr>td>input {

            width: 100% !important;
        }

        .hiddn {
            display: none;
        }
    </style>
    <div id="maincontainer" class="clearfix">
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="row">
                            <?php

                            //                var_dump($_POST);
                            echo '<table class="table">'
                                . '<thead>'
                                . '<tr>'
                                . '<th colspan="2">Consulta productos.</th>'
                                . '</tr>'
                                . '</thead>'
                                . '<tbody>'
                                . '<tr>'
                                . '</tr>'
                                . '</tbody>'
                                . '</table>'; ?>
                            <div style="clear:both;"></div><br />

                            <form id="filter">
                                <!--                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group input-daterange">
                                        <input type="text" id="field1" name="field1" class="form-control" placeholder="Material" />
                                    </div>
                                </div>-->
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <input type="text" id="field1" name="field1" class="form-control"
                                            placeholder="Material" />
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default"
                                                onclick="loadMatchModal('field1','artrefer,artdesc', 'arti', 1)"
                                                type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">

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
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-1 col-sm-1">
                                    <div class="input-group">
                                        <button class="form-control btn btn-primary" type="button"
                                            onclick="chkFlds()">Buscar</button>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <div class="input-group">
                                        <img id="loading"
                                            style="position:relative; width: 55% !important; height: 55% !important;"
                                            class="hiddn" src="images/cargando1.gif" />
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div><br />
                            <?php

                            //                var_dump($_POST);
                            echo '<table class="table">'
                                . '<thead>'
                                . '<tr>'
                                . '<th colspan="2"></th>'
                                . '</tr>'
                                . '</thead>'
                                . '<tbody>'
                                . '<tr>'
                                . '</tr>'
                                . '</tbody>'
                                . '</table>'; ?>
                        </div>
                        <div class="row">

                            <div class="col-lg-6 col-sm-5 col-md-5">

                                <h3 class="heading">Detalles</h3><!-- comment -->
                                <div class="row">
                                    <div id="tabl">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Artículo</strong></th>
                                                        <td><input id="mnInArt" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Descripción</strong>
                                                        </th>
                                                        <td><input id="mnInDesc" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Lote</strong></th>
                                                        <td><input id="mnInLot" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>EAN</strong></th>
                                                        <td><input id="mnInEan" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Gr. Artículo</strong>
                                                        </th>
                                                        <td><input id="mnInGrar" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Nro. Serie</strong>
                                                        </th>
                                                        <td><input id="mnInNroser" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>ABC Logístico</strong>
                                                        </th>
                                                        <td><input id="mnInAbc" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Propietario</strong>
                                                        </th>
                                                        <td><input id="mnInProp" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Nombre</strong></th>
                                                        <td><input id="mnInNom" class="" value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="pl-0 w-25" scope="row"><strong>Unidad de
                                                                Medida</strong></th>
                                                        <td><input id="mnInUm" class="" value="" /></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-4 col-xs-12">
                                <h3 class="heading">Herramientas</h3>
                                <ul class="dshb_icoNav clearfix">
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp1')"
                                            style="background-image: url(img/gCons/screen.png);">Datos Picking</a>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp2')"
                                            style="background-image: url(img/gCons/tag.png);">Presentaciones</a>
                                    </li>
                                    <!--<li><a href="javascript:void(0)" onclick="swModal('mdPp3')" style="background-image: url(img/gCons/connections.png);">Stock Acumulado</a></li>-->
                                    <!--<li><a href="javascript:void(0)" onclick="swModal('mdPp4')" style="background-image: url(img/gCons/connections.png);">Movimientos</a></li>-->
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp5')"
                                            style="background-image: url(img/gCons/bar-chart.png);">Datos EAN</a></li>
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp6')"
                                            style="background-image: url(img/gCons/cassette.png);">Nros. De Serie</a>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp7')"
                                            style="background-image: url(img/gCons/lookup.png);">Stock p/ Ubi.</a>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp8')"
                                            style="background-image: url(img/gCons/van.png);">Expedición</a>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="swModal('mdPp9')"
                                            style="background-image: url(img/gCons/container.png);">Recepción</a></li>
                                </ul>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="slider">

                                </div>
                            </div>
                        </div>
                        <div class="modal fade bd-example-modal-lg" id="modalData" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" style="overflow-x:auto;">
                                    </div>

                                    <div class="modal-footer">
                                        <!--<button type="button" onclick="clsDetail()" id="btnClsPk" class="btn btn-default"><i class="icon-adt_trash"></i></button>-->
                                        <!--<button type="button" disabled="disabled" id="btnPk" onclick="btnVlnSv()" class="btn btn-primary">Guardar</button>-->
                                        <!--                                <button type="button" disabled="disabled" id="btnPk" onclick="btnVlnPk()" class="btn btn-primary">Picking</button>
                                                <button type="button" disabled="disabled" id="btnAnl" onclick="btnVlnAnl()" class="btn btn-warning">Anulación</button>-->
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <?php
    include 'modal_match.php';
    include 'sidebar.php';
    include 'js_in.php';
    ?>
    <script>
        function swModal(id) {
            var art = $("#mnInArt").val();
            var codalma = $("#codalma").val();
            if (art != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/getDatModal.php',
                    data: {
                        id: id,
                        art: art,
                        codalma: codalma
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        if (!dt.err) {
                            var mytable = '<table class="table table-hover table-striped table-bordered  table-condensed"><thead>' + dt.hdr + '</thead><tbody>';

                            $.each(dt.cab, function (key, value) {
                                mytable += "<tr>";
                                $.each(value, function (k, v) {
                                    mytable += "<td>" + v + "</td>";
                                });
                                mytable += "</tr>";
                            });
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-body").empty().append(mytable);
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-title").empty().append(dt.tit);
                            $('#modalData').modal('show');
                        } else {
                            alert(dt.msg);
                        }
                    }
                });
            } else {
                alert("Debe consultar un artículo.");
            }
            //                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
        }
        $(document).ajaxStart(function () {
            $("#loading").removeClass('hiddn');
        });
        $(document).ajaxComplete(function () {
            $("#loading").addClass('hiddn');
        });
        $(document).ready(function () {
            $("#field1").focus();
            $('#maest1').addClass('active');
            $('#c4').click();
            $('#field1').scannerDetection({
                onComplete: function () {
                    chkFlds();
                }
            });
        });
        var slider;
        function loadFlex() {
            if (typeof (slider) == 'object') {
                //                    console.log(typeof(slider));
                slider.reloadSlider();
            } else {
                slider = $('.slider').bxSlider({
                    preloadImages: 'all'
                });
            }

        };
        function chkFlds() {
            var mat = $('#field1').val();
            var codalma = $('#codalma').val();
            var desc = "";
            $.ajax({
                type: 'POST',
                url: 'requests/getDatArt.php',
                data: {
                    mat: mat,
                    desc: desc,
                    codalma: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    if (!dt.err) {
                        var cntul = '';
                        var mats = dt.dat[0];
                        console.log(mats.artdesc);
                        $("#mnInArt").val(mats.artrefer);
                        $("#mnInDesc").val(mats.artdesc);
                        $("#mnInAbc").val(mats.artrot);
                        $("#mnInEan").val(mats.artean);
                        $("#mnInGrar").val(mats.artgrup);
                        $("#mnInNroser").val(mats.artser);
                        $("#mnInAbc").val("");
                        $("#mnInProp").val(mats.almcod);
                        $("#mnInNom").val(mats.clinom);
                        $("#mnInUm").val(mats.unimed);

                    } else {
                        alert(dt.msg);
                    }
                }
            });
        }
    </script>
</body>

</html>