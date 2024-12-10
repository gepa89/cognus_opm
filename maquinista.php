<?php
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("maquinista")) {
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
                      <input class="form-control" id="pedido" name="pedido" />
                    </div>
                  </div>
                  <div class="col-md-3">
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
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Fecha Desde:</label>
                      <input class="form-control" id="fecha_desde" value="<?php echo $hoy; ?>" name="fecha_desde" type="date">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Fecha Hasta:</label>
                      <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                    </div>
                  </div>


                </div>
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Clase:</label>

                      <select class="form-control" id="clase_documento" name="clase_documento[]" multiple="multiple">
                        <?php
                        $sql = "select distinct pedclase from pedubicab where pedclase <> null or pedclase <> ''";
                        $rs = $db->query($sql);
                        while ($ax = $rs->fetch_assoc()) {
                          echo '<option value="' . $ax['pedclase'] . '">' . $ax['pedclase'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Situación:</label>
                      <select class="form-control" id="situacion" name="situacion[]" multiple="multiple">
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
                  <div class="col-md-2">

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
          <table class="table table-hover table-striped table-bordered  table-condensed" id="tblReg">
          </table>
        </div>
        <!-- Modal -->
        <div class="modal fade bd-example-modal-sm" id="assignRec" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
                      <input type="text" readonly id="selected_almacen" class="form-control" value="" />
                      <select class="form-control" id="terminal" name="terminal">
                        <?php
                        $sq = "select * from termi where tipac <> 'RECE'";
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
  <div class="modal fade bd-example-modal-lg" id="modal_sin_ubicacion" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
  <div style="clear:both;"></div>
  <?php
  include 'modal_match.php';
  include 'sidebar.php';
  include 'js_in.php';
  ?>
  <script type="text/javascript">
    var table = null;
    var delay_function = null;
    $(document).ready(function() {
      $('#search').on('keyup', function() {
        if (delay_function !== null) {
          clearTimeout(delay_function);
        }
        delay_function = setTimeout(function() {
          table.search($('#search').val()).draw();
        }, 500);
      });
      $("#clase_documento,#selCod").multiselect({
        selectAllText: 'Todos',
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonWidth: '100%',
        enableCaseInsensitiveFiltering: true,
        buttonText: function(options) {
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
        buttonText: function(options) {
          if (options.length === 0) {
            return 'Ninguno';
          } else if (options.length > 0) {
            return options.length + ' selecionado(s)';
          }
        }
      });
      datatable(null);
      $('#tblReg tbody').on('click', '.details-control', function() {
        var table = $("#tblReg").DataTable();
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
            url: `/wmsd/api/v1/obtener_datos_maquinista_detalles.php`,
            data: {
              pedido: rowx.pedido
            },
            dataType: 'json',
            success: function(json) {
              $.LoadingOverlay("hide");
              generar_tabla_detalles(rowx.pedido, json.detalles, row);
            },
            error: function(xhr, error, thrown) {
              $.LoadingOverlay("hide");
            }
          });

        }
      });
      $("#form_filtros").submit(function(e) {
        e.preventDefault();
        datatable(obtenerUrl());
      });
    });

    function obtenerUrl() {
      let url = '/wmsd/api/v1/obtener_datos_maquinista.php?';
      url += `pedido=${$("#pedido").val()}&`;
      url += `proveedor=${$("#proveedor").val()}&`;
      url += `fecha_desde=${$("#fecha_desde").val()}&`;
      url += `fecha_hasta=${$("#fecha_hasta").val()}&`;
      url += `clase_documento=${$("#clase_documento").val() ?? ""}&`;
      url += `situacion=${$("#situacion").val() ?? ""}&`;
      url += `almacen=${$("#codalma").val() ?? ""}&`;
      return url;
    }

    function generar_tabla_detalles(pedido, datos, row) {
      let cuerpo = ``;
      let fila = ``;
      let columna_detalles = [{
          data: 'posicion',
          title: 'Posicion',
          className: 'dt-body-center'
        }, {
          data: 'material',
          title: 'Articulo',
          className: 'dt-body-center',
        }, {
          data: 'descripcion_material',
          title: 'Descripcion',
          className: 'dt-body-center'
        }, {
          data: 'cantidad_pedido',
          title: 'Cant. Pedido',
          className: 'dt-body-center'
        }, {
          data: 'cantidad_ubicada',
          title: 'Cant. Ubicada',
          className: 'dt-body-center'
        }, {
          data: 'cantidad_pendiente',
          title: 'Cant. Pendiente',
          className: 'dt-body-center'
        },
        {
          data: 'usuario',
          title: 'Usuario',
          className: 'dt-body-center'
        },
        {
          data: 'fecha_y_hora',
          title: 'Fecha',
          className: 'dt-body-center'
        },
      ];
      row.child(`<table class='table table-bordered table-stripped' id='tabla-${pedido}'></table>`).show();
      let detailTable = $(`#tabla-${pedido}`).DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
        },
        info: false,
        ordering: false,
        searching: false,
        data: datos,
        columns: columna_detalles,
      });
    }

    function datatable(url) {
      let url_defecto = obtenerUrl();
      if ($.fn.dataTable.isDataTable('#tblReg')) {
        table.ajax.url(url).load();
      } else {
        table = $('#tblReg').DataTable({
          dom: '<"top"B<lfrtip>><"clear">',
          buttons: [],
          ajax: {
            type: "GET",
            url: url_defecto,
            dataSrc: function(json) {
              $.LoadingOverlay("hide");
              return json.data;
            },
            error: function(xhr, error, thrown) {
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
              data: 'pedido',
              title: "Pedido",
              className: 'dt-body-center'
            },
            {
              data: 'referencia',
              title: "OC pedido",
              className: 'dt-body-center'
            },
            {
              data: 'fecha_creacion',
              title: "Fecha Creacion",
              className: 'dt-body-center'
            },
            {
              data: 'clase_documento',
              title: "C. Documento",
              className: 'dt-body-center'
            },
            {
               title: "Estado",
                        className: 'dt-body-center',
                        mRender: function(data, type, row) {
                            let label = "primary";
                            switch (row.situacion) {
                                case "PD":
                                    label = "info"
                                    break;
                                case "CE":
                                    label = "success"
                                    break;
                                case "AN":
                                    label = "danger"
                                    break;    
                                case "PP":
                                    label = "warning"
                                    break;
                                default:
                                    break;
                            }
                            let span = `<span class='label label-${label}' style="color:white">${row.situacion}</span>`;
                            return span;
                        }
              },
            
            {
              title: "Terminal",
              className: 'dt-body-center',
              mRender: function(data, type, row) {
                if (row.terminal_asignada) {
                  return row.terminal_asignada;
                }

                let group = `<div class="btn-group" role="group">`;


                group += "</div>";
                return group;
              }
            },
            {
              title: "Enviar SAP",
              className: 'dt-body-center',
              mRender: function(data, type, row) {
                let group = `<div>`;
                if (row.enviado == 1) {
                  group += "<span>Enviado</span>";
                }
                group += "</div>";
                return group;
              }
            },
            {
              title: "Acciones",
              className: 'dt-body-center',
              mRender: function(data, type, row) {
                let buttons = "";
                if (row.enviar_sap == true) {
                  buttons = `<li><a style="margin:0;padding:0" href=\"javascript:void(0);\"
                  onclick=\"sendExpedition('${row.referencia}','${row.pedido}')\" >Enviar</a></li>`;
                }
                if (row.anular_wms) {
                  buttons += `<li><a style="margin:0;padding:0" href=\"javascript:void(0);\"
                  onclick=\"nullDoc('${row.pedido}')\" >Anular</a></li>`;
                }
                if (row.terminal_asignada == "") {
                  buttons += `<li><a style="margin:0;padding:0" href=\"javascript:void(0);\"
                  onclick=\"asReceptionRe('${row.pedido}','REPO','${row.almacen}')\" >Asignar Terminal</a></li>`;
                }
                if (buttons == "") {
                  return "";
                }
                let group = `<div class="btn-group">
                      <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-small">
                      ${buttons}
                      </ul>
                    </div>`;
                return group;
              }
            }
          ],
        });
        table.on('preDraw', function() {
          console.log("presd");
          $.LoadingOverlay("show");
          // Add your custom code here to execute before the table is drawn
        });
        table.on('draw', function() {
          console.log("after");
          $.LoadingOverlay("hide");
          // Add your custom code here to execute before the table is drawn
        });
      }
    }

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
        },
        success: function(data) {
          $("#terminal").empty().append(data);
          $('#assignRec').modal('show');
        }
      });

    }

    function asReceptionRe(ped, tipo, almacen) {
      zon = "todos";
      $(".modal-title").empty().append('Asignar pedido <br/><b>#' + ped + '</b> en Zona <b>' + zon + '</b>');
      $("#recepcion").val(ped);
      $.ajax({
        type: 'POST',
        url: 'requests/get_terminal_zone.php',
        data: {
          zon: zon,
          tip: tipo,
          alma: almacen
        },
        success: function(data) {
          $("#terminal").empty().append(data);
          $("#selected_almacen").val(almacen);
          $('#assignRec').modal('show');
        }
      });

    }

    function saveAssign() {
      var pedido = $("#recepcion").val();
      var terminal = $("#terminal").val();
      var codalma = $("#selected_almacen").val();
      $.ajax({
        type: 'POST',
        url: 'requests/asignar_pedido_termial.php',
        data: {
          pedido: pedido,
          terminal: terminal,
          almacen: codalma
        },
        success: function(data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          $("#sndBtn").click();
        }
      });
    }

    function sendExpedition(pedido, pedubicod) {
      $.ajax({
        type: 'POST',
        url: 'requests/send_to_sap.php',
        data: {
          pedido: pedido,
          pedubicod: pedubicod
        },
        success: function(data) {
          var dt = data;
          alert(dt.msg);
          if (dt.error == 0) {
            $("#sndBtn").click();
          }
        },
        error: function(data) {
          console.log(data);
          var dt = JSON.parse(data);
          alert(dt.msg);

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
        success: function(data) {
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