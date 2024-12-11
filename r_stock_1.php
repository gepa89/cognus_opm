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
$pd = '';
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
  <style>
    /*            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }*/

    .label {
      color: #000;
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
                <h3 class="panel-title">Filtros</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Articulo:</label>
                      <input class="form-control" id="pedidos" placeholder="buscar" name="pedidos" />
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
                        <button type="button" id="sndBtn" onclick="ckFields()"
                          class="form-control btn btn-primary">Buscar</button>
                        <button type="button" disabled id="descargar_reporte"
                          class="form-control btn btn-success">Descargar
                          Reporte</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="row">
          <table class="table table-hover table-striped table-bordered dTableR" id="tblReg"
            style="font-size: 12px !important;">
            <thead>

              <tr>
                <th colspan="2">Artículo</th>
                <th colspan="4">Cantidades</th>
              </tr>
              <tr>
                <!--v.artrefer,-->
                <!--                                    v.artdesc,
                                    v.candispo,
                                    v.canmure,
                                    v.canpedven,
                                    v.cantransfe,
                                    v.candevol-->
                <th>Código</th>
                <th>Descripción</th>
                <th>Libre</th>
                <th>Muelle</th>
                <th>Ventas</th>
                <th>Transf.</th>
                <th>Almacen</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
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
  <div style="clear:both;"></div>
  <?php
  include 'modal_match.php';
  include 'sidebar.php';
  include 'js_in.php';
  ?>
  <script type="text/javascript">
    var table = null;
    var params = null;
    function asReception(ped) {
      $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b>');
      $("#recepcion").val(ped);
      $('#assignRec').modal('show');
    }
    function saveAssign() {
      var pedido = $("#recepcion").val();
      var terminal = $("#terminal").val();
      $.ajax({
        type: 'POST',
        url: 'requests/asignar_pedido_termial.php',
        data: {
          pedido: pedido,
          terminal: terminal,
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
        }
      });
    }
    function ckFields() {
      $("#descargar_reporte").removeAttr('disabled');
      var pedidos = $("#pedidos").val();
      var codalma = $("#codalma").val();
      let buscar = $("#pedidos").val();
      params = new URLSearchParams();
      params.set('codalma', codalma);
      params.set('pedido', buscar);
      url = 'requests/getStock.php' + '?' + params.toString();
      datatable(url);
    }
    function swOC(oc, pd) {
      $.ajax({
        type: 'POST',
        url: 'alertas_oc.php',
        data: {
          oc: oc,
          pd: pd
        }, success: function (data) {
          var dt = JSON.parse(data);
          $("#ocCont").empty().append(dt.cntn);
        }
      });
    }

    function datatable(url) {
      if ($.fn.dataTable.isDataTable('#tblReg')) {
        table.ajax.url(url).load();
        /*table = $('#tblReg').DataTable({
            retrieve: true,
            ajax: url,
            processing: true,
            serverSide: true
        }).clear().draw();*/
      }
      else {
        table = $('#tblReg').DataTable({
          dom: '<"top"B<lfrtip>><"clear">',
          buttons: [],
          ajax: 'requests/getStock.php',
          paging: true,
          processing: true,
          serverSide: true,
          searching: false,
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
          },
          columns: [
            { data: 'artrefer' },
            { data: 'artdesc', orderable: false },
            { data: 'candispo', orderable: false },
            { data: 'canmure' },
            { data: 'canpedven', orderable: false },
            { data: 'cantransfe' },
            { data: 'cod_alma' },
          ],
        });
      }
    }

    $(document).ready(function () {
      $('#repor1').addClass('active');
      $('#c9').click();
      $('#recep').addClass('active');
      datatable(null);
      $("#descargar_reporte").click(function (e) {
        location.href = 'requests/exportar_stock_articulo.php?' + params.toString();

      })

      $('#eprod').addClass('active');
      $('#c1').click();
      $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });
      $("#selCodExp").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
          if (options.length === 0) {
            return 'Ninguno';
          }
          else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });

      $("#selCto").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
          if (options.length === 0) {
            return 'Ninguno';
          }
          else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });
      $("#selSit").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonText: function (options) {
          if (options.length === 0) {
            return 'Ninguno';
          }
          else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });
    });
    function min_to_hour(min) {
      var zero2 = new Padder(2);
      var deci = min - Math.floor(min);
      deci = Math.floor(parseFloat(deci) * 10);
      if (deci >= 5) {
        var realmin = Math.ceil(min % 60);
      } else {
        var realmin = Math.floor(min % 60);
      }
      if (realmin > 59) {
        realmin = 0;
        var hour = Math.ceil(min / 60);
      } else {
        var hour = Math.floor(min / 60);
      }


      return zero2.pad(hour) + ":" + zero2.pad(realmin);
    }
    function Padder(len, pad) {
      if (len === undefined) {
        len = 1;
      } else if (pad === undefined) {
        pad = '0';
      }

      var pads = '';
      while (pads.length < len) {
        pads += pad;
      }

      this.pad = function (what) {
        var s = what.toString();
        return pads.substring(0, pads.length - s.length) + s;
      };
    }  
  </script>
</body>

</html>