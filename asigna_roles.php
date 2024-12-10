<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tienePermiso("seguridad", "roles_sec", "leer")) {
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
                            . '<th colspan="2">Asignar Programas a Roles</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th>Seccion</th>
                                    <th>Subseccion</th>
                                    <th>Permiso</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select roles_permisos.id, roles.codigo as rol,roles_permisos.accion,
                                        secciones.seccion,secciones.subseccion, roles_permisos.id_rol, roles_permisos.id_seccion
                                        from roles_permisos
                                        inner JOIN roles on roles_permisos.id_rol=roles.id
                                        INNER JOIN secciones on roles_permisos.id_seccion=secciones.id";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $id_rol = $row['id_rol'];
                                    $id_seccion = $row['id_seccion'];
                                    $id = $row['id'];
                                    $accion = $row['accion'];

                                    $update = "'$id','$id_rol','$id_seccion','$accion'";
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo utf8_encode($row['rol']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['seccion']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['subseccion']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['accion']) ?>
                                        </td>

                                        <td>
                                            <a title="Asignar Rol" onclick="updDat(<?php echo $update ?>)">
                                                <span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>
                                            <a title="Asignar Rol" onclick="eliminarLote('<?php echo $id ?>')">
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

            <div style="clear:both;"></div>
            <!-- Modal -->
            <div class="modal fade bd-example-modal-md" id="addUsr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eForm">
                                <input type="hidden" id="id_permiso">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Roles:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addRol" class="form-control">
                                                <?php
                                                $sq = "select * from roles ";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['id'] . '">' . $ax['codigo'] . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addSec" class="form-control">
                                                <?php
                                                $sq = "select id, seccion, subseccion from secciones ";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_object()) {
                                                    echo "<option value=\"$ax->id\">$ax->seccion-$ax->subseccion</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Permiso:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addPer" class="form-control">
                                                <option value="" selected disabled>--Seleccione--</option>
                                                <option value="leer">Leer</option>
                                                <option value="escribir">Escribir</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <div class="input-group">
                                                <button type="button" id="enviar_formulario" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
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
            $('#segur12').addClass('active');
            $('#c6').click();
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
            $("#addRol").removeAttr('disabled');
            $("#enviar_formulario").data("action", "add")
            $("#addRol").val("");
            $("#addSec").val("");
            $("#addPer").val("")
            $("#id_permiso").val("");
            $('#addUsr').modal('show');
        }

        function updDat(id, addRol, addSec, addPer) {
            $('#addRol').prop('disabled', true);
            $("#addUsr .modal-title").empty().append('Editar ');
            $('#addUsr').modal('show');
            $("#addRol").val(addRol);
            $("#addSec").val(addSec);
            $("#addPer").val(addPer);
            $("#id_permiso").val(id);
            $("#enviar_formulario").data("action", "upd")

        }

        function saveAdd() {
            let data = $("#enviar_formulario").data();
            var Rol = $("#addRol").val();
            var Sec = $("#addSec").val();
            var Per = $("#addPer").val();
            if (Rol != '' && Sec != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveRoles_Permisos.php',
                    data: {
                        action: data.action,
                        id_rol: Rol,
                        id_seccion: Sec,
                        accion: Per,
                        id_permiso: $("#id_permiso").val()
                    },
                    success: function(data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'asigna_roles.php';
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

        function saveUpd() {
            var Rol = $("#updRol").val();
            var Sec = $("#updSec").val();
            var Per = $("#updPer").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveRoles_Permisos.php',
                data: {
                    action: 'upd',
                    Rol: Rol,
                    Sec: Sec,
                    Sse: Sse,
                    Per: Per,
                    table: 'roles_permisos',
                    fields: 'id_rol, id_seccion,id_subseccion, accion'
                },
                success: function(data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'asigna_roles.php';
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