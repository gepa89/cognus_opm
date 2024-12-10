<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("expedicion")) {
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
          <form id="form_filtros" method="get">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h3 class="panel-title">Filtros</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Pedido:</label>
                      <input class="form-control" id="articulo" name="articulo" />
                    </div>
                  </div>
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">Articulo</label>
                      <input class="form-control" id="ubicacion" name="ubicacion" />
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
                  <div class="col-lg-12 col-sm-12"><br />
                                <div class="col-lg-5 col-sm-5">
                                    <label class="label">Fecha Creación:</label>
                                    <div style="clear:both;"></div><br />
                                    <div class="input-group input-daterange">
                                        <input autocomplete="off" type="text" name="dFecCre" id="dFecCre"
                                            class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar"
                                            value="<?php echo (@$_POST["dFecCre"]) ? @$_POST["dFecCre"] : date("d-m-Y", strtotime("now")); ?>" />
                                        <div class="input-group-addon"> hasta </div>
                                        <input autocomplete="off" type="text" name="hFecCre" id="hFecCre"
                                            class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar"
                                            value="<?php echo (@$_POST["hFecCre"]) ? @$_POST["hFecCre"] : date("d-m-Y", strtotime("now")); ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Clase:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selCto" name="selCto[]" multiple="multiple">

                                            <?php
                                            $sql = "select distinct pedclase from pedexcab where pedclase <> null or pedclase <> ''";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['pedclase'] . '">' . $ax['pedclase'] . '</option>';
                                            }
                                            ?>
                                            <!--                                                <option value="EUB">EUB</option>
                                                <option value="ZUB">ZUB</option>
                                                <option value="ZCL">ZCL</option>
                                                <option value="ZNB">ZNB</option>
                                                <option value="ZCON">ZCON</option>
                                                <option value="ZCRE">ZCRE</option>
                                                <option value="ZCOS">ZCOS</option>
                                                <option value="REPO">REPO</option>-->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Situación:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selSit" name="selSit[]" multiple="multiple">
                                            <?php
                                            $sql = "select * from situped";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['siturefe'] . '">' . $ax['siturefe'] . ' - ' . $ax['situdes'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        <label class="label">Cod. Envío:</label>
                                        <div style="clear:both;"></div><br />
                                        <select class="form-control" id="selCod" name="selCod[]" multiple="multiple">
                                            <?php
                                            $sql = "select distinct codenv from pedexcab where codenv <> null or codenv <> ''";
                                            $rs = $db->query($sql);
                                            while ($ax = $rs->fetch_assoc()) {
                                                echo '<option value="' . $ax['codenv'] . '">' . $ax['codenv'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
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
                    <?php

                    echo '<table class="table">'
                        . '<thead>'
                        . '<tr>'
                        . '<th colspan="3"></th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <!-- table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg" style="font-size: 12px !important;">
                       -->
                    <table width="100%" class="table table-hover table-striped table-bordered  table-condensed"
                        id="tblReg" style="font-size: 12px !important;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha Creacion</th>
                                <th>Hora Creacion</th>
                                <th>fecha recepcion</th>
                                <th>Clas.Doc.</th>
                                <th>Cod. Envío</th>
                                <th>Sit.Ped</th>
                                <th>Ubicación</th>
                                <th>Multiref.</th>
                                <th>Factura.</th>
                                <th>Alma</th>
                                
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
    function asReception(ped, zon, alma, tipo) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b> En <b>' + alma + '</b>');
            $("#recepcion").val(ped);
            $.ajax({
                type: 'POST',
                url: 'requests/get_terminal_zone.php',
                data: {
                    zon: zon,
                    tip: tipo,
                    alma: alma
                }, success: function (data) {
                    $("#terminal").empty().append(data);
                    $("#almacen").val(alma);
                    $('#assignRec').modal('show');
                }
            });

        }
    function asReceptionRe(ped, tipo, zon, $almacen) {
            $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b>');
            $("#recepcion").val(ped);
            $.ajax({
                type: 'POST',
                url: 'requests/get_terminal_zone.php',
                data: {
                    zon: zon,
                    tip: tipo,
                    alma: $almacen
                }, success: function (data) {
                    $("#terminal").empty().append(data);
                    $("#almacen").val($almacen);
                    $('#assignRec').modal('show');
                }
            });

        }
    function saveAssign() {
            var pedido = $("#recepcion").val();
            var terminal = $("#terminal").val();
            var codalma = $("#almacen").val();
            $.ajax({
                type: 'POST',
                url: 'requests/asignar_pedido_termial.php',
                data: {
                    pedido: pedido,
                    terminal: terminal,
                    almacen: codalma
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    $("#sndBtn").click();
                }
            });
        }    
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
          terminal: terminal
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
        }
      });
    }
    function ckFields() {
      $("#descargar_reporte").removeAttr('disabled');
      var ubicaciones = $("#ubicacion").val();
      var articulo = $("#articulo").val();
      var codalma = $("#codalma").val();
      params = new URLSearchParams();
      params.set('codalma', codalma);
      params.set('articulo', articulo);
      params.set('ubicaciones', ubicaciones);
      let url = 'requests/getExpe.php' + '?' + params.toString();
      datatable(url);
    }
    function sendExpedition(pedido) {
            $.ajax({
                type: 'POST',
                url: 'requests/send_to_sap_ex.php',
                data: {
                    pedido: pedido
                }, success: function (data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.error == 0) {
                        $("#sndBtn").click();
                    }
                }
            });
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
      }
      else {
        table = $('#tblReg').DataTable({
          dom: '<"top"B<lfrtip>><"clear">',
          buttons: [],
          ajax: 'requests/getExpe.php',
          paging: true,
          processing: true,
          serverSide: true,
          searching: false,
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
          },
          columns: [
            { data: 'pedexentre' },
            { data: 'clinom', orderable: false },
            { data: 'pedexfec' },
            { data: 'pedexhor' },
            { data: 'pedexfec' },
            { data: 'pedclase' },
            { data: 'codenv' },
            { data: 'siturefe' },
            { data: 'pedexentre' },
            { data: 'pedexentre' },
            { data: 'pedexentre' },
            { data: 'almcod' },
            
          ],
        });
      }
    }

    $(document).ready(function () {
      $('#repor2').addClass('active');
      $('#c9').click();
      datatable(null);
      $("#descargar_reporte").click(function (e) {
        location.href = 'requests/exportar_stock_ubicacion.php?' + params.toString();

      })

      $('#recep').addClass('active');
      //                table = $("#tblReg").DataTable({
      //                        "processing": true,
      //                    "bFilter": true,
      //                    dom: '<"top"B<lfrtip>><"clear">',
      //                    buttons: [
      //                         'excel'
      //                    ],
      //                    columnDefs: [ {
      //                        className: 'details-control',
      //                        orderable: false,
      //                        targets:   0
      //                    } ],
      //                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
      //                    "language": {
      //                        "processing":     "Procesando...",
      //                        "lengthMenu":     "Mostrar _MENU_ registros",
      //                        "zeroRecords":    "No se encontraron resultados",
      //                        "emptyTable":     "Ningún dato disponible en esta tabla",
      //                        "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
      //                        "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
      //                        "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
      //                        "infoPostFix":    "",
      //                        "search":         "Buscar:",
      //                        "Url":            "",
      //                        "infoThousands":  ",",
      //                        "loadingRecords": "Cargando...",
      //                        "paginate": {
      //                            "first":    "Primero",
      //                            "last":     "Último",
      //                            "next":     "Siguiente",
      //                            "previous": "Anterior"
      //                        },
      //                        "aria": {
      //                            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
      //                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
      //                        }
      //                    }});


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
      $("#selCod").multiselect({
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