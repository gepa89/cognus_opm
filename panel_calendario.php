<?php

require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT cod_dim as id, dimension as name FROM medpallet";
$query = $db->query($sql);
$medidas = $query->fetch_all(MYSQLI_ASSOC);

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT preseref as id, presedes as name FROM presen";
$query = $db->query($sql);
$presmedi = $query->fetch_all(MYSQLI_ASSOC);
$fecha_inicio= date("Y-m-d");
$fecha_fin = new DateTime();
$fecha_fin = $fecha_fin->add(new DateInterval('P8D'))->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head_ds.php' ?>

<body class="full_width">
  <div id="maincontainer" class="clearfix">
    <?php include 'header.php' ?>
    <div id="contentwrapper">
      <div class="main_content">
        <form id="form_filtros" method="get">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Calendario de Descargas</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">Fecha inicio:</label>
                    <input type="date" class="form-control" required name="fecha_inicio" id="fecha_inicio" value="<?php echo $fecha_inicio ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">Fecha fin:</label>
                    <input type="date" class="form-control" required name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">&nbsp;</label>
                    <button id="btn_filtro" type="submit" class="btn btn-primary"><span>Filtrar</button>
                    <img id="image_loading" hidden width="40" src="/wmsd/img/1488.gif" alt=""></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Total Descargas Por dia</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_entrada"></canvas>
              </div>

            </div>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Total Descargas realizadas Por Dia</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_ocs_cerradas"></canvas>
              </div>

            </div>
          </div>
        </div>

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

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <script>
    var chart_ocs_totales = null;
    var chart_ocs_cerrados = null;

    $(document).ready(function() {

      $("#form_filtros").submit(function(e) {
        e.preventDefault();
        if (chart_ocs_totales !== null) {
          chart_ocs_totales.destroy();
          chart_ocs_cerrados.destroy();
        }
        let formData = new FormData(document.getElementById("form_filtros"));
        const params = new URLSearchParams(formData);
        $("#image_loading").prop('hidden', false);
        $("#btn_filtro").prop('disabled', true);
        obtenerDatos(params.toString());
        return false;
      })
    });

    async function obtenerDatos(parametros) {
      $.ajax({
        url: `/wmsd/api/v1/obtener_descarga.php?${parametros}`,
        type: "GET",
        dataType: "json",
        success: function(datos) {
          chart_ocs_totales = new Chart(document.getElementById('chart_entrada'), obtenerConfMovimientos(datos.totales_a_preparar, "Tot. Descargas", ""));
          chart_ocs_cerrados = new Chart(document.getElementById('chart_ocs_cerradas'), obtenerConfMovimientos(datos.totales_cerrados, "Tot. Cerradas", ""));
        },
        error: function(error) {
          console.error("Error:", error);
        },
        complete: function() {
          $("#image_loading").prop('hidden', true);
          $("#btn_filtro").prop('disabled', false);
        }
      });
    }



    function manejarResultados(datos) {
      chart_ocs_totales = new Chart(document.getElementById('chart_entrada'), obtenerConfMovimientos(datos.entrada));
      chart_ocs_cerrados = new Chart(document.getElementById('chart_ocs_cerradas'), obtenerConfMovimientos(datos.cerrados));

    }

    //todo: cambiar en demas plataformas
    function obtenerNombresFecha(date) {
      fecha = new Date(date);
      fecha.setDate(fecha.getDate() + 1);

      // Define the Spanish locale
      const locale = 'es-ES';

      // Get the PC's timezone
      const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      // Get the day and month in the desired format
      const day = String(fecha.getDate()).padStart(2, '0');
      const month = fecha.toLocaleString('es-ES', {
        month: 'short'
      });

      // Construct the formatted date string
      return `${day}-${month}`;
    }

    function convertirNombresFecha(datos) {
      for (let index = 0; index < datos.length; index++) {
        let element = datos[index];
        element.fecha = obtenerNombresFecha(element.fecha);
      }
      return datos;
    }

    function manejarResultados($datos) {

    }

    function obtenerConfMovimientos(datos, label, color) {
      datos = convertirNombresFecha(datos);
      return cfg = {
        plugins: [ChartDataLabels],
        options: {
          responsive: true,
          maintainAspectRatio: true,
          aspectRatio: 6,
          plugins: {
            datalabels: {
              formatter: function(value, context) {
                return context.dataset.data[context.dataIndex][context.dataset.parsing.yAxisKey];
              },

              align: 'end',
              anchor: 'end',
              color: function(context) {
                return context.dataset.borderColor;
              },
              font: {
                weight: 'bold'
              }
            }
          }
        },
        type: 'bar',
        data: {
          labels: datos.map(row => row.fecha),

          datasets: [{
            label: "OCS",
            data: datos,
            borderColor: color,
            backgroundColor: color,
            parsing: {
              yAxisKey: 'total_ocs',
              xAxisKey: 'fecha'
            }
          }, {
            label: 'CONTENEDORES',
            data: datos,
            parsing: {
              yAxisKey: 'total_contenedor',
              xAxisKey: 'fecha'
            }
          }, {
            label: 'BULTOS',
            data: datos,
            parsing: {
              yAxisKey: 'total_bultos',
              xAxisKey: 'fecha'
            }
          }],

        },
      };
    }
  </script>
</body>

</html>