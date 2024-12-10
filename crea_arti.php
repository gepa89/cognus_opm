<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
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
            <form id="form" method="get">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Creacion de Articulos</h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="buscar">Buscar:</label>
                        <input type="text" class="form-control" id="buscar" required>
                      </div>
                    </div>
                    <div class="col-md-3">
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
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-12">
            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
              <thead>
                <tr>
                  <th>Articulo</th>
                  <th>Descripcion</th>
                  <th>Ean</th>
                  <th>Tiene Kit</th>
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
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <div style="clear:both;"></div>
      <!-- Modal -->
      <div class="modal fade bd-example-modal-md" id="editUsr" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
              <form method="post" id="eForm">
                <div class="form-group">
                  <label for="articulo">Artículo:</label>
                  <input type="text" class="form-control reset-on-hide" id="material" name="material" maxlength="18" required>
                </div>
                <div class="form-group">
                  <label for="descripcion">Descripción:</label>
                  <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="ean">EAN:</label>
                  <input type="text" class="form-control reset-on-hide" id="ean" name="ean" required>
                </div>
                <div class="form-group">
                  <label for="unidad">Unidad de Medida:</label>
                  <select class="form-control reset-on-hide" id="unidadmedida" name="unidadmedida">
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
                <div class="form-group">
                  <label for="serie">Serie:</label>
                  <select class="form-control reset-on-hide" id="serie" name="serie">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="kit">Tiene Kit:</label>
                  <select class="form-control reset-on-hide" id="kit" name="kit">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                  </select>
                </div>  
                <div class="form-group">
                  <label for="serie">Lote:</label>
                  <select class="form-control reset-on-hide" id="lote" name="lote">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="grupo">Grupo de Artículos:</label>
                  <select class="form-control reset-on-hide" id="grupoarticulo" name="grupoarticulo">
                    <?php
                    $sq = "select * from grupoart";
                    $rs = $db->query($sq);
                    echo '<option value="">Seleccionar</option>';
                    while ($ax = $rs->fetch_assoc()) {
                      echo '<option value="' . $ax['artgrup'] . '">' . $ax['artgrup'] . ' - ' . utf8_encode($ax['grupdes']) . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="costo">Costo:</label>
                  <input type="int" id="costo" name="costo" class="form-control reset-on-hide" placeholder="" value="" />

                </div>
                <div class="form-group">
                  <label for="almacen">Almacén:</label>
                  <select class="form-control reset-on-hide" id="almacen" name="almacen">
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
                <div class="form-group">
                  <label for="cliente">Cliente:</label>
                  <select class="form-control reset-on-hide" id="cliente" class="form-control" value="cliente1" name="cliente">
                  </select>
                </div>
                <div class="form-group">
                  <button type="button" id="btn-guardar-articulo" onclick="saveUpd(this)" class="form-control btn btn-primary">Guardar</button>


                </div>
              </form>
              <div style="clear:both;"></div>
            </div>
          </div>

        </div>
      </div>
      <div style="clear:both;"></div>
      <!-- Modal -->
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
      table = $('#tbl_list').DataTable({
        bFilter: true,
        dom: '<"top"B<lfrtip>><"clear">',
        buttons: [{
          text: 'Añadir',
          action: function(e, dt, node, config) {
            addUsr();
          }
        }],
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
        columns: [{
            data: 'artrefer'
          },
          {
            data: 'artdesc'
          },
          {
            data: 'artean'
          },
          {
            data: 'artmarkit'
          },
          {
            data: 'unimed'
          },
          {
            data: 'artser'
          },
          {
            data: 'artgrup'
          },
          {
            data: 'clirefer'
          },
          {
            data: 'artlotemar'
          },
          {
            data: 'costo'
          },
          {
            data: 'almcod'
          },
          {
            mRender: function(data, type, row) {
              let desc = row.artdesc.trim();
              let texto = `<a title="Modificar Material ">
                                            <span style="font-size:14px" onclick="updDat('${row.artrefer}','${desc}','${row.artean}','${row.artmarkit}','${row.unimed}','${row.artser}','${row.artgrup}','${row.clirefer}','${row.artlotemar}','${row.costo}','${row.almcod}','${row.nombre_cliente}')" class="glyphicon glyphicon-pencil"></span></a>
                                        <a title="Modificar Material">
                                            </a>`;
              return texto;
            }
          }
        ],
      });
    }

    function setCliente(cliente) {
      $('#cliente').select2({
        ajax: {
          url: 'http://192.168.136.31/wmsd/api/obtenerClientes.php',
          dataType: "json",
          language: "es",
          delay: 250,
          data: function(params) {
            var query = {
              search: params.term
            }

            // Query parameters will be ?search=[term]&type=public
            return query;
          }
          // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        },
        placeholder: "Buscar Cliente",
        minimumInputLength: 3,
      }).data('select2').$selection.css('height', '32px');

      if (cliente === null || cliente.id_cliente == '') {
        $('#cliente').append(null).trigger('change');
        return;
      }
      let data = {
        id: cliente.id_cliente,
        text: cliente.nom_cliente
      };

      let newOption = new Option(data.text, data.id, true, true);
      $('#cliente').append(newOption).trigger('change');
    }
    $(document).ready(function() {
      $('#editUsr').on('show.bs.modal', function() {
        // Aquí puedes ejecutar el código que desees cuando se abre el modal
        let datoCliente = $('#editUsr').data('cliente') ? $('#editUsr').data('cliente') : null;
        let cliente = null;
        if (datoCliente !== null && datoCliente !== undefined) {
          cliente = JSON.parse(datoCliente);
        }
        window.setTimeout(function() {
          setCliente(cliente)
        }, 250);
        // Puedes agregar más acciones aquí

      });



      $("#form").submit(function(e) {
        e.preventDefault();
        let buscar = $("#buscar").val();
        let url = `requests/obtenerArti.php?buscar=${buscar}`;
        if (table != null) {
          table.destroy();
          table = null;
        }
        crearTabla(url);
        return false;
      })
      $('#ingre2').addClass('active');
      $('#c3').click();
      let url = 'requests/obtenerArti.php';
      crearTabla(url);

    });

    function addUsr() {
      $('#editUsr').data('cliente', null);
      $('.reset-on-hide').val("");
      $("#descripcion").text("");
      $("#material").prop('readonly', false);
      $("#editUsr .modal-title").empty().append('Añadir ');
      $("#btn-guardar-articulo").data('accion', 'add');
      $('#editUsr').modal('show');
    }

    function updDat(addMat, addDes, addEan,addKit, addUm, addPs, addGa, addCl, addPl, addCos, addAlm, nomCliente) {
      let data = {
        id: addCl,
        text: nomCliente
      };
      let newOption = new Option(data.text, data.id, false, false);
      $('#cliente').append(newOption).trigger('change');

      $('.reset-on-hide').val("");
      $("#descripcion").text("");
      $("#material").prop('readonly', true);
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#material").val(addMat);
      $("#descripcion").text(addDes);
      $("#ean").val(addEan);
      $("#kit").val(addKit);
      $("#unidadmedida").val(addUm);
      $("#serie").val(addPs);
      $("#grupoarticulo").val(addGa);
      $("#lote").val(addPl);
      $("#costo").val(addCos);
      $("#almacen").val(addAlm);
      //                $("#almacen").val(addAlm).change();
      $("#btn-guardar-articulo").data('accion', 'upd');
      $('#editUsr').data('cliente', JSON.stringify({
        'id_cliente': addCl,
        'nom_cliente': nomCliente
      }));
      $('#editUsr').modal('show');
    }

    function eliminarEan(ean, codMaterial, cod_alma) {
      $.ajax({
        type: 'POST',
        url: 'requests/eliminarArti.php',
        data: {
          ean: ean,
          cod_material: codMaterial,
          cod_alma: cod_alma
        },
        success: function(data) {
          table.ajax.reload();
        }
      });
    }

    function saveUpd(e) {
      let datos = $(e).data();
      let accion = datos.accion;
      var formData = {
        action: accion,
        Art: $("#material").val(),
        Des: $("#descripcion").val(),
        Ean: $("#ean").val(),
        Kit: $("#kit").val(),
        Umd: $("#unidadmedida").val(),
        Pse: $("#serie").val(),
        Gar: $("#grupoarticulo").val(),
        Cli: $("#cliente").val(),
        Plo: $("#lote").val(),
        Cos: $("#costo").val(),
        Alm: $("#almacen").val(),
        table: 'arti',
        fields: 'artrefer,artdesc,artean,artmarkit,unimed,artser,artgrup,clirefer,artlotemar,costo,almcod'
      };
      //                if(desc != ''){
      $.ajax({
        type: 'POST',
        url: 'requests/saveArti.php',
        data: formData,
        success: function(data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          if (dt.err == 0) {
            window.location = 'crea_arti.php';
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