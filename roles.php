<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tienePermiso("seguridad", "roles", "leer")) {
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
                            . '<th colspan="2">Consulta Roles</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
                            style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Codigo Rol</th>
                                    <th>Referencia Rol</th>
                                    <th>Descripcion</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select * from roles";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $id = $row['id'];
                                    $codigo = $row['codigo'];
                                    $nombre = $row['nombre'];
                                    
                                    $update = "'$id','$codigo','$nombre'";
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo utf8_encode($row['id']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['codigo']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['nombre']) ?>
                                        </td>
                                        
                                        <td>
                                            <a title="Modificar Rol" onclick="updDat(<?php echo $update ?>)">
                                                <span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>
                                            <a title="Modificar Rol" onclick="eliminarLote('<?php echo $id ?>')">
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
                                            <label class="label" style="color:#000;">Codigo Rol:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updId" name="updId" readonly class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Referencia Rol:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updRol" name="updRol" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Descripcion Rol:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updDer" name="updDer" class="form-control"
                                                placeholder="" value="" />
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
                                            <label class="label" style="color:#000;">Referencia del Rol:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addRol" name="addRol" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addDer" name="addDer" class="form-control"
                                                placeholder="" value="" />
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
            $('#segur11').addClass('active');
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
        function updDat(addId, addRol, addDer) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updId").val(addId);
            $("#updRol").val(addRol);
            $("#updDer").val(addDer);
           
            //                $("#updAlm").val(addAlm).change();

            $('#editUsr').modal('show');
        }
        function saveAdd() {
            var Id = $("#addId").val();
            var Rol = $("#addRol").val();
            var Der = $("#addDer").val();
            


            if (Id != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveRoles.php',
                    data: {
                        action: 'add',
                        Id: Id,
                        Rol: Rol,
                        Der: Der,        
                        table: 'roles',
                        fields: 'codigo,nombre'
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'roles.php';
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
            var Id = $("#updId").val();
            var Rol = $("#updRol").val();
            var Der = $("#updDer").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveRoles.php',
                data: {
                    action: 'upd',
                    Id : Id ,
                    Rol: Rol,
                    Der: Der,
                    table: 'roles',
                    fields: 'id,codigo,nombre'
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'roles.php';
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