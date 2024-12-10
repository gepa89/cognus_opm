<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("seguridad")) {
    echo "No tiene acceso";
    exit;
}


$db = new mysqli($SERVER, $USER, $PASS, $DB);
// echo "<pre>"; var_dump($data);echo "</pre>";
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
    </style>
    <div id="maincontainer" class="clearfix">
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?php

                        echo '<table class="table">'
                            . '<thead>'
                            . '<tr>'
                            . '<th colspan="2">Consulta de Terminales</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
                            style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Almacen</th>
                                    <th>T. Acción</th>
                                    <th>Zona</th>
                                    <th>Cantidad</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select * from termi";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $terminales[$row['tercod']] = $row;
                                    echo "<tr>";
                                    echo "<td>" . utf8_encode($row['tercod']) . "</td>";
                                    echo "<td>" . utf8_encode($row['terdes']) . "</td>";
                                    echo "<td>" . utf8_encode($row['almrefer']) . "</td>";
                                    echo "<td>" . utf8_encode($row['tipac']) . "</td>";
                                    echo "<td>" . utf8_encode($row['terzonpre']) . "</td>";
                                    echo "<td>" . utf8_encode($row['canped']) . "</td>";
                                    echo "<td>" . '<a title="Cambiar estado" onclick="updDat(' . "'" . $row['tercod'] . "'" . ",'" . $row['terdes'] . "'" . ",'" . $row['almrefer'] . "'" . ",'" . $row['tipac'] . "'" . ",'" . $row['terzonpre'] . "'" . ",'" . $row['canped'] . "'" . ')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>' . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
            <!-- Modal -->
            <div class="modal fade bd-example-modal-md" id="editUsr" tabindex="-1" role="dialog"
                aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eForm">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Código Terminal:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updCod" name="updCod" readonly class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Cant.Preparación:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updCant" name="updDesc" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Descripción:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updDesc" name="updDesc" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Tipo de Acción:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updTip" class="form-control">
                                                <?php
                                                $sq = "select * from tipac";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['tipcod'] . '">' . $ax['tipcod'] . ' - ' . utf8_encode($ax['tipdes']) . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Zona:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updZona" class="form-control">
                                                <option value="">Seleccionar</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Almacen:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updAlm" class="form-control">
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
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <div class="input-group">
                                                <button type="button" onclick="saveUpd()"
                                                    class="form-control btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div style="clear:both;"></div>
            <!-- Modal -->
            <div class="modal fade bd-example-modal-md" id="addUsr" tabindex="-1" role="dialog"
                aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eForm">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Código Terminal:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addCod" name="addCod" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripción:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addDesc" name="addDesc" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-2">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Cant.Preparación:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addCant" name="addDesc" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Tipo de Acción:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addTip" class="form-control">
                                                <?php
                                                $sq = "select * from tipac";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['tipcod'] . '">' . $ax['tipcod'] . ' - ' . utf8_encode($ax['tipdes']) . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Zona:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addZona" class="form-control">
                                                <option value="">Seleccionar</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Almacen:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addAlm" class="form-control">
                                                <?php
                                                $sq = "select * from alma where cencod = 'LCC1'";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . utf8_encode($ax['almdes']) . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <div class="input-group">
                                                <button type="button" onclick="saveAdd()"
                                                    class="form-control btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php
    include 'sidebar.php';
    include 'js_in.php';
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#segur6').addClass('active');
            $('#c6').click();
            //                setTimeout(function(){ window.location = 'general.php'; }, 180000);
            table = $('#tbl_list').DataTable({
                "processing": true,
                "serverSide": false,
                "bFilter": true,
                dom: '<"top"B<lfrtip>><"clear">',
                buttons: [
                    {
                        text: 'Añadir',
                        action: function (e, dt, node, config) {
                            addUsr();
                        }
                    },
                    'excel'
                ],
                "bInfo": true,
                "bLengthChange": true,
                "destroy": true,
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
                //                300000
            });
        });

    </script>
    <script type="text/javascript">
        function addUsr() {
            $("#addUsr .modal-title").empty().append('Añadir ');
            $('#addUsr').modal('show');
        }
        function updDat(addCod, addDesc, addAlm, addTip, addZona, addCant) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updCod").val(addCod);
            $("#updDesc").val(addDesc);
            $("#updTip").val(addTip);
            $("#updZona").val(addZona);
            $("#updAlm").val(addAlm);
            //                $("#updAlm").val(addAlm).change();
            $("#updCant").val(addCant);
            $('#editUsr').modal('show');
        }
        function saveAdd() {
            var Cod = $("#addCod").val();
            var Desc = $("#addDesc").val();
            var Tip = $("#addTip").val();
            var Zona = $("#addZona").val();
            var Alm = $("#addAlm").val();
            var cant = $("#addCant").val();

            if (Desc != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveQuery.php',
                    data: {
                        action: 'add',
                        Cod: Cod,
                        Desc: Desc,
                        Tip: Tip,
                        Zona: Zona,
                        Alm: Alm,
                        cant: cant,
                        table: 'termi',
                        fields: 'tercod,terdes,tipac,terzonpre,almrefer,canped'
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 's_terminales.php';
                        }
                    }
                });
            } else {
                alert('Favor ingresar una descripción');
            }
        }
        function saveUpd() {
            var Cod = $("#updCod").val();
            var Desc = $("#updDesc").val();
            var Tip = $("#updTip").val();
            var Zona = $("#updZona").val();
            var Alm = $("#updAlm").val();
            var cant = $("#updCant").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveQuery.php',
                data: {
                    action: 'upd',
                    Cod: Cod,
                    Desc: Desc,
                    Tip: Tip,
                    Zona: Zona,
                    Alm: Alm,
                    cant: cant,
                    table: 'termi',
                    fields: 'tercod,terdes,tipac,terzonpre,almrefer,canped'
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 's_terminales.php';
                    }
                }
            });
            //                }else{
            //                    alert('Favor ingresar una descripción');
            //                }
        }
    </script>
</body>

</html>