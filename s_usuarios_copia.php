<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("seguridad")) {
  echo "No tiene acceso";
  exit;
}

function gtROL($r)
{
  //    echo $r;
  switch ($r) {
    case null:
      $desc = ' ';
      break;
    case 0:
      $desc = '00 - SUPERVISOR';
      break;
    case 1:
      $desc = '01 - OPERARIO';
      break;
    default:
      $desc = ' ';
      break;
  }

  return $desc;
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
              . '<th colspan="2">Usuarios</th>'
              . '</tr>'
              . '</thead>'
              . '</table>';

            ?>
            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
              style="font-size: 12px !important;">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Cliente</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th>Acci&oacute;n</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sq = "select * from usuarios";
                //                                            echo $sq;
                $rs = $db->query($sq);
                while ($row = $rs->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . utf8_encode($row['pr_user']) . "</td>";
                  echo "<td>" . $row['pr_nombre'] . "</td>";
                  echo "<td>" . $row['pr_apellido'] . "</td>";
                  echo "<td>" . $row['clirefer'] . "</td>";
                  echo "<td>" . $row['pr_email'] . "</td>";
                  echo "<td>" . $row['rolrefer'] . ' - ' . $row['roldescri'] . "</td>";
                  echo "<td>" . '<a title="Modificar" onclick="updUSR(' . "'" . $row['pr_user'] . "'" . ",'" . $row['pr_nombre'] . "'" . ",'" . $row['pr_apellido'] . "'" . ",'" . $row['clirefer'] . "'" . ",'" . $row['pr_email'] . "'" . ",'" . $row['pr_rol'] . "'" . "" . ')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>' . "</td>";
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
      <div class="modal fade bd-example-modal-lg" id="editUsr" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">

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
                    <label>Direccion de Correo:</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email" value="" />
                    <input type="hidden" id="uuser" name="uuser" class="form-control" value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <label>Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="nombre" value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <label>Apellido:</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="apellido"
                      value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <label>Rol:</label>
                    <select class="form-control" id="rol" name="rol">

                    </select>
                  </div>
                  <div class="input-group">
                    <label>Clientes:</label>
                    <select class="form-control" id="cli" name="cli">
                      <?php
                      $sq = "select clirefer,clinom from clientes order by clirefer asc";
                      $rs = $db->query($sq);
                      echo '<option value="">Cliente</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['clirefer'] . '">' . $ax['clirefer'] . ' - ' . utf8_encode($ax['clinom']) . '</option>';
                        //                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                      }
                      ?>
                    </select>
                  </div>
                  <br />
                  <div class="input-group">
                    <button type="button" onclick="saveUpd()" class="form-control btn btn-primary">Guardar</button>
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
                <div class="col-lg-6 col-sm-6">
                  <div class="input-group">
                    <input type="text" id="add_uuser" name="add_uuser" class="form-control" placeholder="Usuario"
                      value="" />
                  </div>
                  <br />

                  <div class="input-group">
                    <input type="text" id="add_email" name="add_email" class="form-control" placeholder="Email"
                      value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <input type="password" id="add_pass" name="add_pass" class="form-control" placeholder="Contraseña"
                      value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <select class="form-control" id="add_rol" name="add_rol">
                      <?php
                      $sq = "select * from roles";
                      $rs = $db->query($sq);
                      echo '<option value="">Rol</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['rolcod'] . '">' . $ax['rolrefer'] . ' - ' . utf8_encode($ax['roldescri']) . '</option>';
                        //                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                      }
                      ?>
                    </select>
                  </div>
                  <div class="input-group">
                    <select class="form-control" id="add_cli" name="add_cli">
                      <?php
                      $sq = "select clirefer,clinom from clientes";
                      $rs = $db->query($sq);
                      echo '<option value="">Cliente</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['clirefer'] . '">' . $ax['clirefer'] . ' - ' . utf8_encode($ax['clinom']) . '</option>';
                        //                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                      }
                      ?>
                    </select>
                  </div>
                  <br />
                </div>
                <div class="col-lg-6 col-sm-6">
                  <div class="input-group">
                    <input type="text" id="add_nombre" name="add_nombre" class="form-control" placeholder="nombre"
                      value="" />
                  </div>
                  <br />
                  <div class="input-group">
                    <input type="text" id="add_apellido" name="add_apellido" class="form-control" placeholder="apellido"
                      value="" />
                  </div>

                  <br />
                  <div class="input-group">
                    <input type="password" id="add_pass2" name="add_pass2" class="form-control"
                      placeholder="Repetir Contraseña" value="" />
                  </div>
                </div>
                <div style="clear:both;"></div>
                <div class="col-lg-6 col-sm-6">
                  <div class="input-group">
                    <button type="button" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
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
    $(document).on('change', '#add_rol', function () {
      if ($('#add_rol option:selected').val() == 1) {
        $('#add_rel').prop("disabled", false)
      } else {
        $('[name=add_rel]').val('');
        $('#add_rel').prop("disabled", true);
      }
    });
    $(document).on('change', '#rol', function () {
      if ($('#rol option:selected').val() == 1) {
        $('#rel').prop("disabled", false)
      } else {
        $('[name=rel]').val('');
        $('#rel').prop("disabled", true);
      }
    });
    $(document).ready(function () {
      $('#segur3').addClass('active');
      $('#c3').click();
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
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todo"]],
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
      $("#addUsr .modal-title").empty().append('Añadir usuario ');
      $('#addUsr').modal('show');
    }
    function updUSR(usr, nombre, apellido, cli, email, rol) {
      $(".modal-title").empty().append('Modificar usuario <br/><b>' + usr + '</b>');
      $("#uuser").attr('value', usr);
      $("#nombre").attr('value', nombre);
      $("#apellido").attr('value', apellido);
      $("#cli").attr('value', cli);
      $("#email").attr('value', email);
      //                $("#rol").attr('value',rol);
      $("#rol").val(rol);
      $('#editUsr').modal('show');
    }
    function saveAdd() {
      var user = $("#add_uuser").val();
      var nombre = $("#add_nombre").val();
      var apellido = $("#add_apellido").val();
      var cli = $("#add_cli").val();
      var email = $("#add_email").val();
      var pass = $("#add_pass").val();
      var pass2 = $("#add_pass2").val();
      var rol = $("#add_rol").val();
      if (pass == pass2) {
        $.ajax({
          type: 'POST',
          url: 'sendUser.php',
          data: {
            action: 'add_user',
            user: user,
            email: email,
            nombre: nombre,
            apellido: apellido,
            cli: cli,
            pass: pass,
            rol: rol
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 's_usuarios.php';
            }
          }
        });
      } else {
        alert('Las contraseñas no coinciden');
      }
    }
    function saveUpd() {
      var user = $("#uuser").val();
      var nombre = $("#nombre").val();
      var apellido = $("#apellido").val();
      var cli = $("#cli").val();
      var email = $("#email").val();
      var rol = $("#rol").val();
      var flg = 0
      if (flg == 0) {
        $.ajax({
          type: 'POST',
          url: 'sendUser.php',
          data: {
            action: 'edit_usr',
            user: user,
            email: email,
            nombre: nombre,
            apellido: apellido,
            cli: cli,
            rol: rol
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 's_usuarios.php';
            }
          }
        });
      }
    }
  </script>
</body>

</html>