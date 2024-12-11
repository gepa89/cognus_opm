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
                            . '<th colspan="2">Creacion de Kits</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Descripcion</th>
                                    <th>Componente</th>
                                    <th>Des.Componente</th>
                                    <th>Almacen</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select arti.artrefer,artdesc,artrefkit,deskit,artkit.cod_alma, artkit.poskit 
                                        from arti
                                        INNER JOIN artkit on arti.artrefer=artkit.artrefer ORDER BY artrefer ASC
                                        ";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $terminales[$row['artrefer']] = $row;
                                    $artrefer = $row['artrefer'];
                                    $poskit = $row['poskit'];
                                    $artdesc = $row['artdesc'];
                                    $artrefkit = $row['artrefkit'];
                                    $deskit = $row['deskit'];
                                    $codalma = $row['cod_alma'];
                                    $update = "'$artrefer','$artdesc','$artrefkit','$deskit','$codalma'";
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo utf8_encode($row['artrefer']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['artdesc']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['artrefkit']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['deskit']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['cod_alma']) ?>
                                        </td>
                                        <td>
                                            <a title="Modificar Material" onclick="updDat(<?php echo $update ?>)">
                                                <span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>
                                            <a title="Eliminar Material" onclick="eliminarKIT(<?php echo $poskit ?>)">
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
            <div class="modal fade bd-example-modal-md" id="editUsr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eForm">
                                <div class="col-lg-32 col-sm-32">
                                    <div class="col-lg-12 col-sm-8">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updArt" name="updArt" readonly class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updDea" name="updDea" readonly class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Componente:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updCom" name="updCom" class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Des.Componente:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updDec" name="updDec" readonly class="form-control" placeholder="" value="" />
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
                                                <button type="button" onclick="saveUpd()" class="form-control btn btn-primary">Guardar</button>
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
            <div class="modal fade bd-example-modal-ml" id="addUsr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-ml">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eForm">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addArt" name="addArt" class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addDea" name="addDea" readonly class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Componente:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addCom" name="addCom" class="form-control" placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Des.Componente</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addDec" name="addDec" readonly class="form-control" placeholder="" value="" />
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
                                                <button type="button" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
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
        $(document).ready(function() {
            $('#maest9').addClass('active');
            $('#c4').click();
            //                setTimeout(function(){ window.location = 'general.php'; }, 180000);
            table = $('#tbl_list').DataTable({
                "processing": true,
                "serverSide": false,
                "bFilter": true,
                dom: '<"top"B<lfrtip>><"clear">',
                buttons: [{
                        text: 'Añadir',
                        action: function(e, dt, node, config) {
                            addUsr();
                        }
                    },
                    'excel'
                ],
                "bInfo": true,
                "bLengthChange": true,
                "destroy": true,
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
                //                300000
            });
        });
    </script>
    <script type="text/javascript">
        function addUsr() {
            $("#addUsr .modal-title").empty().append('Añadir ');
            $('#addUsr').modal('show');
        }

        function updDat(addArt, addDea, addCom, addDec, addAlm) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updArt").val(addArt);
            $("#updDea").val(addDea);
            $("#updCom").val(addCom);
            $("#updDec").val(addDec);
            $("#updAlm").val(addAlm);
            //                $("#updAlm").val(addAlm).change();

            $('#editUsr').modal('show');
        }

        function saveAdd() {
            var Art = $("#addArt").val();
            var Des = $("#addDea").val();
            var Com = $("#addCom").val();
            var Dec = $("#addDec").val();
            var Alm = $("#addAlm").val();


            if (Art != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveKit.php',
                    data: {
                        action: 'add',
                        Art: Art,
                        Des: Des,
                        Com: Com,
                        Dec: Dec,
                        Alm: Alm,
                        table: 'artkit',
                        fields: 'artrefer,artrefkit,deskit,cod_alma'
                    },
                    success: function(data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'm_kit.php';
                        }
                    },
                    error: function(request) {
                        console.log(request.responseJSON);
                        alert(request.responseJSON.error);
                    }
                });
            } else {
                alert('Favor ingresar una descripción');
            }
        }

        function eliminarKIT(id) {
            let eliminar = confirm("Desea eliminar este registro?");
            if (!eliminar) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: '/wmsd/requests/eliminar_kit.php',
                data: {
                    id: id,
                },
                success: function(data) {
                    alert(data.mensaje);
                    window.location = 'm_kit.php';
                },
                error: function(request) {
                    console.log(request.statusText);
                    alert(request.statusText);
                }
            });
        }

        function saveUpd() {
            var Art = $("#updArt").val();
            var Des = $("#updDea").val();
            var Com = $("#updCom").val();
            var Dec = $("#updDec").val();
            var Alm = $("#updAlm").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveKit.php',
                data: {
                    action: 'upd',
                    Art: Art,
                    Des: Des,
                    Com: Com,
                    Dec: Dec,
                    Alm: Alm,
                    table: 'artkit',
                    fields: 'artrefer,artrefkit,deskit,cod_alma'
                },
                success: function(data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'm_kit.php';
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