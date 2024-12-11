<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("reportes")) {
  echo "No tiene acceso";
  exit;
}
$inicio = date("Y-m-01");
$fin = date("Y-m-d");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head_ds.php' ?>

<body class="full_width">
  <style>
    /*            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }*/
    div.dtsp-searchPane div.dataTables_scrollBody {
      height: 70px !important;
      width: 200px !important;
    }

    .dtsp-columns-1 {
      max-width: 24% !important;
      margin: 0px !important;
    }

    .label {
      color: #000;
    }
  </style>
  <div id="maincontainer" class="clearfix">
    <?php include 'header.php' ?>
    <div id="contentwrapper">
      <div class="main_content">
        <div class="row">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Filtros</h3>
            </div>
            <div class="panel-body">
              <div id="alerta-descarga" hidden class="alert alert-success">
              <img src="<?php echo "images/cargando1.gif"?>" width="20" alt="">
                <strong>Descargando datos </strong><br>Esta operación puede tomar unos minutos, por favor espere...
              </div>
              <form action="" method="get" id="form">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Fecha Inicio:</label>
                      <input class="form-control" required type="date" id="fecha_inicio" value="<?php echo $inicio ?>" name="fecha_inicio" />
                    </div>
                  </div>
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">Fecha Fin:</label>
                      <input class="form-control" required type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $fin ?>" />
                    </div>
                  </div>
                  <div class="col-md-3 ">
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
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">&nbsp;</label>
                      <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="submit" id="descargar_reporte" class="form-control btn btn-success">Descargar
                          Reporte</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>

            </div>
          </div>
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
  <script>
    $(document).ready(function() {
      // Attach a click event handler to the link with ID "myLink"
      $("#form").on("submit", function(event) {
        console.log("submit");
        // Prevent the default action of the click event (in this case, navigating to a URL)
        event.preventDefault();
        var formData = $(this).serialize();
        let params = new URLSearchParams(formData);
        let url = 'requests/exportar_productividad.php?' + params.toString();
        $("#descargar_reporte").prop('disabled', true);
        $("#alerta-descarga").prop('hidden', false);
        var request = new XMLHttpRequest();
        request.open('POST', url, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.responseType = 'blob';

        request.onload = function(e) {
          $("#descargar_reporte").prop('disabled', false);
          $("#alerta-descarga").prop('hidden', true);
          if (this.status === 200) {
            var filename = request.getResponseHeader("filename");
            var blob = this.response;
            if (window.navigator.msSaveOrOpenBlob) {
              window.navigator.msSaveBlob(blob, filename);
            } else {
              var downloadLink = window.document.createElement('a');
              var contentTypeHeader = request.getResponseHeader("Content-Type");
              downloadLink.href = window.URL.createObjectURL(new Blob([blob], {
                type: contentTypeHeader
              }));
              downloadLink.download = filename;
              document.body.appendChild(downloadLink);
              downloadLink.click();
              document.body.removeChild(downloadLink);

            }
          }
        };
        request.send();
      });

    })
  </script>
</body>

</html>