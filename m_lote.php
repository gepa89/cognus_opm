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
                            . '<th colspan="2">Creacion de Lotes</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
                            style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Lote</th>
                                    <th>Material</th>
                                    <th>Almacen</th>
                                    <th>Fec.Caducidad</th>
                                    <th>Fec.Inspeccion</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select * from loteart";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $terminales[$row['artlote']] = $row;
                                    $lote = $row['artlote'];
                                    $artrefer = $row['artrefer'];
                                    $codalma = $row['cod_alma'];
                                    $fecaduc = $row['fecaduc'];
                                    $fecinspe = $row['fecinspe'];
                                    $update = "'$lote','$artrefer','$fecaduc','$fecinspe','$codalma'";
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo utf8_encode($row['artlote']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['artrefer']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['cod_alma']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['fecaduc']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['fecinspe']) ?>
                                        </td>
                                        <td>
                                            <a title="Modificar Material" onclick="updDat(<?php echo $update ?>)">
                                                <span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>
                                            <a title="Modificar Material" onclick="eliminarLote('<?php echo $lote ?>')">
                                                <span style="font-size:14px" class="glyphicon glyphicon-trash"></span></a>
                                        </td>
                                    </tr>
                                <?php } ?>

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
                                            <label class="label" style="color:#000;">Lote:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updLot" name="updLot" readonly class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Material:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updMat" name="updMat" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Fec.Caducidad:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="date" id="updFca" name="updFca" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Fec.Inspeccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="date" id="updFin" name="updFin" class="form-control"
                                                placeholder="" value="" />
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
                                            <label class="label" style="color:#000;">Lote:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addLot" name="addLot" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Material:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addMat" name="addMat" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Fec.Caducidad:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="date" id="addFca" name="addFca" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Fec.Inspeccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="date" id="addFin" name="addFin" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>

                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Almacen:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addAlm" class="form-control">
                                                <?php
                                                $sq = "select * from alma ";
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
            $('#maest7').addClass('active');
            $('#c4').click();
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
        function updDat(addLot, addMat, addFca, addFin, addAlm) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updLot").val(addLot);
            $("#updMat").val(addMat);
            $("#updFca").val(addFca);
            $("#updFin").val(addFin);
            $("#updAlm").val(addAlm);
            //                $("#updAlm").val(addAlm).change();

            $('#editUsr').modal('show');
        }
        function saveAdd() {
            var Lot = $("#addLot").val();
            var Art = $("#addMat").val();
            var Fca = $("#addFca").val();
            var Fin = $("#addFin").val();
            var Alm = $("#addAlm").val();


            if (Art != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveLote.php',
                    data: {
                        action: 'add',
                        Lot: Lot,
                        Art: Art,
                        Fca: Fca,
                        Fin: Fin,
                        Alm: Alm,
                        table: 'loteart',
                        fields: 'artlote,artrefer,fecaduc,fecinspe,cod_alma'
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'm_lote.php';
                        }
                    }, error: function (request) {
                        console.log(request.responseJSON);
                        alert(request.responseJSON.error);
                    }
                });
            } else {
                alert('Favor ingresar una descripción');
            }
        }
        function saveUpd() {
            var Lot = $("#updLot").val();
            var Art = $("#updMat").val();
            var Fca = $("#updFca").val();
            var Fin = $("#updFin").val();
            var Alm = $("#updAlm").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveLote.php',
                data: {
                    action: 'upd',
                    Lot: Lot,
                    Art: Art,
                    Fca: Fca,
                    Fin: Fin,
                    Alm: Alm,
                    table: 'loteart',
                    fields: 'artlote,artrefer,fecaduc,fecinspe,cod_alma'
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'm_lote.php';
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