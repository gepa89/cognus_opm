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

    .dropdown-menu {
      min-width: 60px;
      z-index: 99999999;

      /* Adjust as needed */
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
                <h3 class="panel-title">Consultar Pedidos Ofertas</h3>
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
                      <input class="form-control" id="fecha_desde" value="<?php echo $hoy; ?>" name="fecha_desde" type="date">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="buscar">Fecha Hasta:</label>
                      <input class="form-control" id="fecha_hasta" value="<?php echo $hoy; ?>" name="fecha_hasta" type="date">
                    </div>
                  </div>
                  
                 

                </div>
                <div class="row">
                 
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Situación:</label>
                      <select class="form-control" id="situacion" name="situacion[]" multiple="multiple">
                        <?php
                        $sql = "select * from situped WHERE siturefe in ('PD','AN','PR','CE')";
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
                      <input type="text" id="addPed" name="addPed" disabled="true" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>
                   <div class="col-sm-8">
                    <div class="form-group">
                      <label for="buscar">Proveedor</label>
                      <input type="text" id="addPro" name="addPro" disabled="true" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div> 
                  </div> 
                  <div class="row">
                     <div class="col-sm-8">
                        <div class="form-group">
                          <label for="buscar">Observation</label>
                   
                          <textarea id="addObs" name="addObs" class="form-control" maxlength="250" rows="4" cols="50"></textarea>

                        </div>
                      </div>
                 
                  <div style="clear:both;"></div><br /><br />

                  <div style="clear:both;"></div><br /><br />
                  <div class="col-lg4 col-sm-4">
                    <div class="input-group">
                      <div class="input-group">
                        <button type="button" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
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
          <table class="table table-hover table-striped table-bordered  table-condensed" style="width: 100% !important;" id="tblReg">
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

  <script id="form-solicitar-aprobacion" type="text/template">
    <form onsubmit="return submitFormGenerico(event);" id="form-generico" action="__url__">
    <input type="hidden" name="pedido" value="__pedido__" id="pedido" />
    <input type="hidden" name="almacen" value="__almacen__" id="almacen" />

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <p>Está seguro que desea Solicitar Aprobacion de la Oferta?. La acción no es reversible.</p>
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
  <script id="anular-descarga" type="text/template">
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
  <script id="cerrar-descarga" type="text/template">
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
      $("#clase_documento").multiselect({
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
        var table = $("#tblReg").DataTable({
          'retrieve': true
        });
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
            url: `/api/v1/obtener_ofertas_detalle.php`,
            data: {
              pedido: rowx.idoferta
            },
            dataType: 'json',
            success: function(json) {
              $.LoadingOverlay("hide");
              generar_tabla_detalles(rowx.pedido, json.detalles, row);
            },
            error: function(xhr, error, thrown) {
              $.LoadingOverlay("hide");
              console.log(xhr);
              console.log(error);
              console.log(thrown);
            }
          });

        }
      });
      $("#form_filtros").submit(function(e) {
        e.preventDefault();
        $.LoadingOverlay("show");
        datatable(obtenerURL());
      });
    });

    function submitFormGenerico() {
      event.preventDefault();
      let url = $("#form-generico").attr('action');
      let ajaxData = $("#form-generico").serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
      }, {});
      $(".btn-submit").prop('disabled', true);
      $("#loading-button").show();

      $.ajax({
        type: "POST",
        url: url,
        data: ajaxData,
        success: function(response) {
          $("#modal-generico").modal("hide");
          if (response.exito) {
            datatable(obtenerURL());
            iziToast.success({
              title: 'Exito!',
              backgroundColor: "#70a415",
              titleColor: "white",
              messageColor: "white",
              message: response.mensaje,
            });
          } else {
            iziToast.error({
              title: 'Error!',
              message: response.mensaje,
            });
          }

        },
        error: function(xhr, error, thrown) {
          console.log(xhr);
          console.log(error);
          console.log(thrown);
        },
        complete: function() {
          $(".btn-submit").prop('disabled', false);
          $("#loading-button").hide();
        }
      });
      return false;
    }


    function obtenerURL() {
      let url = '/api/v1/obtener_ofertas.php?';
      url += `pedido=${$("#pedido").val()}&`;
      url += `proveedor=${$("#proveedor").val()}&`;
      url += `fecha_desde=${$("#fecha_desde").val()}&`;
      url += `fecha_hasta=${$("#fecha_hasta").val()}&`;
    
      url += `clase_documento=${$("#clase_documento").val() ?? ""}&`;
      url += `situacion=${$("#situacion").val() ?? ""}&`;
      url += `almacen=${$("#codalma").val()}`;
      return url;
    }
    function addUsr(idoferta,nombre) {
        
      $("#addUsr .modal-title").empty().append('Añadir ');
      $('#modalData').modal('hide');
      
      $('#addUsr').modal('show');
      $('#addUsr input[name="addPed"]').val(idoferta); 
      $('#addUsr input[name="addPro"]').val(nombre); 
    }
    function generar_tabla_detalles(pedido, datos, row) {
      let cuerpo = ``;
      let fila = ``;
      let columna_detalles = [{
          data: 'posicion',
          title: 'Pos.',
          className: 'dt-body-center'
        }, {
          data: 'material',
          title: 'Material',
          className: 'dt-body-center'
        }, {
          data: 'matdesc',
          title: 'Descripcion',
          className: 'dt-body-center'
        }, {
          data: 'unimed',
          title: 'UM',
          className: 'dt-body-center'
        }, {
          data: 'canped',
          title: 'Cant.Pedido',
          className: 'dt-body-center'
        }, {
          data: 'precuni',
          title: 'Precio',
          className: 'dt-body-center'
        },
        {
          data: 'prectotal',
          title: 'Prec.Total',
          className: 'dt-body-center'
        },
        {
          data: 'cencod',
          title: 'Centro',
          className: 'dt-body-center'
        },
        {
          data: 'codalma',
          title: 'Almacen',
          className: 'dt-body-center'
        },
        {
          data: 'artgrup',
          title: 'Grup.Art.',
          className: 'dt-body-center'
        },
      ];
      row.child(`<table class="table table-bordered table-condensed" style="width:100%" id='tabla-${pedido}'></table>`).show();
      let detailTable = $(`#tabla-${pedido}`).DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
        },
        info: false,
        ordering: false,
        searching: false,
        data: datos,
        scrollX: true,
        columns: columna_detalles,
      });
    }

    function datatable(url) {
      $.LoadingOverlay("show");
      if ($.fn.dataTable.isDataTable('#tblReg')) {
        table.ajax.url(url).load();
        $.LoadingOverlay("hide");
      } else {
        table = $('#tblReg').DataTable({
          dom: '<"top"B<lfrtip>><"clear">',
          buttons: [],
          ajax: {
            type: "GET",
            url: obtenerURL(),
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
          scrollX: true,
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
              data: 'idoferta',
              title: "CM",
              className: 'dt-body-center'
            },
            {
              data: 'pedclase',
              title: "Sociedad.",
              className: 'dt-body-center',
            },
            {
              data: 'sociedad',
              title: "Cond.Pag.",
              className: 'dt-body-center'
            },
            {
              data: 'codmone',
              title: "Proveedor",
              className: 'dt-body-center'
            },
            {
              data: 'nombre',
              title: "Fec.CM",
              className: 'dt-body-center'
            },
            {
              data: 'fecped',
              title: "Fec.Pro.Emb.",
              className: 'dt-body-center'
            },
            {
              data: 'fecped',
              title: "Fec.Pro.Lleg.",
              className: 'dt-body-center'
            },
            {
              title: "Acciones",
              className: 'dt-body-center',
              mRender: function(data, type, row) {
                let solicitaaprob = `<li><a style="margin:0;padding:0" onclick="solicitarapro('${row.idoferta}')" href="javascript:void(0);">Sol.Aprobacion</a></li>`;
                let anularButton = `<li><a style="margin:0;padding:0"  onclick="anularPedido('${row.idoferta}')"  href="javascript:void(0);">Anular</a></li>`;
                let cerrarButton = `<li><a style="margin:0;padding:0"  onclick="cerrarPedido('${row.idoferta}')"  href="javascript:void(0);">Cerrar</a></li>`;
                let adjuntardocu = `<li><a style="margin:0;padding:0" onclick="adjuntardoc('${row.idoferta}')" href="javascript:void(0);">Adjuntar Documento</a></li>`;
                let modPedido = `<li><a style="margin:0;padding:0"  target="_blank" href="mod_ofertas.php"  href="javascript:void(0);">Modificar Oferta</a></li>`;
                let group = `<div class="btn-group">
                      <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-small">
                      ${solicitaaprob}
                      ${anularButton}
                      ${cerrarButton}
                      ${adjuntardocu}
                      ${modPedido}
                      </ul>
                    </div>`;

                if (row.estado == "AN" || row.estado == "CE") {
                  group = "";
                }
                return group;
              }
            },
               
            {
              title: "Observacion",
              className: 'dt-body-center',
              mRender: function (data, type, row) {
              let texto = `<a title="Add Observation">
                                            <span style="font-size:14px" onclick="addUsr('${row.idoferta}','${row.nombre}')" class="glyphicon glyphicon-edit"></span></a>
                                         <a title="View Observation">
                                            <span style="font-size:14px" onclick="swModal('${row.idoferta}')" class="glyphicon glyphicon-search"></span></a>`;
              return texto;
            }
            },     
            {
              data: 'fecped',
              title: "Moneda",
              className: 'dt-body-center'
            }, 
            {
              data: 'totalcm',
              title: '<span style="color: red;">Valor CM</span>',
              className: 'dt-body-center',
              render: function(data, type, row) {
                // Modifica el valor de la celda para que se muestre en rojo
                return '<span style="color: red;">' + data + '</span>';
                }
              },
            {
              data: 'totalpo',
              title: '<span style="color: red;">Valor PO</span>',
              className: 'dt-body-center',
              render: function(data, type, row) {
                // Modifica el valor de la celda para que se muestre en rojo
                return '<span style="color: red;">' + data + '</span>';
                }
              },      
            {
              title: "Estado",
              className: 'dt-body-center',
              mRender: function(data, type, row) {
                let label = "primary";
                switch (row.situped) {
                  case "PD":
                    label = "default"
                    break;
                  case "AN":
                    label = "warning"
                    break;
                  case "CE":
                    label = "success"
                    break;
                  case "DC":
                    label = "info"
                    break;  
                  case "PR":
                    label = "default"
                    break;
                  default:
                    break;
                }
                let span = `<span class='label label-${label}' style="color:white">${row.situped}</span>`;
                return span;
              }
            }
            
          ],
        });
      }
    }

    function generarModal(titulo, contenido) {

      $("#modal-generico .modal-title").empty().text(titulo);
      $("#modal-generico .modal-body").empty().html(contenido);
      $("#modal-generico").modal('show');

    }
    function swModal(idoferta) {
            let ped = idoferta; 
        //    console.log(ped);
           
            if (ped != '') {
                $.ajax({
                    type: 'POST',
                    url: 'requests/getObservacionesoferta.php',
                    data: {
                       
                        ped: idoferta
                        
                    }, success: function (data) {
                        var dt = JSON.parse(data);
                        if (!dt.err) {
                            var mytable = '<table class="table table-hover table-striped table-bordered  table-condensed"><thead>' + dt.hdr + '</thead><tbody>';

                            $.each(dt.cab, function (key, value) {
                                mytable += "<tr>";
                                $.each(value, function (k, v) {
                                    mytable += "<td>" + v + "</td>";
                                });
                                mytable += "</tr>";
                            });
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-body").empty().append(mytable);
                            $("#modalData .modal-dialog.modal-lg .modal-content .modal-title").empty().append(dt.tit);
                           
                            $('#modalData').modal('show');
                        } else {
                            alert(dt.msg);
                        }
                    }
                });
            } else {
                alert("Debe consultar un artículo.");
            }
            //                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
        }
    function solicitarapro(pedido) {
      let url = "/api/v1/guardar_solicitud_aprobacion.php?pedido=" + pedido;
      let contenido = $("#form-solicitar-aprobacion").html();
      contenido = contenido.replace('__pedido__', pedido);
      contenido = contenido.replace('__url__', url);

      generarModal(`Programar Descarga ${pedido}`, contenido);
    }
    
    function anularPedido(idoferta) {
      let url = "/api/v1/anular_oferta.php?idoferta=" + idoferta;
      let contenido = $("#anular-descarga").html();
      contenido = contenido.replace('__pedido__', idoferta);
      contenido = contenido.replace('__url__', url);

      generarModal(`Anular Descarga ${idoferta}`, contenido);
    }
    
    function adjuntardoc(docCompra) {
    // Llamar a la función para abrir el modal y pasar el docCompra
    openModal(docCompra);
}
    function cerrarPedido(idoferta) {
      let url = "/api/v1/cerrar_oferta.php";
      let contenido = $("#cerrar-descarga").html();
      contenido = contenido.replace('__pedido__', pedido);
      contenido = contenido.replace('__url__', url);

      generarModal(`Cerrar Descarga ${pedido}`, contenido);
    }

    async function asReception(ped, almacen) {
      await obtenerTerminalesOnOpenModal(almacen);
      $(".modal-title").empty().append('sdds pedido <br/><b>#' + ped + '</b>');
      $("#recepcion").val(ped);
      $("#codalmacen").val(almacen);
      $('#assignRec').modal('show');
    }

    function saveAdd() {
  var Ped = $("#addPed").val();  // Obtener el valor del campo "addPed"
  var Obs = $("#addObs").val();  // Obtener el valor del campo "addObs"

  // Realizar la petición AJAX independientemente de si "Ped" está vacío o no
  $.ajax({
    type: 'POST',
    url: 'requests/saveOfertasobse.php',  // URL del archivo PHP que maneja la solicitud
    data: {
      action: 'add',    // Acción a realizar en el servidor
        Ped: Ped,         // Pasar el valor de Ped (descripción)
        Obs: Obs,         // Pasar el valor de Obs (observación)
        table: 'obseofertas',  // Tabla de base de datos a la que se debe insertar
        fields: 'idoferta,observacion'  // Campos de la tabla a insertar
    },
    success: function (data) {
      var dt = JSON.parse(data);  // Parsear la respuesta JSON del servidor
      alert(dt.msg);  // Mostrar el mensaje retornado por el servidor
      if (dt.err == 0) {
        window.location = 'consulta_oferta.php';  // Redirigir si no hay errores
      }
    },
    error: function (request) {
      alert(request.responseJSON.error);  // Mostrar el error si la solicitud falla
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