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
              <h3 class="panel-title">Filtros</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">Fecha inicio:</label>
                    <input type="date" class="form-control" required name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d") ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cliente">Fecha fin:</label>
                    <input type="date" class="form-control" required name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d") ?>">
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
            <h3 class="panel-title">Demanda por dia por tipo de trabajo - SKU</h3>
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
            <h3 class="panel-title">Productividad por dia por tipo de trabajo - SKU</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_salida"></canvas>
              </div>

            </div>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Demanda por hora por tipo de trabajo - SKU</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_hora_entrega"></canvas>
              </div>

            </div>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Productividad por hora por tipo de trabajo - SKU</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_hora_salida"></canvas>
              </div>

            </div>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Productividad Por Colaborador - SKU</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col" style="width: auto;max-height:300px">
                <canvas id="chart_mes_colaborador"></canvas>
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
    var chart_datos_entrada = null;
    var chart_datos_salida = null;
    var chart_datos_hora_entrada = null;
    var chart_datos_hora_salida = null;
    var chart_datos_mes_usuario = null;

    $(document).ready(function() {

      $("#form_filtros").submit(function(e) {
        e.preventDefault();
        if (chart_datos_entrada !== null) {
          chart_datos_entrada.destroy();
          chart_datos_hora_entrada.destroy();
          chart_datos_hora_salida.destroy();
          chart_datos_salida.destroy();
          chart_datos_mes_usuario.destroy();
        }
        let formData = new FormData(document.getElementById("form_filtros"));
        const params = new URLSearchParams(formData);
        $("#image_loading").prop('hidden', false);
        $("#btn_filtro").prop('disabled', true);
        obtenerDatos(params.toString());
        obtenerDatosMesUsuario(params.toString());
        return false;
      })
    });

    async function obtenerDatos(parametros) {
      $.ajax({
        url: `/wmsd/api/v1/obtener_productividad.php?${parametros}`,
        type: "GET",
        dataType: "json",
        success: function(datos) {
          chart_datos_entrada = new Chart(document.getElementById('chart_entrada'), obtenerConfMovimientos(datos.entrada));
          chart_datos_salida = new Chart(document.getElementById('chart_salida'), obtenerConfMovimientos(datos.salida));
          chart_datos_hora_entrada = new Chart(document.getElementById('chart_hora_entrega'), obtenerConfHora(datos.entrada_horas));
          chart_datos_hora_salida = new Chart(document.getElementById('chart_hora_salida'), obtenerConfHora(datos.salida_horas));
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

    async function obtenerDatosMesUsuario(parametros) {
      $.ajax({
        url: `/wmsd/api/v1/obtener_productividad_usuario.php?${parametros}`,
        type: "GET",
        dataType: "json",
        success: function(datos) {
          const cfg = {
            plugins: [ChartDataLabels],
            options: {
              responsive: true,
              maintainAspectRatio: true,
              aspectRatio: 6,
              plugins: {
                datalabels: {

                  color: function(context) {
                    return context.dataset.borderColor;
                  },
                  font: {
                    weight: 'bold'
                  },
                  formatter: function(value, context) {
                    return context.dataset.data[context.dataIndex][context.dataset.parsing.yAxisKey];
                  },
                  align: 'end',
                  anchor: 'end'
                }
              }
            },
            type: 'bar',
            data: {
              datasets: [{
                label: 'Recepción',
                data: datos,
                parsing: {
                  yAxisKey: 'entradas',
                  xAxisKey: 'nombre'
                },
              }, {
                label: 'Expedición',
                data: datos,
                parsing: {
                  yAxisKey: 'salidas',
                  xAxisKey: 'nombre'
                },
              }, {
                label: 'Maquinista',
                data: datos,
                parsing: {
                  yAxisKey: 'maquinista',
                  xAxisKey: 'nombre'
                },
              }],

            },
          };
          chart_datos_mes_usuario = new Chart(document.getElementById('chart_mes_colaborador'), cfg);
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
      chart_datos_entrada = new Chart(document.getElementById('chart_entrada'), obtenerConfMovimientos(datos.entrada));
      char_datos_salida = new Chart(document.getElementById('chart_salida'), obtenerConfMovimientos(datos.salida));
      chart_datos_hora_entrada = new Chart(document.getElementById('chart_datos_hora_entrega'), obtenerConfHora(datos.entrada_horas));
      chart_datos_hora_salida = new Chart(document.getElementById('chart_datos_hora_salida'), obtenerConfHora(datos.salida_horas));
    }

    function obtenerNombresFecha(date) {

      date.setDate(date.getDate() + 1);

      // Define the Spanish locale
      const locale = 'es-ES';

      // Get the PC's timezone
      const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      // Get the day and month in the desired format
      const day = String(date.getDate()).padStart(2, '0');
      const month = new Intl.DateTimeFormat(locale, {
        month: 'short',
        timeZone
      }).format(date);

      // Construct the formatted date string
      return `${day}-${month}`;
    }

    function convertirNombresFecha(datos) {
      for (let index = 0; index < datos.length; index++) {
        let element = datos[index];
        element.fecha = obtenerNombresFecha(new Date(element.fecha));
      }
      return datos;
    }

    function manejarResultados($datos) {

    }

    function obtenerConfMovimientos(datos) {
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
            label: 'Recepción',
            data: datos,
            parsing: {
              yAxisKey: 'entrada',
              xAxisKey: 'fecha'
            },
          }, {
            label: 'Expedición',
            data: datos,
            parsing: {
              yAxisKey: 'salida',
              xAxisKey: 'fecha'
            }
          }, {
            label: 'Maquinista',
            data: datos,
            parsing: {
              yAxisKey: 'maquinista',
              xAxisKey: 'fecha'
            }
          }],

        },
      };
    }

    function obtenerConfHora(datos) {
      return cfg = {
        plugins: [ChartDataLabels],
        options: {
          responsive: true,
          maintainAspectRatio: true,
          aspectRatio: 6,
          plugins: {
            datalabels: {

              color: function(context) {
                return context.dataset.borderColor;
              },
              font: {
                weight: 'bold'
              },
              formatter: function(value, context) {
                return context.dataset.data[context.dataIndex][context.dataset.parsing.yAxisKey];
              },
              align: 'end',
              anchor: 'end'
            }
          }
        },
        type: 'line',
        data: {
          labels: datos.map(row => row.hora),
          datasets: [{
            label: 'Recepción',
            data: datos,
            parsing: {
              yAxisKey: 'entrada',
              xAxisKey: 'hora'
            },
          }, {
            label: 'Expedición',
            data: datos,
            parsing: {
              yAxisKey: 'salida',
              xAxisKey: 'hora'
            },
          }, {
            label: 'Maquinista',
            data: datos,
            parsing: {
              yAxisKey: 'maquinista',
              xAxisKey: 'hora'
            },
          }],

        },
      };
    }
  </script>
</body>

</html>