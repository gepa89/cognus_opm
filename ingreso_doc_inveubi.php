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
              <h3 class="panel-title">Creacion Documento de Inventario por Ubicacion</h3>
            </div>
            <div class="panel-body">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="almacen">Almacen Entrada:</label>
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
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="tipo">Tipos de Ubicaciones:</label>
                    <select class="form-control" id="tipo" name="tipo">
                      <option value="todos" selected>Todos</option>
                      <?php
                      $sql = "select * from tipos";
                      $rs = $db->query($sql);
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['codtipo'] . '">' . $ax['codtipo'] . ' - ' . $ax['descripcion'] . '</option>';
                      }
                      ?>
                    </select>
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
    $(document).ready(function() {
      var spread = null;

      $('#form').submit(function(e) {
        e.preventDefault();
        formData = $('#form').serializeArray();
        object = {};
        formData.forEach(function(value, key) {
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
          url: 'api/guardarDocInveUbi.php',
          data: {
            tipo: $("#tipo").val(),
            mic: $("#mic").val(),
            crt: $("#crt").val(),
            factura: $("#factura").val(),
            documento: $("#documento").val(),
            clase_documento: $("#clase_documento").val(),
            cod_almacen: $("#almacen").val(),
            detalles: spread.getData()
          },
          success: function(data) {
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

      var beforeChange = function(instance, cell, x, y, value) {
        //Todo: agregar validacion precio total
      }
      var blockSection = false;
      var changed = function(instance, cell, x, y, value) {
        if (blockSection) {
          return;
        }
        blockSection = true;
        let cellName = jexcel.getColumnNameFromId([x, y]);
        if (x == 0) {
          let tipo = $("#tipo").val();
          
          let pos = Number(x);
          let y_axis = Number(y);
          $.ajax({
            url: 'api/obtenerArticulosInveUbi.php',
            type: "GET",
            data: {
              cod_articulo: value,
              tipo: tipo
            },
            dataType: "json",
            success: function(data) {
              if (data.estado === "exito") {
                for (let i = 0; i < data.dato.length; i++) {
                  const element = data.dato[i];
                  spread.setValueFromCoords(pos, y_axis, element.ubirefer);
                  spread.setValueFromCoords(pos + 1, y_axis, element.artdesc);
                  spread.setValueFromCoords(pos + 2, y_axis, element.total);
                  // setear unidad de medida
                  spread.setValueFromCoords(pos + 3, y_axis, element.artrefer);
                  // setear precio
                  spread.setValueFromCoords(pos + 4, y_axis, element.ubitipo);
                  y_axis = y_axis + 1;
                }
                // setear descripcion material
              }
              blockSection = false;
            },
            error: function(jqXHR, exception) {
              blockSection = false;
            }
          });
        }
      }
      $('#inven4').addClass('active');
      $('#c8').click();
      $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });

      var width = document.getElementById('table-container').offsetWidth;
      colWidths = [100, 400, 60, 80, 80];
      let cantidadColumnas = 5;
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
        minDimensions: [5, 20],
        tableOverflow: true,
        tableWidth: `${width}px`,
        allowManualInsertColumn: false,
        columns: [{
            type: 'text'
          },
          {
            type: 'text'
          },
          {
            type: 'numeric'
          },
          {
            type: 'text'
          },
          {
            type: 'text'
          },

        ],
        tableOverflow: true,
        colHeaders: ['Ubicacion', 'Descripción', 'Stock', 'Material', 'Tipo'],
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
      spread = jexcel(document.getElementById('datos'), opciones);
    });
    /*$("#tipo").multiselect({
      selectAllText: 'Todos',
      includeSelectAllOption: true,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      buttonText: function(options) {
        if (options.length === 0) {
          return '--TIPO--';
        } else if (options.length > 0) {
          return options.length + ' selecionado(s)';
        }
      }
    });*/
  </script>
</body>

</html>