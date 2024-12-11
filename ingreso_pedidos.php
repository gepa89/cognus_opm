<?php

require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();

verificarUsuarioLogueado();
//verificarPermisoEscritura("externo", "ingreso_pedido");
if (!tieneAccesoAModulo("externo")) {
  echo "No tiene acceso";
  exit;
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT cod_dim as id, dimension as name FROM medpallet";
$query = $db->query($sql);
$medidas = $query->fetch_all(MYSQLI_ASSOC);

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT preseref as id, presedes as name FROM presen";
$query = $db->query($sql);
$presmedi = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head_ds.php' ?>

<body class="full_width">
  <div id="maincontainer" class="clearfix">
    <?php include 'header.php' ?>
    <div id="contentwrapper">
      <div class="main_content">
        <form id="form" method="post">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Creacion Pedidos de Entrada</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="cliente">Cliente:</label>
                    <select id="cliente" name="cliente" style="height: 40px !important;" required
                      class="form-control input-lg"></select>
                  </div>
                </div>
                <!-- comment <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">Documento:</label>
                    <select id="clase_documento" name="documento" class="form-control"></select>
                  </div>
                </div>-->

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="cliente">Documentos:</label>
                    <select id="documento" required class="form-control">
                      <?php
                      $sq = "select * from clasdoc where ensal = 'in'";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['pedclase'] . '">' . $ax['pedclase'] . ' - ' . utf8_encode($ax['descripcion']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <label for="cliente">Moneda:</label>
                    <select id="moneda" required name="moneda" class="form-control">
                      <option selected disabled value=""></option>
                      <option value="PYG">PYG</option>
                      <option value="USD">USD</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="cliente">Tipo:</label>
                    <select id="tipo" required name="tipo" class="form-control">
                      <option selected disabled value=""></option>
                      <option value="IC04">IC04</option>
                      <option value="IC09">IC09</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="cliente">MIC:</label>
                    <input id="mic" type="type" name="mic" class="form-control">
                  </div>
                </div> 
              </div>
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="cliente">Almacen Entrada:</label>
                    <select id="almacen" required class="form-control">
                      <?php
                      $sq = "select * from alma";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . utf8_encode($ax['almdes']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="cliente">Nro.Pedido:</label>
                    <input id="pedgen" disabled name="pedgen" class="form-control">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="cliente">CRT:</label>
                    <input id="crt" type="type" name="crt" class="form-control">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="cliente">Factura:</label>
                    <input id="factura" type="type" name="factura" class="form-control">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Configuración Pedidos - Detalles</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-12" id="datos">
                  <div id="table-container" style="width: 100%;"></div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 pull-right">
                  <button type="submit" class="btn btn-primary pull-right">Guardar</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div style="clear:both;"></div>
  <?php
  include 'sidebar.php';
  include 'js_in.php';

  ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/es.js"></script>

  <script>
    var dimensiones = <?php echo json_encode($medidas); ?>;
    var unimedidas = <?php echo json_encode($presmedi); ?>;
    var columnaCantidad = 2;
    var columnaPrecio = 4;
    $(document).ready(function () {
      var spread = null;

      $('#form').submit(function (e) {
        e.preventDefault();
        formData = $('#form').serializeArray();
        object = {};
        formData.forEach(function (value, key) {
          object[key] = value;
        });
        let datos = spread.getData();
        let datos_filtrados = [];
        for (const row of datos) {
          if (row[0].trim() == '' || row[2].trim() == '' || row[4].trim() == '') {
            continue;
          }
          datos_filtrados.push(row);
        }
        if (datos_filtrados.length == 0) {
          return;
        }
        $.ajax({
          type: 'POST',
          url: 'api/guardarIngresoPedidos.php',
          data: {
            cliente: $("#cliente").val(),
            moneda: $("#moneda").val(),
            tipo: $("#tipo").val(),
            mic: $("#mic").val(),
            crt: $("#crt").val(),
            factura: $("#factura").val(),
            documento: $("#documento").val(),
            clase_documento: $("#clase_documento").val(),
            cod_almacen: $("#almacen").val(),
            detalles: spread.getData()
          }, success: function (data) {
            spread.destroy();
            spread = jexcel(document.getElementById('datos'), opciones);
            Swal.fire({
              title: 'Exito!!',
              icon: 'success',
              html: `<h3>Pedido guardado. ID pedido <span style='color:red'>${data.id_pedido}</span></h3>`,
              showDenyButton: false,
              showCancelButton: false,
              confirmButtonText: 'Continuar',
            }).then((result) => {
              /* Read more about isConfirmed, isDenied below */
              if (result.isConfirmed) {
                window.location.reload();
              }
            });
          }
        });
      });
      $('#cliente').select2({
        ajax: {
          url: '/wmsd/api/obtenerClientes.php',
          dataType: "json",
          language: "es",
          delay: 250,
          data: function (params) {
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
      $('#moneda').select2().data('select2').$selection.css('height', '32px');

      var beforeChange = function (instance, cell, x, y, value) {
        //Todo: agregar validacion precio total
      }
      var changed = function (instance, cell, x, y, value) {
        let cellName = jexcel.getColumnNameFromId([x, y]);
        if (x == 0) {
          let pos = Number(x);
          $.ajax({
            url: 'api/obtenerArticulosDocumento.php',
            type: "GET",
            data: {
              cod_articulo: value
            },
            dataType: "json",
            success: function (data) {
              if (data.estado === "exito") {
                // setear descripcion material
                spread.setValueFromCoords(pos + 1, y, data.dato.artdesc);
                // setear unidad de medida
                spread.setValueFromCoords(pos + 3, y, data.dato.unimed);
                // setear precio
                spread.setValueFromCoords(pos + 5, y, data.dato.costo);

              }
            }
          });
        }
      }

      var width = document.getElementById('table-container').offsetWidth;
      colWidths = [100, 400, 60, 80, 80, 80, 80, 60, 60, 60, 125];
      let cantidadColumnas = 11;
      let suma = 0;
      for (const col of colWidths) {
        suma = suma + col;
      }
      let newColWidths = [];
      for (const col of colWidths) {
        let valor = (col * (width - 80)) / suma;
        newColWidths.push(Math.ceil(valor));
      }
      var opciones = {
        onchange: changed,
        allowInsertColumn: false,
        allowInsertRow: true,
        allowDeletingAllRows: false,
        allowDeleteColumn: false,
        allowDeleteRow: true,
        allowRenameColumn: false,
        copyCompatibility: false,
        minDimensions: [11, 20],
        tableOverflow: true,
        tableWidth: `${width}px`,
        allowManualInsertColumn: false,
        columns: [
          { type: 'text' },
          { type: 'text' },
          { type: 'numeric' },
          {
            type: 'dropdown', source: unimedidas
          },
          { type: 'numeric' },
          { type: 'numeric' },
          { type: 'numeric' },
          { type: 'numeric' },
          { type: 'numeric' },
          { type: 'numeric' },
          {
            type: 'dropdown', source: dimensiones
          },
        ],
        tableOverflow: true,
        colHeaders: ['Material', 'Descripción', 'Cantidad', 'UM', 'Caj. x Pallets', 'Prec.unit', 'Prec. Total', 'Vol.m3', 'Peso.N', 'Peso.B', 'Tam.Pallet'],
        colWidths: newColWidths,
        text: {
          noRecordsFound: 'NNo se encontraron registros',
          showingPage: 'Mostrando página {0} de {1} ',
          show: 'Mostrar ',
          search: 'Buscar',
          entries: ' entradas',
          insertANewColumnBefore: 'Añadir una nueva columna antes',
          insertANewColumnAfter: 'Añadir una nueva columna despues',
          deleteSelectedColumns: 'Borrar columnas seleccionadas',
          renameThisColumn: 'Renombrar esta columna',
          orderAscending: 'Ordenar ascendente',
          orderDescending: 'Ordenar descendente',
          insertANewRowBefore: 'Añadir una nueva fila antes',
          insertANewRowAfter: 'Añadir una nueva fila despues',
          deleteSelectedRows: 'Borrar filas seleccionadas',
          editComments: 'Editar comentarios',
          addComments: 'Añadir comentarios',
          comments: 'Comentarios',
          clearComments: 'Limpiar comentarios',
          copy: 'Copiar...',
          paste: 'Pegar...',
          saveAs: 'Guardar como...',
          about: 'Acerca de',
          areYouSureToDeleteTheSelectedRows: '¿Está seguro de borrar las filas seleccionadas?',
          areYouSureToDeleteTheSelectedColumns: '¿Está seguro de borrar las columnas seleccionadas?',
          thisActionWillDestroyAnyExistingMergedCellsAreYouSure: 'This action will destroy any existing merged cells. Are you sure?',
          thisActionWillClearYourSearchResultsAreYouSure: 'This action will clear your search results. Are you sure?',
          thereIsAConflictWithAnotherMergedCell: 'There is a conflict with another merged cell',
          invalidMergeProperties: 'Invalid merged properties',
          cellAlreadyMerged: 'Cell already merged',
          noCellsSelected: 'Ninguna celda seleccionada',

        }
      };
      spread = jexcel(document.getElementById('datos'), opciones
      );
    });
  </script>
</body>

</html>