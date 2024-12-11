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
                <h3 class="panel-title">Administrar Estibadores</h3>
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
              . '<th colspan="2">Agregar Estibador</th>'
              . '</tr>'
              . '</thead>'
              . '</table>';

            ?>
            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_listb"
              style="font-size: 16px !important;">
              <thead>
                <tr>
                  <th>Estibador</th>
                  <th>Descripcion</th>
                  <th>Fecha</th>
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
              <form method="post" id="eForm">
                <div class="row">
                   <div class="col-sm-4">
                    <div class="form-group">
                      <label for="buscar">Estibador</label>
                      <input type="text" id="updEst" name="updEst" class="form-control"
                             placeholder="" value="" disabled="true" />
                    </div>
                  </div>
                     <div class="col-sm-7">
                        <div class="form-group">
                          <label for="buscar">Descripcion</label>
                          <input type="text" id="updDes" name="updDes" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                 
                  <div style="clear:both;"></div><br /><br />

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
      <div class="modal fade bd-example-modal-lg" id="addUsr" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
              <form method="post" id="eForm">
               <div class="row">
                   <div class="col-sm-4">
                    <div class="form-group">
                      <label for="buscar">Estibador</label>
                      <input type="text" id="addEst" name="addEst" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>
                     <div class="col-sm-7">
                        <div class="form-group">
                          <label for="buscar">Descripcion</label>
                          <input type="text" id="addDes" name="addDes" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                 
                  <div style="clear:both;"></div><br /><br />

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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/es.js"></script>
  <script type="text/javascript">
    var table = null;
    function crearTabla(url) {
      table = $('#tbl_listb').DataTable({
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
          { data: 'codestib' },
          { data: 'nombre', orderable: false },
          { data: 'fecre', orderable: false },
          
          {
            mRender: function (data, type, row) {
              let texto = `<a title="Modificar Material">
                                            <span style="font-size:14px" onclick="updDat('${row.codestib}','${row.nombre}','${row.fecre}')" class="glyphicon glyphicon-pencil"></span></a>
                                         <a title="Eliminar Bultos">
                                            <span style="font-size:14px" onclick="eliminarBultos('${row.tipbul}')" class="glyphicon glyphicon-trash"></span></a>`;
              return texto;
            }
          }
        ],
      });
    }
    $(document).ready(function () {
      $('#para44').addClass('active');
      $('#c7').click();
      let url = 'requests/obtenerEstibador.php';
      crearTabla(url);
      $("#form_filtros").submit(function (e) {
        e.preventDefault();
        let buscar = $("#buscar").val();
        let url = `requests/obtenerEstibador.php?buscar=${buscar}`;
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
    function updDat(addEst, addDes) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updEst").val(addEst);
      $("#updDes").val(addDes);
     
      
      //                $("#updAlm").val(addAlm).change();

      $('#editUsr').modal('show');
    }

    function eliminarBultos(bul) {
      $.ajax({
        type: 'POST',
        url: 'requests/eliminarBultos.php',
        data: {
          pue: pue
          
        }, success: function (data) {
          console.log(data);
          table.ajax.reload();
        }
      });
    }
    function saveAdd() {
      var Est = $("#addEst").val();
      var Des = $("#addDes").val();


      if (Est != '') {
        $.ajax({
          type: 'POST',
          url: 'requests/saveEstibador.php',
          data: {
            action: 'add',
            Est: Est,
            Des: Des,

            table: 'estibador',
            fields: 'codestib,nombre'
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 'crea_estibador.php';
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
      var Est = $("#updEst").val();
      var Des = $("#updDes").val();
     
  

      //                if(desc != ''){
      $.ajax({
        type: 'POST',
        url: 'requests/saveEstibador.php',
        data: {
          action: 'upd',
          Est: Est,
          Des: Des,
         
         
          table: 'estibador',
          fields: 'codestib,nombre'
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          if (dt.err == 0) {
            window.location = 'crea_estibador.php';
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