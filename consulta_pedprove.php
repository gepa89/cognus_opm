<?php
require('conect.php');
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

<?php include 'head_ds.php'; ?>
<link rel="stylesheet" href="css/custom-datatable.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.11.0/dist/jspreadsheet.css">

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
        <?php include 'header.php'; ?>

        <div id="contentwrapper">
            <div class="main_content">
                <!-- Formulario de filtros -->
                <div class="row">
                    <form id="form_filtros" method="get">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">Consultar Pedidos de Compras</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pedido">Pedido:</label>
                                            <input class="form-control" id="pedido" name="pedido" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="proveedor">Proveedor:</label>
                                            <input class="form-control" id="proveedor" name="proveedor" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="codalma">Almacen:</label>
                                            <select id="codalma" class="form-control">
                                                <option value="">Seleccionar</option>
                                                <?php
                                                $sq = "SELECT * FROM alma";
                                                $rs = $db->query($sq);
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . utf8_encode($ax['almdes']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_desde">Fecha Desde:</label>
                                            <input class="form-control" id="fecha_desde" name="fecha_desde" type="date" value="<?php echo $hoy; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_hasta">Fecha Hasta:</label>
                                            <input class="form-control" id="fecha_hasta" name="fecha_hasta" type="date" value="<?php echo $hoy; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="situacion">Situación:</label>
                                            <select class="form-control" id="situacion" name="situacion[]" multiple="multiple">
                                                <?php
                                                $sql = "SELECT * FROM situped WHERE siturefe IN ('PD', 'AN', 'PR', 'CE', 'DC', 'PL')";
                                                $rs = $db->query($sql);
                                                while ($ax = $rs->fetch_assoc()) {
                                                    echo '<option value="' . $ax['siturefe'] . '">' . $ax['siturefe'] . ' - ' . $ax['situdes'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buscar">&nbsp;</label>
                                            <button type="submit" id="sndBtn" class="form-control btn btn-primary">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Componente de Spreadsheet -->
                <div class="row">
                    <div id="spreadsheet" style="margin: 20px;"></div>
                    <button class="btn btn-success" onclick="guardarSpreadsheet()">Guardar Datos</button>
                </div>

                <!-- Tabla de resultados -->
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4" style="float: right;">
                        <div class="input-group">
                            <span class="input-group-addon">Buscar:</span>
                            <input type="search" class="form-control" id="search" placeholder="Buscar">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-hover table-striped table-bordered table-condensed" style="width: 100% !important;" id="tblReg">
                    </table>
                </div>

                <!-- Modales -->
                <?php include 'modales.php'; ?>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <?php include 'js_in.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.11.0/dist/jspreadsheet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsuites@4.11.0/dist/jsuites.js"></script>

    <script type="text/javascript">
        var table = null;
        var spreadsheet = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar el spreadsheet
            spreadsheet = jspreadsheet(document.getElementById('spreadsheet'), {
                data: [],
                columns: [
                    { type: 'text', title: 'Tipo Contenedor', width: 150 },
                    { type: 'numeric', title: 'Cantidad', width: 100 },
                    { type: 'text', title: 'Número Contenedor', width: 150 },
                    { type: 'text', title: 'Observación', width: 300 }
                ],
                minDimensions: [4, 10],
                allowInsertRow: true,
                allowDeleteRow: true
            });
        });

        function guardarSpreadsheet() {
            const data = spreadsheet.getData();
            const filteredData = data.filter(row => row.some(cell => cell !== null && cell !== ''));

            if (filteredData.length === 0) {
                alert('No hay datos para guardar.');
                return;
            }

            fetch('/api/v1/guardar_spreadsheet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ datos: filteredData })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Datos guardados correctamente.');
                    } else {
                        alert('Error al guardar los datos.');
                    }
                })
                .catch(error => {
                    console.error('Error al guardar los datos:', error);
                });
        }

        function obtenerURL() {
            let url = '/api/v1/obtener_pedprove.php?';
            url += `pedido=${$("#pedido").val()}&`;
            url += `proveedor=${$("#proveedor").val()}&`;
            url += `fecha_desde=${$("#fecha_desde").val()}&`;
            url += `fecha_hasta=${$("#fecha_hasta").val()}&`;
            url += `situacion=${$("#situacion").val() ?? ""}&`;
            url += `almacen=${$("#codalma").val()}`;
            return url;
        }

        function datatable(url) {
            if ($.fn.dataTable.isDataTable('#tblReg')) {
                table.ajax.url(url).load();
            } else {
                table = $('#tblReg').DataTable({
                    ajax: obtenerURL(),
                    columns: [
                        { data: 'docompra', title: 'Pedido', className: 'dt-body-center' },
                        { data: 'clasdoc', title: 'Clas.Doc.', className: 'dt-body-center' },
                        { data: 'codsocie', title: 'Sociedad', className: 'dt-body-center' },
                        { data: 'nombre', title: 'Proveedor', className: 'dt-body-center' },
                        { data: 'fecre', title: 'Fec.Pedido', className: 'dt-body-center' },
                        { data: 'orgcompra', title: 'Org.Compra', className: 'dt-body-center' },
                        { data: 'grupcompra', title: 'Grup.Compra.', className: 'dt-body-center' },
                        { data: 'codmone', title: 'Moneda', className: 'dt-body-center' },
                        {
                            data: 'totalp',
                            title: '<span style="color: red;">Valor Pedido</span>',
                            className: 'dt-body-center',
                            render: function (data, type, row) {
                                return '<span style="color: red;">' + data + '</span>';
                            }
                        },
                        { data: 'userliberacion', title: 'Liberado por', className: 'dt-body-center' },
                        {
                            data: 'situped',
                            title: 'Estado',
                            className: 'dt-body-center',
                            render: function (data, type, row) {
                                let label = "primary";
                                switch (data) {
                                    case "PD":
                                        label = "default";
                                        break;
                                    case "AN":
                                        label = "warning";
                                        break;
                                    case "CE":
                                        label = "success";
                                        break;
                                    case "DC":
                                        label = "info";
                                        break;
                                    case "PR":
                                        label = "default";
                                        break;
                                }
                                return `<span class='label label-${label}' style="color:white">${data}</span>`;
                            }
                        }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                    },
                    scrollX: true
                });
            }
        }

        $(document).ready(function () {
            $("#form_filtros").submit(function (e) {
                e.preventDefault();
                datatable(obtenerURL());
            });
        });
    </script>
</body>

</html>
