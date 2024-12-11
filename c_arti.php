<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();

verificarUsuarioLogueado();
//verificarPermisoLectura("externo", "articulo");
if (!tieneAccesoAModulo("externo")) {
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
                            . '<th colspan="2">Creacion de Articulos</th>'
                            . '</tr>'
                            . '</thead>'
                            . '</table>';

                        ?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
                            style="font-size: 16px !important;">
                            <thead>
                                <tr>
                                    <th>Articulo</th>
                                    <th>Descripcion</th>
                                    <th>UM</th>
                                    <th>P.Serie</th>
                                    <th>G.A.</th>
                                    <th>Cliente</th>
                                    <th>P.Lote</th>
                                    <th>Costo</th>
                                    <th>Almacen</th>
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px !important;">
                                <?php
                                $id_usuario = $_SESSION['user_id'];
                                $sql = "SELECT * FROM usuarios WHERE pr_id={$id_usuario}";
                                $usuario = $db->query($sql)->fetch_assoc();
                                $rol = $usuario['pr_rol'];
                                $sq = "select * from arti";
                                if ((int) $rol != 5) {
                                    $sq = "SELECT arti.* 
                                    from arti 
                                    inner join usuarios ON usuarios.clirefer = arti.clirefer 
                                    WHERE usuarios.pr_id = {$id_usuario}";
                                }

                                $rs = $db->query($sq);
                                while ($row = $rs->fetch_assoc()) {
                                    $terminales[$row['artlote']] = $row;
                                    echo "<tr>";
                                    echo "<td>" . utf8_encode($row['artrefer']) . "</td>";
                                    echo "<td>" . utf8_encode($row['artdesc']) . "</td>";
                                    echo "<td>" . utf8_encode($row['unimed']) . "</td>";
                                    echo "<td>" . utf8_encode($row['artser']) . "</td>";
                                    echo "<td>" . utf8_encode($row['artgrup']) . "</td>";
                                    echo "<td>" . utf8_encode($row['clirefer']) . "</td>";
                                    echo "<td>" . utf8_encode($row['artlotemar']) . "</td>";
                                    echo "<td>" . utf8_encode($row['costo']) . "</td>";
                                    echo "<td>" . utf8_encode($row['almcod']) . "</td>";
                                    if (tienePermiso("externo", "crear_articulo", "editar")) {
                                        echo "<td>" . '<a title="Modificar Material  " onclick="updDat(' . "'" . $row['artrefer'] . "'" . ",'" . $row['artdesc'] . "'" . ",'" . $row['unimed'] . "'" . ",'" . $row['artser'] . "'" . ",'" . $row['artgrup'] . "'" . ",'" . $row['clirefer'] . "'" . ",'" . $row['artlotemar'] . "'" . ",'" . $row['costo'] . "'" . ",'" . $row['almcod'] . "'" . ')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>' . "</td>";
                                    }
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
                                            <label class="label" style="color:#000;">Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updMat" name="updMat" readonly class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updDes" name="updDes" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div><br /><br />
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Uni.Medida:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updUm" class="form-control">
                                                <?php
                                                $sq = "select * from presen";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['preseref'] . '">' . $ax['preseref'] . ' - ' . utf8_encode($ax['presedes']) . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">P.Serie:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updPs" class="form-control">

                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Gru.Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updGa" class="form-control">

                                                <option value="VA">VA-Varios</option>
                                                <option value="LI">LI-Liquidos</option>
                                                <option value="RE">RE-Repuestos</option>


                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-sm-10">
                                        <div class="">
                                            <label class="label" style="color:#000;">Clientes</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="updCl" name="updCl" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">P.Serie:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="updPl" class="form-control">

                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="">
                                            <label class="label" style="color:#000;">Costo</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="int" id="updCos" name="updCos" class="form-control"
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
                                            <label class="label" style="color:#000;">Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addMat" name="addMat" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Descripcion:</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addDes" name="addDes" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Uni.Medida:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addUm" class="form-control">
                                                <?php
                                                $sq = "select * from presen";
                                                $rs = $db->query($sq);
                                                echo '<option value="">Seleccionar</option>';
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['preseref'] . '">' . $ax['preseref'] . ' - ' . utf8_encode($ax['presedes']) . '</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">P.Serie:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addPs" class="form-control">
                                                <option value="">PERFIL SERIE-</option>
                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Gru.Articulo:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addGa" class="form-control">
                                                <option value="VA">VA</option>
                                                <option value="VA">VA-Varios</option>
                                                <option value="LI">LI-Liquidos</option>
                                                <option value="RE">RE-Repuestos</option>


                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Clientes</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="text" id="addCl" name="addCl" class="form-control"
                                                placeholder="" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">P.Lote:</label>
                                            <div style="clear:both;"></div><br />
                                            <select id="addPl" class="form-control">
                                                <option value="">PERFIL LOTE</option>
                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="input-group">
                                            <label class="label" style="color:#000;">Costo</label>
                                            <div style="clear:both;"></div><br />
                                            <input type="int" id="addCos" name="addCos" class="form-control"
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
        function updDat(addMat, addDes, addUm, addPs, addGa, addCl, addPl, addCos, addAlm) {
            $("#editUsr .modal-title").empty().append('Editar ');
            $("#updMat").val(addMat);
            $("#updDes").val(addDes);
            $("#updUm").val(addUm);
            $("#updPs").val(addPs);
            $("#updGa").val(addGa);
            $("#updCl").val(addCl);
            $("#updPl").val(addPl);
            $("#updCos").val(addCos);
            $("#updAlm").val(addAlm);
            //                $("#updAlm").val(addAlm).change();

            $('#editUsr').modal('show');
        }
        function saveAdd() {
            var Art = $("#addMat").val();
            var Des = $("#addDes").val();
            var Umd = $("#addUm").val();
            var Pse = $("#addPs").val();
            var Gar = $("#addGa").val();
            var Cli = $("#addCl").val();
            var Plo = $("#addPl").val();
            var Cos = $("#addCos").val();
            var Alm = $("#addAlm").val();


            if (Art != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/saveArti.php',
                    data: {
                        action: 'add',
                        Art: Art,
                        Des: Des,
                        Umd: Umd,
                        Pse: Pse,
                        Gar: Gar,
                        Cli: Cli,
                        Plo: Plo,
                        Cos: Cos,
                        Alm: Alm,
                        table: 'arti',
                        fields: 'artrefer,artdesc,unimed,artser,artgrup,clirefer,artlotemar,costo,almcod'
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'c_arti.php';
                        }
                    }
                });
            } else {
                alert('Favor ingresar una descripción');
            }
        }
        function saveUpd() {
            var Art = $("#updMat").val();
            var Des = $("#updDes").val();
            var Umd = $("#updUm").val();
            var Pse = $("#updPs").val();
            var Gar = $("#updGa").val();
            var Cli = $("#updCl").val();
            var Plo = $("#updPl").val();
            var Cos = $("#updCos").val();
            var Alm = $("#updAlm").val();

            //                if(desc != ''){
            $.ajax({
                type: 'POST',
                url: 'requests/saveArti.php',
                data: {
                    action: 'upd',
                    Art: Art,
                    Des: Des,
                    Umd: Umd,
                    Pse: Pse,
                    Gar: Gar,
                    Cli: Cli,
                    Plo: Plo,
                    Cos: Cos,
                    Alm: Alm,
                    table: 'arti',
                    fields: 'artrefer,artdesc,unimed,artser,artgrup,clirefer,artlotemar,costo,almcod'
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.err == 0) {
                        window.location = 'c_arti.php';
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
<?php $db->close(); ?>