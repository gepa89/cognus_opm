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
    const BASE_URL = '/api/v1/';

    $(document).ready(function () {
      initializeSearch();
      initializeMultiselects(["#clase_documento", "#situacion"]);
      datatable(null);

      $('#tblReg tbody').on('click', '.details-control', handleDetailsControl);
      $("#form_filtros").submit(handleFilterForm);
      initializeSpreadsheet();
      setupDynamicActions();
      setupModals();
    });

    /**
     * Initialize search functionality with debounce.
     */
    function initializeSearch() {
      let delayFunction = null;

      $('#search').on('keyup', function () {
        if (delayFunction) clearTimeout(delayFunction);
        delayFunction = setTimeout(() => {
          table.search($('#search').val()).draw();
        }, 500);
      });
    }

    /**
     * Initialize multiselect dropdowns.
     * @param {Array} selectors - Array of selector strings for multiselect elements.
     */
    function initializeMultiselects(selectors) {
      selectors.forEach(selector => {
        $(selector).multiselect({
          selectAllText: 'Todos',
          includeSelectAllOption: true,
          enableFiltering: true,
          buttonWidth: '100%',
          enableCaseInsensitiveFiltering: true,
          buttonText: function (options) {
            return options.length === 0 ? 'Ninguno' : `${options.length} seleccionado(s)`;
          }
        });
      });
    }

    /**
     * Initialize and manage the DataTable.
     * @param {string|null} url - URL for DataTable's AJAX source.
     */
    function datatable(url) {
      $.LoadingOverlay("show");

      if ($.fn.dataTable.isDataTable('#tblReg')) {
        table.ajax.url(url).load();
        $.LoadingOverlay("hide");
      } else {
        table = $('#tblReg').DataTable({
          dom: '<"top"Bfrtip>',
          ajax: {
            type: "GET",
            url: obtenerURL(),
            dataSrc: function (json) {
              $.LoadingOverlay("hide");
              return json.data;
            },
            error: logAjaxError
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
          columns: getTableColumns()
        });
      }
    }

    /**
     * Define columns for the DataTable.
     * @returns {Array} Columns configuration.
     */
    function getTableColumns() {
      return [
        { className: 'details-control details-open', orderable: false, data: null, defaultContent: '' },
        { data: 'docompra', title: "Pedido", className: 'dt-body-center' },
        { data: 'clasdoc', title: "Clas.Doc.", className: 'dt-body-center' },
        { data: 'codsocie', title: "Sociedad", className: 'dt-body-center' },
        { data: 'nombre', title: "Proveedor", className: 'dt-body-center' },
        { data: 'fecre', title: "Fec.Pedido", className: 'dt-body-center' },
        { data: 'orgcompra', title: "Org.Compra", className: 'dt-body-center' },
        { data: 'grupcompra', title: "Grup.Compra.", className: 'dt-body-center' },
        { data: 'codmone', title: "Moneda", className: 'dt-body-center' },
        { data: 'totalp', title: '<span style="color: red;">Valor Pedido</span>', className: 'dt-body-center', render: data => `<span style="color: red;">${data}</span>` },
        { data: 'userliberacion', title: "Liberado por", className: 'dt-body-center' },
        { title: "Estado", className: 'dt-body-center', mRender: renderStatus },
        { title: "Acciones", className: 'dt-body-center', mRender: renderActions }
      ];
    }

    /**
     * Render the status column in DataTable.
     * @param {any} data - Status data.
     * @param {string} type - Column type.
     * @param {Object} row - Row data.
     * @returns {string} Rendered HTML.
     */
    function renderStatus(data, type, row) {
      const statusLabels = {
        "PD": "default",
        "AN": "warning",
        "CE": "success",
        "DC": "info",
        "PR": "default"
      };
      const labelClass = statusLabels[row.situped] || "primary";
      return `<span class='label label-${labelClass}' style="color:white">${row.situped}</span>`;
    }

    /**
     * Render the actions column in DataTable.
     * @param {any} data - Data for the column.
     * @param {string} type - Column type.
     * @param {Object} row - Row data.
     * @returns {string} Rendered HTML for actions.
     */
    function renderActions(data, type, row) {
      if (["AN", "CE"].includes(row.estado)) return "";

      const actions = [
        createActionButton("solicitadc", "Solicitar DC", row.docompra),
        createActionButton("anularPedido", "Anular", row.docompra),
        createActionButton("cerrarPedido", "Cerrar", row.docompra),
        createActionButton("solicitarAprobacion", "Sol. Aprobacion", row.docompra),
        createActionButton("addDat", "Add Container Data", row.docompra),
        createActionButton("updUsr", "Edit Container Data", row.docompra),
        createActionButton("adjuntardoc", "Adjuntar Documento", row.docompra),
        createActionButton("modificarPedido", "Modificar Pedido", row.docompra, "mod_pedidos.php")
      ];

      return `<div class="btn-group">
                <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    Acciones <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-small">
                    ${actions.join("\n")}
                </ul>
            </div>`;
    }

    /**
     * Create a single action button for DataTable actions.
     * @param {string} action - Action identifier.
     * @param {string} text - Button text.
     * @param {string} docompra - Associated `docompra` value.
     * @param {string} [url="javascript:void(0);"] - URL for the action.
     * @returns {string} Rendered HTML for the action button.
     */
    function createActionButton(action, text, docompra, url = "javascript:void(0);") {
      return `<li><a class="dynamic-action" data-action="${action}" data-docompra="${docompra}" href="${url}">${text}</a></li>`;
    }

    /**
     * Handle click events for dynamic actions.
     */
    document.addEventListener('click', function (event) {
      const target = event.target;

      if (target && target.classList.contains('dynamic-action')) {
        event.preventDefault();

        const action = target.getAttribute('data-action');
        const docompra = target.getAttribute('data-docompra');

        handleAction(action, docompra);
      }
    });

    /**
     * Handle specific actions triggered by dynamic action buttons.
     * @param {string} action - Action identifier.
     * @param {string} docompra - Associated `docompra` value.
     */
    function handleAction(action, docompra) {
      switch (action) {
        case 'solicitadc':
          openModal(`Solicitar DC para ${docompra}`, '#solicitar-dc-modal');
          break;
        case 'anularPedido':
          openModal(`Anular Pedido ${docompra}`, '#anular-pedido-modal');
          break;
        case 'cerrarPedido':
          openModal(`Cerrar Pedido ${docompra}`, '#cerrar-pedido-modal');
          break;
        case 'addDat':
          window.currentDocCompra = docompra;
          openModal('Agregar Contenedor', '#add-container-modal');
          break;
        default:
          console.error('Acción no reconocida:', action);
      }
    }

    /**
     * Open a modal with the given title and selector.
     * @param {string} title - Modal title.
     * @param {string} selector - Modal selector.
     */
    function openModal(title, selector) {
      const modal = $(selector);
      if (!modal.length) {
        console.error(`Modal not found for selector: ${selector}`);
        return;
      }
      modal.find('.modal-title').text(title);
      modal.modal('show');
    }

    /**
     * Generate URL for DataTable's AJAX source based on filters.
     * @returns {string} Generated URL.
     */
    function obtenerURL() {
      const params = new URLSearchParams({
        pedido: $("#pedido").val(),
        proveedor: $("#proveedor").val(),
        fecha_desde: $("#fecha_desde").val(),
        fecha_hasta: $("#fecha_hasta").val(),
        clase_documento: $("#clase_documento").val() || "",
        situacion: $("#situacion").val() || "",
        almacen: $("#codalma").val()
      });
      return `${BASE_URL}obtener_pedprove.php?${params.toString()}`;
    }

    /**
     * Log AJAX errors to the console.
     */
    function logAjaxError(xhr, error, thrown) {
      $.LoadingOverlay("hide");
      console.error(xhr, error, thrown);
    }

    /**
     * Handle the filter form submission.
     * @param {Event} event - Form submission event.
     */
    function handleFilterForm(event) {
      event.preventDefault();
      $.LoadingOverlay("show");
      datatable(obtenerURL());
    }

    /**
     * Initialize the spreadsheet functionality.
     */
    function initializeSpreadsheet() {
      const spreadsheet = jspreadsheet(document.getElementById('spreadsheet'), {
        data: [],
        columns: [
          { type: 'dropdown', title: 'Tipo Contenedor', width: 200, source: ['Contenedor 1', 'Contenedor 2', 'Contenedor 3'] },
          { type: 'numeric', title: 'Cantidad', width: 100 },
          { type: 'text', title: 'Nro. Contenedor', width: 150 },
          { type: 'text', title: 'Observacion', width: 300 }
        ],
        minDimensions: [4, 10],
        allowInsertRow: true,
        allowInsertColumn: true,
        allowDeleteRow: true,
        allowDeleteColumn: true
      });

      window.saveConte = function () {
        if (typeof spreadsheet !== 'undefined' && typeof spreadsheet.getData === 'function') {
          const data = spreadsheet.getData();
          const filteredData = data.filter(row => row.every(cell => cell !== null && cell !== ''));

          if (filteredData.length === 0) {
            alert('No hay filas completas en el spreadsheet para guardar.');
            return;
          }

          const structuredData = filteredData.map(row => ({
            docompra: window.currentDocCompra,
            tipconte: row[0],
            canti: row[1],
            numconte: row[2],
            observacion: row[3]
          }));

          fetch('requests/saveDatConte.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              action: 'add',
              table: 'datconteped',
              fields: 'docompra,tipconte,numconte,canti,observacion',
              data: structuredData
            })
          })
            .then(response => response.json())
            .then(result => {
              if (result.err === 0) {
                alert(result.msg);
                window.location = 'consulta_pedprove.php';
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
    }

  </script>
</body>

</html>