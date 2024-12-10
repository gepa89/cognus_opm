<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tienePermiso("seguridad", "secciones", "leer")) {
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
                            . '<th colspan="2">Consulta Programas</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
                            style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Codigo Seccion</th>
                                    <th>Seccion</th>
                                    <th>Sub Seccion</th>
                                    <th>Descripcion de la Seccion</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $sq = "select * from secciones";
                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $id = $row['id'] ;
                                    $seccion = $row['seccion'];
                                    $subseccion = $row['subseccion'];
                                    $secciondes = $row['secciondes'];
                                    
                                    $update = "'$id','$seccion','$subseccion','$secciondes'";
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo utf8_encode($row['id']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['seccion']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['subseccion']) ?>
                                        </td>
                                        <td>
                                            <?php echo utf8_encode($row['secciondes']) ?>
                                        </td>
                                        
                                        <td>
                                            <a title="Modificar Programa" onclick="updDat(<?php echo $update ?>)">
                                                <span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>
                                            <a title="Modificar Programa" onclick="eliminarLote('<?php echo $id ?>')">
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
                                            <label class="label" style="color:#000;">Id Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text"  disabled="true"id="updId" name="updId" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Seccion del Programa:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updSec" name="updSec" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Sub Seccion del Programa:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updSus" name="updSus" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Descripcion de la Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updSed" name="updSed" class="form-control"
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
                                            <label class="label" style="color:#000;">Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addSec" name="addSec" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Sub Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addSus" name="addSus" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion de la Seccion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addSed" name="addSed" class="form-control"
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
            $('#segur10').addClass('active');
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
        function updDat(addId, addSec, addSus, addSed) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updId").val(addId);
            $("#updSec").val(addSec);
            $("#updSus").val(addSus);
            $("#updSed").val(addSed);
           
            //                $("#updAlm").val(addAlm).change();

            $('#editUsr').modal('show');
        }
        function saveAdd() {
            var Id = $("#addId").val();
            var Sec = $("#addSec").val();
            var Sus = $("#addSus").val();
            var Sed = $("#addSed").val();
            


            if (Id != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/savePrograma.php',
                    data: {
                        action: 'add',
                        Id: Id,
                        Sec: Sec,
                        Sus: Sus, 
                        Sed: Sed,
                        table: 'secciones',
                        fields: 'seccion,subseccion,secciondes'
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'program.php';
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
            var Sec = $("#updSec").val();
            var Sus = $("#updSus").val();
            var Sed = $("#updSed").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/savePrograma.php',
                data: {
                    action: 'upd',
                    Id: Id,
                    Sec: Sec,
                    Sus: Sus,
                    Sed: Sed,
                    table: 'secciones',
                    fields: 'id,seccion,subseccion, secciondes'
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'program.php';
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