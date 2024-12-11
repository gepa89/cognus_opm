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
                <h3 class="panel-title">Capacidad de Ubicaciones</h3>
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
                  <th>Dimension</th>
                  <th>Material</th>
                  <th>Descripcion</th>
                  <th>Capacidad</th>
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
                  <div class="col-lg4 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Dimension:</label>
                      <div style="clear:both;"></div><br />
                      <select id="updDim" class="form-control">
                        <?php
                        $sq = "select * from dimen";
                        $rs = $db->query($sq);
                        echo '<option value="">Seleccionar</option>';
                        while ($ax = $rs->fetch_assoc()) {
                          echo '<option value="' . $ax['dimension'] . '">' . $ax['dimension'] . ' - ' . utf8_encode($ax['dimdesc']) . '</option>';
                        }
                        ?>

                      </select>
                    </div>
                  </div>
                  <div class="col-lg-20 col-sm-20">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Material:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="updMat" name="updMat" class="form-control" placeholder="" value="" />
                    </div>
                  </div>
                  <div class="col-lg-20 col-sm-20">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Descripcion:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="updDes" name="updDes" class="form-control" placeholder="" value="" />
                    </div>
                  </div>  
                  <div style="clear:both;"></div><br /><br />
                  <div class="col-lg-12 col-sm-12">
                    <div class="">
                      <label class="label" style="color:#000;">Capacidad:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="updCap" name="updCap" class="form-control" placeholder="" value="" />
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
                  <div class="col-lg4 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Dimension:</label>
                      <div style="clear:both;"></div><br />
                      <select id="addDim" class="form-control">
                        <?php
                        $sq = "select * from dimen";
                        $rs = $db->query($sq);
                        echo '<option value="">Seleccionar</option>';
                        while ($ax = $rs->fetch_assoc()) {
                          echo '<option value="' . $ax['dimension'] . '">' . $ax['dimension'] . ' - ' . utf8_encode($ax['dimdesc']) . '</option>';
                        }
                        ?>

                      </select>
                    </div>
                  </div>
                  <div class="col-lg-8 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Material:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="addMat" name="addMat" class="form-control" placeholder="" value="" />
                    </div>
                  </div>
                  <div class="col-lg-8 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Descripcion:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="addDes" name="addDes" class="form-control" readonly="readonly" placeholder="" value="" />
                    </div>
                  </div>  
                  <div class="col-lg-4 col-sm-4">
                    <div class="input-group">
                      <label class="label" style="color:#000;">Capacidad:</label>
                      <div style="clear:both;"></div><br />
                      <input type="text" id="addCap" name="addCap" class="form-control" placeholder="" value="" />
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
          { data: 'dimension' },
          { data: 'artrefer', orderable: false },
          { data: 'artdesc', orderable: false },
          { data: 'capacidad', orderable: false },
          { data: 'codalma' },
          {
            mRender: function (data, type, row) {
              let texto = `<a title="Modificar Material">
                                            <span style="font-size:14px" onclick="updDat('${row.dimension}','${row.artrefer}','${row.artdesc}','${row.capacidad}','${row.codalma}')" class="glyphicon glyphicon-pencil"></span></a>
                                         <a title="Modificar Material">
                                            <span style="font-size:14px" onclick="eliminarEan('${row.dimension}','${row.artrefer}','${row.artdesc}','${row.capacidad}','${row.codalma}')" class="glyphicon glyphicon-trash"></span></a>`;
              return texto;
            }
          }
        ],
      });
    }
    $(document).ready(function () {
      $('#capa1').addClass('active');
      $('#p1').click();
      let url = 'requests/obtenerCapaci.php';
      crearTabla(url);
      $("#form_filtros").submit(function (e) {
        e.preventDefault();
        let buscar = $("#buscar").val();
        let url = `requests/obtenerCapaci.php?buscar=${buscar}`;
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
    function updDat(addDim, addMat, addDes, addCap, addAlm) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updDim").val(addDim);
      $("#updMat").val(addMat);
      $("#updDes").val(addDes);
      $("#updCap").val(addCap);
      $("#updAlm").val(addAlm);
      //                $("#updAlm").val(addAlm).change();

      $('#editUsr').modal('show');
    }

    function eliminarEan(dim, codMaterial, capaci, cod_alma) {
      $.ajax({
        type: 'POST',
        url: 'requests/eliminarCapacidad.php',
        data: {
          dim: dim,
          cod_material: codMaterial,
          cod_alma: cod_alma,
          capaci: capaci
        }, success: function (data) {
          console.log(data);
          table.ajax.reload();
        }
      });
    }
    function saveAdd() {
      var Dim = $("#addDim").val();   
      var Art = $("#addMat").val();
      
      var Cap = $("#addCap").val();
      var Alm = $("#addAlm").val();


      if (Art != '') {
        $.ajax({
          type: 'POST',
          url: 'requests/saveCapaUbi.php',
          data: {
            action: 'add',
            Dim: Dim,
            Art: Art,
           
            Cap: Cap,
            Alm: Alm,
            table: 'capaubi',
            fields: 'dimension, artrefer, capacidad, codalma'
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 'capaci_ubi.php';
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
      var Dim = $("#updDim").val();
      var Art = $("#updMat").val();
      var Des = $("#updDes").val();
      var Cap = $("#updCap").val();
      var Alm = $("#updAlm").val();

      //                if(desc != ''){
      $.ajax({
        type: 'POST',
        url: 'requests/saveCapaUbi.php',
        data: {
          action: 'upd',
          Dim: Dim,
          Art: Art,
          Des: Des,
          Cap: Cap,
          Alm: Alm,
          table: 'capaubi',
          fields: 'dimension,artrefer,capacidad,codalma'
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          if (dt.err == 0) {
            window.location = 'capaci_ubi.php';
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