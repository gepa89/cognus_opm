<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("recepcion")) {
  echo "No tiene acceso";
  exit;
}
$hoy = date('Y-m-d');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head_ds.php' ?>
<link rel="stylesheet" href="css/custom-datatable.css">

<body class="full_width">
  <style>
    #tblReg_filter {
      display: none;
    }

    #tblReg tbody tr td:first-of-type {
      width: 100px;
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
                <h3 class="panel-title">Consultar Pedidos de Compras</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Pedido:</label>
                      <input class="form-control" id="pedido" name="pedido" />
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Proveedor</label>
                      <input class="form-control" id="proveedor" name="proveedor" />
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="buscar">Almacen</label>
                      <select id="codalma" class="form-control">
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
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="buscar">Fecha Desde:</label>
                      <input class="form-control" id="fecha_desde" value="<?php echo $hoy; ?>" name="fecha_desde"
                        type="date">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="buscar">Fecha Hasta:</label>
                      <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta"
                        type="date">
                    </div>
                  </div>



                </div>
                <div class="row">

                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Situación:</label>
                      <select class="form-control" id="situacion" name="situacion[]" multiple="multiple">
                        <?php
                        $sql = "select * from situped WHERE siturefe in ('PD','AN','PR','CE','DC','PL')";
                        $rs = $db->query($sql);
                        while ($ax = $rs->fetch_assoc()) {
                          echo '<option value="' . $ax['siturefe'] . '">' . $ax['siturefe'] . ' - ' . $ax['situdes'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">&nbsp;</label>
                      <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="submit" id="sndBtn" class="form-control btn btn-primary">Buscar</button>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
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
                    <div class="col-sm-2">
                      <div class="form-group">
                        <label for="buscar">Order</label>
                        <input type="text" id="addPed" name="addPed" disabled="true" class="form-control" placeholder=""
                          value="" />
                      </div>
                    </div>
                    <div class="col-sm-8">
                      <div class="form-group">
                        <label for="buscar">Proveedor</label>
                        <input type="text" id="addPro" name="addPro" disabled="true" class="form-control" placeholder=""
                          value="" />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-8">
                      <div class="form-group">
                        <label for="buscar">Observation</label>

                        <textarea id="addObs" name="addObs" class="form-control" maxlength="250" rows="4"
                          cols="50"></textarea>

                      </div>
                    </div>

                    <div style="clear:both;"></div><br /><br />

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
        <div class="row">
          <div class="col-md-4"></div>
          <div class="col-md-4"></div>
          <div class="col-md-4" style="float: right;">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">Buscar:</span>
              <input type="search" class="form-control" id="search" placeholder="Buscar">
            </div>
          </div>
        </div>
        <div class="row">
          <table class="table table-hover table-striped table-bordered  table-condensed" style="width: 100% !important;"
            id="tblReg">
          </table>
        </div>
        <!-- Modal -->
        <div class="modal fade bd-example-modal-sm" id="assignRec" tabindex="-1" role="dialog"
          aria-labelledby="mySmallModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm">

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
                      <input type="hidden" id="recepcion" name="recepcion" class="form-control" value="" />
                      <input type="hidden" id="codalmacen" name="codalmacen">
                      <select class="form-control" id="terminal" name="terminal">
                        <?php
                        $sq = "select * from termi where tipac = 'RECE'";
                        $rs = $db->query($sq);
                        echo '<option value="">Seleccionar</option>';
                        while ($ax = $rs->fetch_assoc()) {
                          echo '<option value="' . $ax['tercod'] . '">' . $ax['tercod'] . ' - ' . utf8_encode($ax['terdes']) . '</option>';
                          //                                                                        echo "<script>loadCity('".$ax['id']."');</script>"
                        }
                        ?>
                      </select>
                    </div>
                    <br />
                    <div class="input-group">
                      <button type="button" onclick="saveAssign()" class="form-control btn btn-primary">Guardar</button>
                    </div>
                  </div>
                </form>
                <div style="clear:both;"></div>
              </div>
            </div>

          </div>
        </div>
        <div style="clear:both;"></div>
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-md" id="editUsr" tabindex="-1" role="dialog"
    aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Detalle de Usuario</h4>
        </div>
        <div class="modal-body">
          <!-- Tabla dentro del modal -->
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Order</th>
                <th>Date</th>
                <th>User</th>
                <th>Observation</th>
              </tr>
            </thead>
            <tbody id="cuerpo_tabla">

            </tbody>
          </table>
          <!-- Botón para cerrar o realizar una acción adicional -->
          <div class="form-group text-center">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="modal_sin_ubicacion" tabindex="-1" role="dialog"
    aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <table class="table table-responsive table-striped">
            <thead>
              <tr>
                <th>Pedido</th>
                <th>Posicion</th>
                <th>Articulo</th>
                <th>Descripcion</th>
              </tr>
            </thead>
            <tbody id="cuerpo_tabla">

            </tbody>

          </table>
        </div>
      </div>
    </div>

  </div>
  <div id="fileModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Adjuntar Documento</h2>

      <!-- Formulario para seleccionar el archivo -->
      <form id="uploadForm">
        <input type="file" name="documents" id="documents" multiple>
        <br><br>
        <button type="button" onclick="uploadDocuments()">Subir Archivos</button>
      </form>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="modalData" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="overflow-x:auto;">
        </div>

        <div class="modal-footer">
          <!--<button type="button" onclick="clsDetail()" id="btnClsPk" class="btn btn-default"><i class="icon-adt_trash"></i></button>-->
          <!--<button type="button" disabled="disabled" id="btnPk" onclick="btnVlnSv()" class="btn btn-primary">Guardar</button>-->
          <!--                                <button type="button" disabled="disabled" id="btnPk" onclick="btnVlnPk()" class="btn btn-primary">Picking</button>
                                                <button type="button" disabled="disabled" id="btnAnl" onclick="btnVlnAnl()" class="btn btn-warning">Anulación</button>-->
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" onclick="addUsr()">Add observation</button>
        </div>

      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="addDat" tabindex="-1" role="dialog"
    aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Contenedor</h4>
        </div>
        <div class="modal-body">
          <form method="post" id="eForm">
            <div id="spreadsheet"></div>
            <div class="row">

              <div class="col-sm-3">
                <div class="form-group">
                  <label for="proveedor">Nro.Pedido:</label>
                  <input id="pedgen" disabled name="pedgen" class="form-control">
                </div>
              </div>
              <div style="clear:both;"></div><br /><br />
              <div class="col-lg4 col-sm-4">
                <div class="input-group">
                  <div class="input-group">
                    <button type="button" onclick="saveConte()" class="form-control btn btn-primary">Guardar</button>
                  </div>
                </div>
              </div>

            </div>


          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-sm" id="modal-generico" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
        </div>
      </div>

    </div>
  </div>
  <div style="clear:both;"></div>
  <?php
  include 'modal_match.php';
  include 'sidebar.php';
  include 'js_in.php';
  ?>

  <script id="form-solicitar-contenedor" type="text/template">
    <form onsubmit="return submitFormGenerico(event);" id="form-generico" action="__url__">
    <input type="hidden" name="pedido" value="__pedido__" id="pedido" />
    <input type="hidden" name="almacen" value="__almacen__" id="almacen" />

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <p>Está seguro que desea Solicitar Datos de Contenedor?. La acción no es reversible.</p>
        </div>
        <div class="form-group">
          <label for="buscar"></label>
          <Button class="btn btn-primary btn-submit" type="submit">
            <img id="loading-button" hidden src="/opm/assets/img/loading.svg" width="20px" alt="">
            Guardar
          </Button>
        </div>
      </div>
    </div>
  </form>
  </script>
  <script id="form-solicitar-aprobacion" type="text/template">
    <form onsubmit="return submitFormGenerico(event);" id="form-generico" action="__url__">
    <input type="hidden" name="pedido" value="__pedido__" id="pedido" />
    <input type="hidden" name="almacen" value="__almacen__" id="almacen" />

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <p>Está seguro que desea Solicitar Aprobacion?. La acción no es reversible.</p>
        </div>
        <div class="form-group">
          <label for="buscar"></label>
          <Button class="btn btn-primary btn-submit" type="submit">
            <img id="loading-button" hidden src="/opm/assets/img/loading.svg" width="20px" alt="">
            Guardar
          </Button>
        </div>
      </div>
    </div>
  </form>
  </script>
  <script id="anular-pedido" type="text/template">
    <form onsubmit="return submitFormGenerico(event);" id="form-generico" action="__url__">
    <input type="hidden" name="pedido" value="__pedido__" id="pedido" />
    <input type="hidden" name="almacen" value="__almacen__" id="almacen" />

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <p>Está seguro que desea anular este pedido?. La acción no es reversible.</p>
        </div>
        <div class="form-group">
          <label for="buscar"></label>
          <Button class="btn btn-primary btn-submit" type="submit">
            <img id="loading-button" hidden src="/opm/assets/img/loading.svg" width="20px" alt="">
            Guardar
          </Button>
        </div>
      </div>
    </div>
  </form>
  </script>
  <script id="cerrar-pedido" type="text/template">
    <form onsubmit="return submitFormGenerico(event);" id="form-generico" action="__url__">
    <input type="hidden" name="pedido" value="__pedido__" id="pedido" />
    <input type="hidden" name="almacen" value="__almacen__" id="almacen" />

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <p>Está seguro que desea cerrar este pedido?. La acción no es reversible.</p>
        </div>
        <div class="form-group">
          <label for="buscar"></label>
          <Button class="btn btn-primary btn-submit" type="submit">
            <img id="loading-button" hidden src="/opm/assets/img/loading.svg" width="20px" alt="">
            Guardar
          </Button>
        </div>
      </div>
    </div>
  </form>
  </script>
  <script type="text/javascript">
    var table = null;
    var delay_function = null;

    $(document).ready(function () {
      $('#search').on('keyup', function () {
        if (delay_function !== null) {
          clearTimeout(delay_function);
        }
        delay_function = setTimeout(function () {
          table.search($('#search').val()).draw();
        }, 500);
      });
      $("#clase_documento").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonWidth: '100%',
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
          if (options.length === 0) {
            return 'Ninguno';
          } else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });
      $("#situacion").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonWidth: '100%',
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
          if (options.length === 0) {
            return 'Ninguno';
          } else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });
      datatable(null);
      $('#tblReg tbody').on('click', '.details-control', function () {
        var table = $("#tblReg").DataTable({
          'retrieve': true
        });
        var tr = $(this).closest('tr');
        //                    var dataTableRow = table.row( tr );
        var row = table.row(tr);
        var rowx = table.row(tr).data();
        if (row.child.isShown()) {
          $(tr.find('td:first')[0]).removeClass('details-close').addClass('details-open');
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');


        } else {
          // Open this row
          //row.child(format(details[rowx[1]]));
          $(tr.find('td:first')[0]).removeClass('details-open').addClass('details-close');
          row.child("<div>Cargando Espere ...</div>").show();
          tr.addClass('shown');
          $.LoadingOverlay("show");
          $.ajax({
            url: `/api/v1/obtener_pedprove_detalle.php`,
            data: {
              pedido: rowx.docompra
            },
            dataType: 'json',
            success: function (json) {
              $.LoadingOverlay("hide");
              generar_tabla_detalles(rowx.pedido, json.detalles, row);
            },
            error: function (xhr, error, thrown) {
              $.LoadingOverlay("hide");
              console.log(xhr);
              console.log(error);
              console.log(thrown);
            }
          });

        }
      });
      $("#form_filtros").submit(function (e) {
        e.preventDefault();
        $.LoadingOverlay("show");
        datatable(obtenerURL());
      });
    });

    function submitFormGenerico() {
      event.preventDefault();
      let url = $("#form-generico").attr('action');
      let ajaxData = $("#form-generico").serializeArray().reduce(function (obj, item) {
        obj[item.name] = item.value;
        return obj;
      }, {});
      $(".btn-submit").prop('disabled', true);
      $("#loading-button").show();

      $.ajax({
        type: "POST",
        url: url,
        data: ajaxData,
        success: function (response) {
          $("#modal-generico").modal("hide");
          if (response.exito) {
            datatable(obtenerURL());
            iziToast.success({
              title: 'Exito!',
              backgroundColor: "#70a415",
              titleColor: "white",
              messageColor: "white",
              message: response.mensaje,
            });
          } else {
            iziToast.error({
              title: 'Error!',
              message: response.mensaje,
            });
          }

        },
        error: function (xhr, error, thrown) {
          console.log(xhr);
          console.log(error);
          console.log(thrown);
        },
        complete: function () {
          $(".btn-submit").prop('disabled', false);
          $("#loading-button").hide();
        }
      });
      return false;
    }

    function addUsr(docompra, nombre) {

      $("#addUsr .modal-title").empty().append('Añadir ');
      $('#modalData').modal('hide');

      $('#addUsr').modal('show');
      $('#addUsr input[name="addPed"]').val(docompra);
      $('#addUsr input[name="addPro"]').val(nombre);
    }
    function obtenerURL() {
      let url = '/api/v1/obtener_pedprove.php?';
      url += `pedido=${$("#pedido").val()}&`;
      url += `proveedor=${$("#proveedor").val()}&`;
      url += `fecha_desde=${$("#fecha_desde").val()}&`;
      url += `fecha_hasta=${$("#fecha_hasta").val()}&`;

      url += `clase_documento=${$("#clase_documento").val() ?? ""}&`;
      url += `situacion=${$("#situacion").val() ?? ""}&`;
      url += `almacen=${$("#codalma").val()}`;
      return url;
    }

    function generar_tabla_detalles(pedido, datos, row) {
      let cuerpo = ``;
      let fila = ``;
      let columna_detalles = [{
        data: 'posnr',
        title: 'Pos.',
        className: 'dt-body-center'
      }, {
        data: 'artrefer',
        title: 'Material',
        className: 'dt-body-center'
      }, {
        data: 'artdesc',
        title: 'Descripcion',
        className: 'dt-body-center'
      }, {
        data: 'unimed',
        title: 'UM',
        className: 'dt-body-center'
      }, {
        data: 'canti',
        title: 'Cant.Pedido',
        className: 'dt-body-center'
      }, {
        data: 'preuni',
        title: 'Precio',
        className: 'dt-body-center'
      },
      {
        data: 'pretotal',
        title: 'Prec.Total',
        className: 'dt-body-center'
      },
      {
        data: 'cencod',
        title: 'Centro',
        className: 'dt-body-center'
      },
      {
        data: 'cod_alma',
        title: 'Almacen',
        className: 'dt-body-center'
      },
      {
        data: 'volumen',
        title: 'Grup.Art.',
        className: 'dt-body-center'
      },
      ];
      row.child(`<table class="table table-bordered table-condensed" style="width:100%" id='tabla-${pedido}'></table>`).show();
      let detailTable = $(`#tabla-${pedido}`).DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
        },
        info: false,
        ordering: false,
        searching: false,
        data: datos,
        scrollX: true,
        columns: columna_detalles,
      });
    }

    function datatable(url) {
      $.LoadingOverlay("show");
      if ($.fn.dataTable.isDataTable('#tblReg')) {
        table.ajax.url(url).load();
        $.LoadingOverlay("hide");
      } else {
        table = $('#tblReg').DataTable({
          dom: '<"top"B<lfrtip>><"clear">',
          buttons: [],
          ajax: {
            type: "GET",
            url: obtenerURL(),
            dataSrc: function (json) {
              $.LoadingOverlay("hide");
              return json.data;
            },
            error: function (xhr, error, thrown) {
              $.LoadingOverlay("hide");
              console.log(xhr);
              console.log(error);
              console.log(thrown);
            },
          },
          paging: true,
          processing: true,
          serverSide: true,
          searching: true,
          ordering: true,
          scrollX: true,
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
          },
          columnDefs: [{
            className: 'details-control',
            orderable: false,
            targets: 0
          }],
          columns: [{
            className: 'details-control details-open',
            orderable: false,
            data: null,
            defaultContent: ''
          }, {
            data: 'docompra',
            title: "Pedido",
            className: 'dt-body-center'
          },
          {
            data: 'clasdoc',
            title: "Clas.Doc.",
            className: 'dt-body-center',
          },
          {
            data: 'codsocie',
            title: "Sociedad",
            className: 'dt-body-center'
          },

          {
            data: 'nombre',
            title: "Proveedor",
            className: 'dt-body-center'
          },
          {
            data: 'fecre',
            title: "Fec.Pedido",
            className: 'dt-body-center'
          },
          {
            data: 'orgcompra',
            title: "Org.Compra",
            className: 'dt-body-center'
          },
          {
            data: 'grupcompra',
            title: "Grup.Compra.",
            className: 'dt-body-center'
          },

          {
            title: "Acciones",
            className: 'dt-body-center',
            mRender: function (data, type, row) {
              // Genera los botones del dropdown con atributos de datos personalizados
              let progrButton = `<li><a style="margin:0;padding:0" data-action="solicitadc" data-docompra="${row.docompra}" href="javascript:void(0);">Solicitar DC</a></li>`;
              let anularButton = `<li><a style="margin:0;padding:0" data-action="anularPedido" data-docompra="${row.docompra}" href="javascript:void(0);">Anular</a></li>`;
              let cerrarButton = `<li><a style="margin:0;padding:0" data-action="cerrarPedido" data-docompra="${row.docompra}" href="javascript:void(0);">Cerrar</a></li>`;
              let solicitaApro = `<li><a style="margin:0;padding:0" data-action="solicitarAprobacion" data-docompra="${row.docompra}" href="javascript:void(0);">Sol.Aprobacion</a></li>`;
              let addcontaine = `<li><a style="margin:0;padding:0" data-action="addData" data-docompra="${row.docompra}" href="javascript:void(0);">Add Container Data</a></li>`;
              let editcontaine = `<li><a style="margin:0;padding:0" data-action="updUsr" data-docompra="${row.docompra}" href="javascript:void(0);">Edit Container Data</a></li>`;
              let adjuntardocu = `<li><a style="margin:0;padding:0" data-action="adjuntardoc" data-docompra="${row.docompra}" href="javascript:void(0);">Adjuntar Documento</a></li>`;
              let modPedido = `<li><a style="margin:0;padding:0" data-action="modificarPedido" data-docompra="${row.docompra}" href="javascript:void(0);">Modificar Pedido</a></li>`;

              // Contenedor del grupo de botones
              let group = `
        <div class="btn-group">
            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Acciones <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-small">
                ${progrButton}
                ${anularButton}
                ${cerrarButton}
                ${solicitaApro}
                ${addcontaine}
                ${editcontaine}
                ${adjuntardocu}
                ${modPedido}
            </ul>
        </div>
    `;

              // Condición para ocultar el dropdown si el estado es "AN" o "CE"
              if (row.estado == "AN" || row.estado == "CE") {
                group = "";
              }
              return group;
            }

          },
          {
            title: "Observacion",
            className: 'dt-body-center',
            mRender: function (data, type, row) {
              let texto = `<a title="Add Observation">
                                            <span style="font-size:14px" onclick="addUsr('${row.docompra}','${row.nombre}')" class="glyphicon glyphicon-edit"></span></a>
                                         <a title="View Observation">
                                            <span style="font-size:14px" onclick="swModal('${row.docompra}')" class="glyphicon glyphicon-search"></span></a>`;
              return texto;
            }
          },
          {
            data: 'codmone',
            title: "Moneda",
            className: 'dt-body-center'
          },
          {
            data: 'totalp',
            title: '<span style="color: red;">Valor Pedido</span>',
            className: 'dt-body-center',
            render: function (data, type, row) {
              // Modifica el valor de la celda para que se muestre en rojo
              return '<span style="color: red;">' + data + '</span>';
            },
          },
          {
            data: 'userliberacion',
            title: "Liberado por",
            className: 'dt-body-center'
          },

          {
            title: "Estado",
            className: 'dt-body-center',
            mRender: function (data, type, row) {
              let label = "primary";
              switch (row.situped) {
                case "PD":
                  label = "default"
                  break;
                case "AN":
                  label = "warning"
                  break;
                case "CE":
                  label = "success"
                  break;
                case "DC":
                  label = "info"
                  break;
                case "PR":
                  label = "default"
                  break;
                default:
                  break;
              }
              let span = `<span class='label label-${label}' style="color:white">${row.situped}</span>`;
              return span;
            }
          }

          ],
        });
      }
    }
    // Maneja los clics en los botones del dropdown dinámico
    $(document).on('click', '.dropdown-menu li a', function (event) {
      event.preventDefault(); // Evita el comportamiento por defecto del enlace

      // Obtén el valor del atributo `data-action` y `data-docompra`
      const action = $(this).data('action');
      const docompra = $(this).data('docompra');

      // Valida si la función correspondiente existe y ejecútala
      if (action && typeof window[action] === 'function') {
        window[action](docompra);
      } else {
        console.error(`La acción "${action}" no está definida o no es una función.`);
      }
    });
    window.solicitadc = function (docompra) {
      console.log(`Solicitando DC para ${docompra}`);
      // Lógica aquí
    };

    window.anularPedido = function (docompra) {
      console.log(`Anulando pedido para ${docompra}`);
      
      let url = "/api/v1/anular_pedprove.php?docompra=" + docompra;
      let contenido = $("#anular-pedido").html();
      contenido = contenido.replace('__pedido__', docompra);
      contenido = contenido.replace('__url__', url);

      generarModal(`Anular Descarga ${docompra}`, contenido);
    };

    window.cerrarPedido = function (docompra) {
      console.log(`Cerrando pedido para ${docompra}`);
      
      let url = "/api/v1/cerrar_pedprove.php?docompra" + docompra;
      let contenido = $("#cerrar-pedido").html();
      contenido = contenido.replace('__pedido__', docompra);
      contenido = contenido.replace('__url__', url);

      generarModal(`Cerrar Descarga ${docompra}`, contenido);
    };

    window.solicitarAprobacion = function (docompra) {
      console.log(`Solicitando aprobación para ${docompra}`);
      
      let url = "/api/v1/guardar_solicitud_aprobacion.php?docompra=" + docompra;
      let contenido = $("#form-solicitar-aprobacion").html();
      contenido = contenido.replace('__pedido__', docompra);
      contenido = contenido.replace('__url__', url);

      generarModal(`Programar Descarga ${docompra}`, contenido);
    };

    window.addData = function (docompra) {
      console.log(`Agregando container data para ${docompra}`);

      pedidoATratar = pedido;
      // Lógica aquí
    };

    window.updUsr = function (addPue, addDes) {
      console.log(`Editando container data para ${docompra}`);
      
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updPue").val(addPue);
      $("#updDes").val(addDes);
      $('#editUsr').modal('show');
    };

    window.adjuntardoc = function (docompra) {
      console.log(`Adjuntando documento para ${docompra}`);
    };

    window.modificarPedido = function (docompra) {
      console.log(`Modificando pedido para ${docompra}`);
      // Lógica aquí
    };
    document.addEventListener('DOMContentLoaded', function () {
      let pedidoATratar = null;

      // Inicializar el componente de jspreadsheet
      const spreadsheet = jspreadsheet(document.getElementById('spreadsheet'), {
        data: [],
        columns: [
          { type: 'dropdown', title: 'Tipo Contenedor', width: 200, source: ['Contenedor 1', 'Contenedor 2', 'Contenedor 3'] },
          { type: 'numeric', title: 'Cantidad', width: 100 },
          { type: 'text', title: 'Nro. Contenedor', width: 150 },
          { type: 'text', title: 'Observacion', width: 300 }
        ],
        minDimensions: [4, 10], // Número mínimo de columnas y filas
        allowInsertRow: true,
        allowInsertColumn: true,
        allowDeleteRow: true,
        allowDeleteColumn: true
      });

      // Función para guardar los datos del jspreadsheet
      window.saveConte = function () {
        debugger
        if (typeof spreadsheet !== 'undefined' && typeof spreadsheet.getData === 'function') {
          // Obtener los datos del spreadsheet
          const data = spreadsheet.getData();

          // Validar que haya un pedido asignado
          if (!pedidoATratar) {
            alert('No se ha seleccionado un pedido.');
            return;
          }

          // Filtrar las filas que tengan todos los campos llenos
          const filteredData = data.filter(row => row.every(cell => cell !== null && cell !== ''));

          // Validar que no esté vacío
          if (filteredData.length === 0) {
            alert('No hay filas completas en el spreadsheet para guardar.');
            return;
          }

          // Crear un array estructurado para enviar al servidor
          const structuredData = filteredData.map(row => ({
            docompra: pedidoATratar,
            tipconte: row[0],
            canti: row[1],
            numconte: row[2],
            observacion: row[3]
          }));

          // Enviar los datos al servidor
          fetch('requests/saveDatConte.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: 'add',
              table: 'datconteped',
              fields: 'docompra,tipconte,numconte,canti,observacion',
              data: structuredData
            })
          })
            .then(response => {
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
            })
            .then(result => {
              // Validar la respuesta del servidor
              if (result && result.err === 0) {
                alert(result.msg);
                window.location = 'consulta_pedprove.php'; // Redirigir si el guardado fue exitoso
              } else {
                alert(result.msg || 'Error desconocido en el servidor');
              }
            })
            .catch(error => {
              console.error('Error al guardar los datos:', error);
              alert('Error al guardar los datos: ' + error.message);
            });
        } else {
          console.error('El objeto spreadsheet o el método getData no está disponible.');
        }
      };
    });
    /* function saveConte() {
          var Doc = $("#addDoc").val();  
          var Tco = $("#addTco").val();
          var Nco = $("#addNco").val();
          var Can = $("#addCan").val();
          var Obs = $("#addObs").val();
          
    
          if (Doc != '') {
            $.ajax({
              type: 'POST',
              url: 'requests/saveDatConte.php',
              data: {
                action: 'add',
                Doc: Doc,
                Tco: Tco,
                Nco: Nco,
                Can: Can,
                Obs: Obs,
              
    
                table: 'datconteped',
                fields: 'docompra,tipconte,numconte,canti,observacion'
              }, success: function (data) {
                var dt = JSON.parse(data);
                alert(dt.msg);
                if (dt.err == 0) {
                  window.location = 'consulta_pedprove.php';
                }
              }, error: function (request) {
                alert(request.responseJSON.error);
              }
            });
          } else {
            alert('Favor ingresar una descripción');
          }
        }*/
    /* function saveConte() {
    const data = jspreadsheet.getData('spreadsheet');
    
    // Obtener los datos de las filas de la tabla
    
    let tipconte = data[1][1];  // Primera fila, segunda columna (Tipo Contenedor)
    let canti = data[1][2];  // Primera fila, tercera columna (Cantidad)
    let numconte = data[1][3];  // Primera fila, cuarta columna (Nro. Contenedor)
    let observacion = data[1][4];  // Primera fila, quinta columna (Observacion)

    // Validar los datos
    if (!docompra || !tipconte || !canti || !numconte || !observacion) {
        alert("Por favor complete todos los campos.");
        return;
    }
    //conole.log (tipconte);
    // Enviar los datos al servidor con AJAX o formularios
    let formData = new FormData();
   // formData.append('addDoc', docompra);
    formData.append('addTco', tipconte);
    formData.append('addCan', canti);
    formData.append('addNco', numconte);
    formData.append('addObs', observacion);

    fetch ('saveDatConte.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Contenedor guardado exitosamente');
            $('#addDat').modal('hide');  // Cerrar el modal
        } else {
            alert('Error al guardar el contenedor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error.');
    });
} */
    function swModal(docompra) {
      let ped = docompra;
      //    console.log(ped);

      if (ped != '') {
        $.ajax({
          type: 'POST',
          url: 'requests/getObservaciones.php',
          data: {

            ped: docompra

          }, success: function (data) {
            var dt = JSON.parse(data);
            if (!dt.err) {
              var mytable = '<table class="table table-hover table-striped table-bordered  table-condensed"><thead>' + dt.hdr + '</thead><tbody>';

              $.each(dt.cab, function (key, value) {
                mytable += "<tr>";
                $.each(value, function (k, v) {
                  mytable += "<td>" + v + "</td>";
                });
                mytable += "</tr>";
              });
              $("#modalData .modal-dialog.modal-lg .modal-content .modal-body").empty().append(mytable);
              $("#modalData .modal-dialog.modal-lg .modal-content .modal-title").empty().append(dt.tit);

              $('#modalData').modal('show');
            } else {
              alert(dt.msg);
            }
          }
        });
      } else {
        alert("Debe consultar un artículo.");
      }
      //                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
    }
    function generarModal(titulo, contenido) {

      $("#modal-generico .modal-title").empty().text(titulo);
      $("#modal-generico .modal-body").empty().html(contenido);
      $("#modal-generico").modal('show');

    }

    const express = require('express');
    const multer = require('multer');
    const path = require('path');
    const app = express();

    // Configuración de Multer para el almacenamiento de archivos
    const storage = multer.diskStorage({
      destination: function (req, file, cb) {
        cb(null, 'uploads/'); // Guardamos los archivos en la carpeta 'uploads'
      },
      filename: function (req, file, cb) {
        cb(null, Date.now() + path.extname(file.originalname)); // Asignamos un nombre único
      }
    });

    const upload = multer({ storage: storage });

    // Endpoint para manejar la subida de archivos
    app.post('/upload_documents', upload.array('documents[]'), (req, res) => {
      const docCompra = req.body.doccompra;  // Obtenemos el valor de docCompra
      const files = req.files;  // Obtenemos los archivos subidos

      if (!files || files.length === 0) {
        return res.status(400).send('No se cargaron archivos.');
      }

      // Aquí puedes guardar la información de los archivos en la base de datos
      // Ejemplo de cómo guardar la ruta de cada archivo:
      files.forEach(file => {
        const filePath = path.join('uploads', file.filename);

        // Aquí guardas la ruta y el docCompra en la base de datos
        // Ejemplo usando un ORM (en una base de datos SQL):
        // db.query("INSERT INTO documentos (doccompra, file_path) VALUES (?, ?)", [docCompra, filePath]);
      });

      res.send('Archivos subidos con éxito.');
    });

    // Servir archivos estáticos (para acceder a los documentos subidos)
    app.use('/uploads', express.static('uploads'));

    // Iniciar el servidor
    app.listen(3000, () => {
      console.log('Servidor corriendo en puerto 3000');
    });

    // Función para abrir el modal
    function openModal(docCompra) {
      // Abrir el modal
      document.getElementById('fileModal').style.display = 'block';
      // Guardar el valor de docCompra en una variable global para usarlo al enviar el archivo
      window.currentDocCompra = docCompra;
    }

    // Función para cerrar el modal
    function closeModal() {
      document.getElementById('fileModal').style.display = 'none';
    }

    // Función para manejar la carga de documentos
    function uploadDocuments() {
      var files = document.getElementById("documents").files;

      if (files.length === 0) {
        alert('Por favor selecciona al menos un archivo.');
        return;
      }

      var formData = new FormData();

      // Agregar los archivos seleccionados
      for (var i = 0; i < files.length; i++) {
        formData.append("documents[]", files[i]);  // Usamos 'documents[]' para manejar múltiples archivos
      }

      // También pasamos el valor de docCompra
      formData.append("doccompra", window.currentDocCompra);

      // Realizamos la petición AJAX para subir los documentos
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "/upload_documents", true);

      xhr.onload = function () {
        if (xhr.status === 200) {
          alert('Archivos subidos con éxito.');
          closeModal();  // Cerrar el modal después de subir los archivos
        } else {
          alert('Hubo un error al subir los archivos.');
        }
      };

      xhr.send(formData);
    }
    function addDat(docompra) {
      $("#addDat .modal-title").empty().append('Añadir ');
      $('#addDat').modal('show');
      $('#addDat input[name="addDoc"]').val(docompra);
    }
    function updDat(addPue, addDes) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updPue").val(addPue);
      $("#updDes").val(addDes);
      $('#editUsr').modal('show');
    }
    function saveAdd() {
      var Ped = $("#addPed").val();  // Obtener el valor del campo "addPed"
      var Obs = $("#addObs").val();  // Obtener el valor del campo "addObs"

      // Realizar la petición AJAX independientemente de si "Ped" está vacío o no
      $.ajax({
        type: 'POST',
        url: 'requests/savePedproveobse.php',  // URL del archivo PHP que maneja la solicitud
        data: {
          action: 'add',    // Acción a realizar en el servidor
          Ped: Ped,         // Pasar el valor de Ped (descripción)
          Obs: Obs,         // Pasar el valor de Obs (observación)
          table: 'obsepedprove',  // Tabla de base de datos a la que se debe insertar
          fields: 'pedprove,observacion'  // Campos de la tabla a insertar
        },
        success: function (data) {
          var dt = JSON.parse(data);  // Parsear la respuesta JSON del servidor
          alert(dt.msg);  // Mostrar el mensaje retornado por el servidor
          if (dt.err == 0) {
            window.location = 'consulta_pedprove.php';  // Redirigir si no hay errores
          }
        },
        error: function (request) {
          alert(request.responseJSON.error);  // Mostrar el error si la solicitud falla
        }
      });
    }

    function solicitadc(pedido) {
    }

    function anularPedido(docompra) {
      let url = "/api/v1/anular_pedprove.php?docompra=" + docompra;
      let contenido = $("#anular-pedido").html();
      contenido = contenido.replace('__pedido__', docompra);
      contenido = contenido.replace('__url__', url);

      generarModal(`Anular Descarga ${docompra}`, contenido);
    }

    function cerrarPedido(docompra) {
    }

    async function asReception(ped, almacen) {
      await obtenerTerminalesOnOpenModal(almacen);
      $(".modal-title").empty().append('sdds pedido <br/><b>#' + ped + '</b>');
      $("#recepcion").val(ped);
      $("#codalmacen").val(almacen);
      $('#assignRec').modal('show');
    }

    function saveAssign() {
      var pedido = $("#recepcion").val();
      var terminal = $("#terminal").val();
      let almacen = $("#codalmacen").val();
      if (!terminal) {
        alert("Almacen no valido");
        return;
      }
      data = {
        pedido: pedido,
        terminal: terminal,
        almacen: almacen
      };
      $.ajax({
        type: 'POST',
        url: 'requests/asignar_pedido_termial.php',
        data: data,
        success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          $('#sndBtn').click();
        }
      });
    }

    function obtenerTerminalesOnOpenModal(codalma) {
      $.ajax({
        type: 'POST',
        url: 'requests/obtenerTerminales.php',
        data: {
          codalma: codalma
        },
        success: function (data) {
          var dt = JSON.parse(data);
          $("#terminal").empty();
          dt['almacenes'].forEach(element => {
            let option = `<option value="${element.tercod}">${element.tercod} - ${element.terdes}</option>`;
            $("#terminal").append(option);
          });
        }
      });
    }
  </script>
</body>

</html>