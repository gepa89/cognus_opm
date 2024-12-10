<?php

require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
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

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT tipconte as id, nombre as name FROM contenedor";
$query = $db->query($sql);
$contenedores = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head_ds.php' ?>

<body class="full_width">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
    integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <div id="maincontainer" class="clearfix">
    <?php include 'header.php' ?>
    <div id="contentwrapper">
      <div class="main_content">
        <form id="form" method="post">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Contratacion de Seguro</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Nro.Pedido:</label>
                    <div class="input-group">
                      <input type="search" id="id_pedido_buscar" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-primary" id="buscar_pedido" type="button"><span
                            class="glyphicon glyphicon-search" aria-hidden="true">
                          </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="proveedor">Proveedor:</label>
                    <select id="proveedor" name="proveedor" required class="js-data-example-ajax form-control"></select>
                  </div>
                </div>
                <!-- comment <div class="col-sm-4">
                  <div class="form-group">
                    <label for="cliente">Documento:</label>
                    <select id="clase_documento" name="documento" class="js-data-example-ajax form-control"></select>
                  </div>
                </div>-->

                <div class="col-sm-1">
                  <div class="form-group">
                    <label for="proveedor">Tip.Flete:</label>
                    <select id="clasflete" required name="moneda" class="js-data-example-ajax form-control">
                      <option selected disabled value=""></option>
                      <option value="INTERNO">Flete Local</option>
                      <option value="EXTERNO">Flete Exterior</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-1">
                  <div class="form-group">
                    <label for="proveedor">Moneda:</label>
                    <select id="moneda" required name="moneda" class="js-data-example-ajax form-control">
                      <option selected disabled value=""></option>
                      <option value="PYG">PYG</option>
                      <option value="USD">USD</option>
                    </select>
                  </div>
                </div>
             
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="proveedor">Sociedad:</label>
                    <select id="codsocie" required class="form-control">
                      <?php
                      $sq = "select * from sociedad";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['codsocie'] . '">' . $ax['codsocie'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Org.Compra:</label>
                    <select id="orgcompra" required class="form-control">
                      <?php
                      $sq = "select * from orgacompra";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['orgcompra'] . '">' . $ax['orgcompra'] . ' - ' . utf8_encode($ax['orgdes']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div> 
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Puerto de Carga:</label>
                    <select id="puertcarga" required class="form-control">
                      <?php
                      $sq = "select * from puertos";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['codpuerto'] . '">' . $ax['codpuerto'] . ' - ' . utf8_encode($ax['nompuerto']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div> 
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Incoterm:</label>
                    <select id="incote" required class="form-control">
                      <?php
                      $sq = "select * from incoterm";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['incote'] . '">' . $ax['incote'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="proveedor">Factura:</label>
                    <input id="factura" type="type" name="factura" class="js-data-example-ajax form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Centro:</label>
                    <select id="cencod" required class="form-control">
                      <?php
                      $sq = "select * from centros";
                      $rs = $db->query($sq);
                      echo '<option value="" selected disabled>Seleccionar</option>';
                      while ($ax = $rs->fetch_assoc()) {
                        echo '<option value="' . $ax['cencod'] . '">' . $ax['cencod'] . ' - ' . utf8_encode($ax['cendes']) . '</option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>  
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Almacen:</label>
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
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Nro.Pedido:</label>
                    <input id="pedgen" disabled name="pedgen" class="js-data-example-ajax form-control">
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="proveedor">Status Pedido:</label>
                    <input id="stped" disabled name="stped" class="js-data-example-ajax form-control">
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
    var spread = null;
    var dimensiones = <?php echo json_encode($medidas); ?>;
    var unimedidas = <?php echo json_encode($presmedi); ?>;
    var contenedor = <?php echo json_encode($contenedores); ?>;
    var columnaCantidad = 2;
    var columnaPrecio = 4;
    function buscarPedido(iDPedido) {
      $.ajax({
        type: 'GET',
        url: 'api/obtenerPedFlete.php',
        data: {
          id_pedido: iDPedido,
        }, success: function (data) {
          if (data.estado === "exito") {
            let cabecera = data.datos.cabecera;
            let option = new Option(cabecera.nombre, cabecera.codprove, true, true);
            $('#proveedor').append(option).trigger('change');
            $('#proveedor').val(cabecera.codprove);
            $('#proveedor').trigger('change');
            $("#moneda").val(cabecera.codmone).trigger('change');
            $("#codsocie").val(cabecera.codsocie).trigger('change');
            $("#documento").val(cabecera.clasdoc);
            $("#puertcarga").val(cabecera.puertcarga);
            $("#grupcompra").val(cabecera.grupcompra);
            $("#almacen").val(cabecera.almacen).trigger('change');
           
            $("#cencod").val(cabecera.cencod).trigger('change');
           
            $("#stped").val(cabecera.estado_pedido);
            spread.setData(data.datos.detalles);
          }
        }
      })
    };

    $(document).ready(function () {
      $("#buscar_pedido").click(function () {
        let iDpedido = $("#id_pedido_buscar").val();
        if (iDpedido) {
          buscarPedido(iDpedido);
        }
      })
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
          url: 'api/guardarPedFlete.php',
          data: {
            proveedor: $("#proveedor").val(),
            moneda: $("#moneda").val(),
            puertcarga: $("#puertcarga").val(),
            incote: $("#incote").val(),
            clasflete: $("#clasflete").val(),
            documento: $("#documento").val(),
            codsocie: $("#codsocie").val(),
          /*  clase_documento: $("#clase_documento").val(),*/
            cod_almacen: $("#almacen").val(),
            cencod: $("#cencod").val(),
            id_pedido: $("#pedgen").val(),
            detalles: spread.getData()
          }, success: function (data) {
            spread.destroy();
            spread = jexcel(document.getElementById('datos'), opciones);
            Swal.fire({
              title: 'Exito!!',
              icon: 'success',
              html: `<h3>Pedido guardado. ID pedido <span style='color:red''>${data.id_pedido}</span></h3>`,
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
      $('#proveedor').select2({
        ajax: {
          url: '/api/obtenerProveedor.php',
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
        placeholder: "Buscar Proveedor",
        minimumInputLength: 3,
      });
      $('#clase_documento').select2({
        ajax: {
          url: '/api/obtenerClaseDocumento.php',
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
        placeholder: "Buscar Documento",
        minimumInputLength: 2,
      });

      var beforeChange = function (instance, cell, x, y, value) {
        //Todo: agregar validacion precio total
      }
      var changed = function (instance, cell, x, y, value) {
        let cellName = jexcel.getColumnNameFromId([x, y]);
        if (x == 0) {
          let pos = Number(x);
          
        }
      }

      var width = document.getElementById('table-container').offsetWidth;
      colWidths = [100, 150, 400, 200, 80];
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
        minDimensions: [5, 10],
        tableOverflow: true,
        tableWidth: `${width}px`,
        allowManualInsertColumn: false,
        columns: [
          { type: 'numeric' },
          
          {
            type: 'dropdown', source: contenedor
          },
          
          { type: 'text' },
          { type: 'text' },
          
          
          { type: 'text' },
          
          /*{
            type: 'dropdown', source: dimensiones
          },*/
        ],
        tableOverflow: true,
        colHeaders: ['Cant.Contenedor','Tip.Contenedor', 'Descripcion del Contenedor', 'Num.Contenedor', 'Observacion'],
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