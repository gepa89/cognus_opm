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
          <form id="form_filtros" method="get">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h3 class="panel-title">Dias Permitidos para Mezclar el mismo Material</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Buscar:</label>
                      <input type="search" class="form-control" name="buscar" required id="buscar">
                    </div>
                  </div>
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">&nbsp;</label>
                      <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-12">
            <?php

            echo '<table class="table">'
              . '<thead>'
              . '<tr>'
              . '<th colspan="2">Agregar Capacidad</th>'
              . '</tr>'
              . '</thead>'
              . '</table>';

            ?>
            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list"
              style="font-size: 16px !important;">
              <thead>
                <tr>
                  <th>Material</th>
                  <th>Descripcion</th>
                  <th>Dias Mezcla</th>
                  <th>Almacen</th>
                  <th>Acci&oacute;n</th>
                </tr>
              </thead>
              <tbody></tbody>
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
                   <div class="col-lg-12 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Material:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="updMat" name="updMat" class="form-control" readonly="readonly" placeholder="" value="" />
                    </div>
                  </div>
                  <div class="col-lg-12 col-sm-12">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Descripcion:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="updDes" name="updDes" class="form-control" readonly="readonly" placeholder="" value="" />
                    </div>
                  </div>
                  <div style="clear:both;"></div><br /><br />
                  <div class="col-lg-4 col-sm-4">
                    <div class="">
                      <label class="label" style="color:#000;">Dias Mezcla:</label>
                      <div style="clear:both;"></div><br />
                      <input type="number" id="updDia" name="updDia" class="form-control" placeholder="" value="" />
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
                  <div class="col-lg-12 col-sm-12">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Material:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="addMat" name="addMat" class="form-control" placeholder="" value="" />
                    </div>
                  </div>
                  
                  <div class="col-lg-4 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Dias Mezcla:</label>
                      <div style="clear:both;"></div><br />
                      <input type="number" id="addDia" name="addDia" class="form-control" placeholder="" value="" />
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
    var table = null;
    function crearTabla(url) {
      table = $('#tbl_list').DataTable({
        bFilter: true,
        dom: '<"top"B<lfrtip>><"clear">',
        buttons: [
          {
            text: 'Añadir',
            action: function (e, dt, node, config) {
              addUsr();
            }
          }
        ],
        bInfo: true,
        bLengthChange: true,
        destroy: true,
        processing: true,
        serverSide: true,
        paging: true,
        ajax: url,
        searching: false,
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
        },
        columns: [
          { data: 'artrefer' },
          { data: 'artdesc', orderable: false },
          { data: 'candias', orderable: false },
          { data: 'codalma' },
          {
            mRender: function (data, type, row) {
              let texto = `<a title="Modificar Material">
                                            <span style="font-size:14px" onclick="updDat('${row.artrefer}','${row.artdesc}','${row.candias}','${row.codalma}')" class="glyphicon glyphicon-pencil"></span></a>
                                         <a title="Modificar Material">
                                            <span style="font-size:14px" onclick="eliminarEan('${row.artrefer}','${row.artdesc}','${row.candias}','${row.codalma}')" class="glyphicon glyphicon-trash"></span></a>`;
              return texto;
            }
          }
        ],
      });
    }
    $(document).ready(function () {
      $('#capa3').addClass('active');
      $('#p1').click();
      let url = 'requests/obtenerDiasMezcla.php';
      crearTabla(url);
      $("#form_filtros").submit(function (e) {
        e.preventDefault();
        let buscar = $("#buscar").val();
        let url = `requests/obtenerDiasMezcla.php?buscar=${buscar}`;
        if (table != null) {
          table.destroy();
          table = null;
        }
        crearTabla(url);
        return false;
      })

    });

    function addUsr() {
      $("#addUsr .modal-title").empty().append('Añadir ');
      $('#addUsr').modal('show');
    }
    function updDat(addMat, addDes, addDia, addAlm) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updMat").val(addMat);
      $("#updDes").val(addDes);
      $("#updDia").val(addDia);
      $("#updAlm").val(addAlm);
      //                $("#updAlm").val(addAlm).change();

      $('#editUsr').modal('show');
    }

    function eliminarEan(Art, codMaterial, Dia, cod_alma) {
      $.ajax({
        type: 'POST',
        url: 'requests/eliminarDiasmezcla.php',
        data: {
          Art: Art,
          cod_material: codMaterial,
          cod_alma: cod_alma,
          Dia: Dia
        }, success: function (data) {
          console.log(data);
          table.ajax.reload();
        }
      });
    }
    function saveAdd() {
      var Art = $("#addMat").val();   
      var Dia = $("#addDia").val();
      var Alm = $("#addAlm").val();


      if (Art != '') {
        $.ajax({
          type: 'POST',
          url: 'requests/saveDiasMezcla.php',
          data: {
            action: 'add',
            Art: Art,
            Dia: Dia,
            Alm: Alm,
            table: 'diasmezcla',
            fields: 'artrefer, candias, codalma'
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 'diasmezcla.php';
            }
          }, error: function (request) {
            alert(request.responseJSON.error);
          }
        });
      } else {
        alert('Favor ingresar una descripción');
      }
    }
    function saveUpd() {
      var Art = $("#updMat").val();
      var Des = $("#updDes").val();
      var Dia = $("#updDia").val();
      var Alm = $("#updAlm").val();

      //                if(desc != ''){
      $.ajax({
        type: 'POST',
        url: 'requests/saveDiasMezcla.php',
        data: {
          action: 'upd',
          Art: Art,
          Des: Des,
          Dia: Dia,
          Alm: Alm,
          table: 'diasmezcla',
          fields: 'artrefer,candias,codalma'
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          if (dt.err == 0) {
            window.location = 'diasmezcla.php';
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