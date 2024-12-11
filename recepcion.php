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

        #tblReg tbody tr td:first-of-type {
            width: 100px;
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
                                <h3 class="panel-title">Pedido Proforma </h3>
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
                                            <label>Clase:</label>
                                            <select class="form-control" id="clase_documento" name="clase_documento[]" multiple="multiple">
                                                <?php
                                                $sq = "select * from clasdoc";
                                                $rs = $db->query($sq);
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['pedclase'] . '">' . $ax['pedclase'] . ' - ' . $ax['descripcion'] . '</option>';
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
                            
                        </div>

                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
     <div class="modal fade bd-example-modal-md" id="editUsr" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalle de Usuario</h4>
            </div>
            <div class="modal-body">
                <!-- Tabla dentro del modal -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Observacion</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo_tabla">

                        </tbody>
                </table>
                <!-- Botón para cerrar o realizar una acción adicional -->
                <div class="form-group text-center">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
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
                                <th>Ubicacion</th>
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
            <img id="loading-button" hidden src="/wmsdz/assets/img/loading.svg" width="20px" alt="">
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
            <img id="loading-button" hidden src="/wmsdz/assets/img/loading.svg" width="20px" alt="">
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
            <img id="loading-button" hidden src="/wmsdz/assets/img/loading.svg" width="20px" alt="">
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
                        url: `/wmsdz/api/v1/obtener_datos_recepcion_detalles.php`,
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
            let url = '/wmsdz/api/v1/obtener_datos_recepcion.php?';
            url += `pedido=${$("#pedido").val()}&`;
            url += `proveedor=${$("#proveedor").val()}&`;
            url += `fecha_desde=${$("#fecha_desde").val()}&`;
            url += `fecha_hasta=${$("#fecha_hasta").val()}&`;
            url += `clase_documento=${$("#clase_documento").val() ?? ""}&`;
            url += `situacion=${$("#situacion").val() ?? ""}&`;
            url += `almacen=${$("#codalma").val()}`;
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
                    className: 'dt-body-center'
                }, {
                    data: 'descripcion_material',
                    title: 'Descripcion',
                    className: 'dt-body-center'
                }, {
                    data: 'cantidad_pedido',
                    title: 'Cant.Pedido',
                    className: 'dt-body-center'
                }, {
                    data: 'cantidad_pendiente',
                    title: 'Cant.Ant.',
                    className: 'dt-body-center'
                }, {
                    data: 'prec_uni',
                    title: 'Prec.Unit.',
                    className: 'dt-body-center'
                },
                {
                    data: 'prec_tot',
                    title: 'Prec.Tot.',
                    className: 'dt-body-center'
                },
                {
                    data: 'multireferencia',
                    title: 'Multireferencia',
                    className: 'dt-body-center'
                },
                {
                    data: 'muelle',
                    title: 'Muelle',
                    className: 'dt-body-center'
                },
                {
                    data: 'usuario',
                    title: 'Usuario',
                    className: 'dt-body-center'
                },
                {
                    data: 'fecha',
                    title: 'Fecha',
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
                            data: 'clase_documento',
                            title: "Clase Doc.",
                            className: 'dt-body-center',
                        },
                        {
                            data: 'fecha_creacion',
                            title: "Fecha Creacion",
                            className: 'dt-body-center'
                        },
                        {
                            data: 'empresa',
                            title: "Empresa",
                            className: 'dt-body-center'
                        },
                        {
                            data: 'proveedor',
                            title: "Proveedor",
                            className: 'dt-body-center'
                        },
                        {
                            title: "Moneda",
                            data: "codmone",
                            className: 'dt-body-center'
                        },
                        {
                            title: "Importe Tot.",
                            data: "codmone",
                            className: 'dt-body-center'
                        },
                        {
                            title: "Estado",
                            className: 'dt-body-center',
                            mRender: function(data, type, row) {
                                let label = "primary";
                                switch (row.estado) {
                                    case "PD":
                                        label = "info"
                                        break;
                                    case "CE":
                                        label = "success"
                                        break;
                                    case "PP":
                                        label = "warning"
                                        break;
                                    default:
                                        break;
                                }
                                let span = `<span class='label label-${label}' style="color:white">${row.estado}</span>`;
                                return span;
                            }
                        },
                        
                        {
                            title: "Modificar",
                            className: 'dt-body-center',
                            mRender: function(data, type, row) {
                                let btnModificar = `<a title="Modificar Pedido" target="_blank" href="mod_pedidos.php"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>`;
                                let group = `<div class="btn-group" role="group">`;
                                if (row.puede_anular) {
                                    group += btnModificar;
                                }
                             //   $lk = '<a title="Modifiación de Ubicación" target="_blank" href="p_ref_ubic_upd.php?mat='."".$art."".'"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>';
                                group += "</div>";
                                return group;
                            }
                        },
                           
                        {
                            title: "Observaciones",
                            className: 'dt-body-center',
                            mRender: function(data, type, row) {
                                let btnObservar = `<a title="Ver Observaciones" <span style="font-size:14px" onclick="updDat('${row.pedido}')" class="glyphicon glyphicon-pencil"></span></a>`;
                               
                                let btnCrear = `<a title="Agregar Observacion" target="_blank" href="mod_pedidos.php"><span style="font-size:14px" class="glyphicon glyphicon-edit"></span></a>`;
                                let group = `<div class="btn-group" role="group">`;
                                if (row.estado=='PD') {
                                    group = "";
                                }
                                group += "</div>";
                                return group;
                            }
                        },
                        {
                            title: "Acciones",
                            className: 'dt-body-center',
                            mRender: function(data, type, row) {
                              let progrButton = `<li><a style="margin:0;padding:0" onclick="programarHorario('${row.pedido}')" href="javascript:void(0);">Sol.Aprobacion</a></li>`;
                              let anularButton = `<li><a style="margin:0;padding:0"  onclick="anularPedido('${row.pedido}')"  href="javascript:void(0);">Anular</a></li>`;
                              let cerrarButton = `<li><a style="margin:0;padding:0"  onclick="cerrarPedido('${row.pedido}')"  href="javascript:void(0);">Cerrar</a></li>`;
                              let group = `<div class="btn-group">
                                    <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Acciones <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-small">
                                    ${progrButton}
                                    ${cerrarButton}
                                    ${anularButton}
                                    </ul>
                                  </div>`;

                              if (row.estado == "AN" || row.estado == "CE") {
                                group = "";
                              }
                              return group;
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

    function programarHorario(pedido) {
      let url = "/wmsdz/api/v1/guardar_solicitud_aprobacion.php?pedido=" + pedido;
      let contenido = $("#form-solicitar-aprobacion").html();
      contenido = contenido.replace('__pedido__', pedido);
      contenido = contenido.replace('__url__', url);

      generarModal(`Programar Descarga ${pedido}`, contenido);
    }
    function updDat(addPue, addDes) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updPue").val(addPue);
      $("#updDes").val(addDes);
     
      
      //                $("#updAlm").val(addAlm).change();

      $('#editUsr').modal('show');
    }
    function anularPedido(pedido) {
      let url = "/wmsdz/api/v1/anular_oferta.php?pedido=" + pedido;
      let contenido = $("#anular-descarga").html();
      contenido = contenido.replace('__pedido__', pedido);
      contenido = contenido.replace('__url__', url);

      generarModal(`Anular Descarga ${pedido}`, contenido);
    }

    function cerrarPedido(pedido) {
      let url = "/wmsdz/api/v1/cerrar_oferta.php";
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
        async function asReception(ped, almacen) {
            await obtenerTerminalesOnOpenModal(almacen);
            $(".modal-title").empty().append('sdds pedido <br/><b>#' + ped + '</b>');
            $("#recepcion").val(ped);
            $("#codalmacen").val(almacen);
            $('#assignRec').modal('show');
        }

        function sendReception(pedido) {
            $.ajax({
                type: 'POST',
                url: 'requests/send_to_sap.php',
                data: {
                    pedido: pedido
                },
                success: function(data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    if (dt.error == 0) {
                        $('#sndBtn').click();
                    }
                }
            });
        }
        function programarHorario(pedido) {
            let url = "/wmsdz/api/obtenerPedidoDocumentos.php?id_pedido=" + pedido;
            
           

            
          }
        function saveAssign() {
            var pedido = $("#recepcion").val();
            var terminal = $("#terminal").val();
            let almacen = $("#codalmacen").val();
            if (!terminal) {
                alert("Almacen no valido");
                return;
            }
            data = {
                pedido: pedido,
                terminal: terminal,
                almacen: almacen
            };
            $.ajax({
                type: 'POST',
                url: 'requests/asignar_pedido_termial.php',
                data: data,
                success: function(data) {
                    var dt = JSON.parse(data);
                    alert(dt.msg);
                    $('#sndBtn').click();
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