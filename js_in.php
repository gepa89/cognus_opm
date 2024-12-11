<script src="js/jquery.min.js"></script>
<script src="js/jquery-migrate.min.js"></script>
<script src="lib/jquery-ui/jquery-ui-1.10.0.custom.min.js"></script>
<!-- touch events for jquery ui-->
<script src="js/forms/jquery.ui.touch-punch.min.js"></script>
<!-- easing plugin -->
<script src="js/jquery.easing.1.3.min.js"></script>
<!-- smart resize event -->
<script src="js/jquery.debouncedresize.min.js"></script>
<!-- js cookie plugin -->
<script src="js/jquery_cookie_min.js"></script>
<!-- main bootstrap js -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- bootstrap plugins -->
<script src="js/bootstrap.plugins.min.js"></script>
<!-- typeahead -->
<script src="lib/typeahead/typeahead.min.js"></script>
<!-- code prettifier -->
<script src="lib/google-code-prettify/prettify.min.js"></script>
<!-- sticky messages -->
<script src="lib/sticky/sticky.min.js"></script>
<!-- lightbox -->
<script src="lib/colorbox/jquery.colorbox.min.js"></script>
<script src="js/jquery.scannerdetection.js"></script>
<!-- jBreadcrumbs -->
<script src="lib/jBreadcrumbs/js/jquery.jBreadCrumb.1.1.min.js"></script>
<!-- hidden elements width/height -->

<!-- hidden elements width/height -->
<script src="js/jquery.actual.min.js"></script>
<script src="js/jsuites.js"></script>
<script src="js/jexcel.js"></script>
<!-- custom scrollbar -->
<script src="lib/slimScroll/jquery.slimscroll.js"></script>
<!-- fix for ios orientation change -->
<script src="js/ios-orientationchange-fix.js"></script>
<!-- to top -->
<script src="lib/UItoTop/jquery.ui.totop.min.js"></script>
<!-- mobile nav -->
<script src="js/selectNav.js"></script>
<!-- moment.js date library -->
<script src="lib/moment/moment.min.js"></script>

<!-- common functions -->
<script src="js/pages/gebo_common.js"></script>

<script src="lib/multi-select/js/jquery.multi-select.js"></script>
<script src="lib/multi-select/js/jquery.quicksearch.js"></script>
<!-- enhanced select (chosen) -->
<script src="lib/chosen/chosen.jquery.min.js"></script>
<!-- multi-column layout -->
<script src="js/jquery.imagesloaded.min.js"></script>
<script src="js/jquery.wookmark.js"></script>
<script src="js/jquery-barcode.min.js"></script>
<!-- responsive table -->
<script src="js/jquery.mediaTable.min.js"></script>
<!-- small charts -->
<script src="js/jquery.peity.min.js"></script>
<!-- charts -->
<script src="lib/flot/jquery.flot.min.js"></script>
<script src="lib/flot/jquery.flot.resize.min.js"></script>
<script src="lib/flot/jquery.flot.pie.min.js"></script>
<script src="lib/flot.tooltip/jquery.flot.tooltip.min.js"></script>
<!-- calendar -->
<script src="lib/fullcalendar/fullcalendar.min.js"></script>
<!-- sortable/filterable list -->
<script src="lib/list_js/list.min.js"></script>
<script src="lib/list_js/plugins/paging/list.paging.min.js"></script>
<!-- dashboard functions -->

<!-- masked inputs -->
<script src="js/forms/jquery.inputmask.min.js"></script>
<!-- autosize textareas -->
<script src="js/forms/jquery.autosize.min.js"></script>
<!-- textarea limiter/counter -->
<script src="js/forms/jquery.counter.min.js"></script>
<!--<script src="js/pages/gebo_dashboard.js"></script>-->
<!-- validation -->
<script src="lib/validation/jquery.validate.min.js"></script>
<!-- wizard -->
<script src="lib/stepy/js/jquery.stepy.min.js"></script>
<!-- wizard functions -->


<script src="js/pages/gebo_wizard.js"></script>
<!-- datatable -->
<script src="lib/datatables/jquery.dataTables.min.js"></script>
<script src="lib/datatables/extras/Scroller/media/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="js/pdfmake.min.js"></script>
<script type="text/javascript" src="js/jszip.min.js"></script>
<script type="text/javascript" src="js/pdfmake.min.js"></script>
<script type="text/javascript" src="js/vfs_fonts.js"></script>
<script type="text/javascript" src="js/sweetalert.min.js"></script>


<script type="text/javascript" src="js/buttons.html5.min.js"></script>
<script type="text/javascript" src="js/buttons.print.min.js"></script>
<!-- datatable table tools -->
<script src="lib/datatables/extras/TableTools/media/js/TableTools.min.js"></script>
<script src="lib/datatables/extras/TableTools/media/js/ZeroClipboard.js"></script>
<!-- datatables bootstrap integration -->
<script src="lib/datatables/jquery.dataTables.bootstrap.min.js"></script>
<script src="js/pages/gebo_datatables.js"></script>
<script src="js/bootstrap-multiselect.js"></script>
<script src="js/highcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="/opm/js/iziToast.min.js"></script>


<script type="text/javascript" src="js/dataTables.searchPanes.min.js"></script>
<script type="text/javascript" src="js/dataTables.select.min.js"></script>
<style>
    .jexcel>tbody>tr>td.readonly {
        color: rgba(0, 0, 0, 1) !important;
    }
</style>
<script>
    iziToast.settings({
        timeout: 3000,
        resetOnHover: true,
        position:'topRight'
    });

    $(document).ajaxStart(function() {
        console.log("abro");
        $("#loading").removeClass('hiddn');
    });
    $(document).ajaxComplete(function() {
        $("#loading").addClass('hiddn');
        console.log("cierro");
    });

    $(document).ajaxStop(function() {
        $("#loading").addClass('hiddn');
    });

    function nullDoc(doc, tip) {
        var r = confirm("¿Desea anular el documento? La operación no se puede deshacer.");
        if (r) {
            $.ajax({
                url: 'requests/nullDoc.php',
                type: 'POST',
                data: {
                    doc: doc,
                    tip: tip
                },
                success: function(response) {
                    var dt = JSON.parse(response);
                    alert(dt.msg);
                    //console.log(dt);

                }
            });
        }
    }

    function pend(pedrefer, cod_alma) {

        $.ajax({
            url: 'api/articulosSinUbicacion.php',
            type: 'POST',
            data: {
                pedrefer: pedrefer,
                cod_alma: cod_alma
            },
            success: function(response) {
                let datos = response.datos;
                let tabla = "";
                for (const dato of datos) {
                    let cuerpoTabla = `
                    <tr>
                        <td>${dato.pedrefer}</td>
                        <td>${dato.pedpos}</td>
                        <td>${dato.artrefer}</td>
                        <td>${dato.artdesc}</td>
                         <td>${dato.ubirefer}</td>
                    </tr>`;
                    tabla += cuerpoTabla;
                }
                $("#cuerpo_tabla").empty().append(tabla);
                $("#modal_sin_ubicacion").modal("show");
            }
        });

    }

    function loadMatchModal(inputx, column, table, lim) {
        console.log(inputx);
        console.log(column);
        console.log(table);
        console.log(lim);
        var strin = $("#" + inputx).val();
        var col = column;
        var tbl = table;
        var colArr = column.split(",");
        var toDel = 1;
        $("#strin").attr('value', strin);
        $("#column").attr('value', col);
        $("#table").attr('value', tbl);
        $("#limAr").attr('value', lim);

        $(".modal-title").empty().append('Buscar por aproximación');

        if (colArr[1]) {
            console.log('#resultGrid es ');
            console.log(typeof($('#resultGrid')));
            //                $('#resultGrid').jexcel('deleteColumn', 0);//dt.length
            $.each(colArr, function(k, v) {
                if (k > 0) {
                    $('#resultGrid').jexcel('deleteColumn', k); //dt.length
                    $('#resultGrid').jexcel('insertColumn', k);
                    $('#resultGrid').jexcel('setHeader', 1, 'Descripcion');
                }
            });
            //                toDel = colArr.length;
        }
        if ($("#rowSel").length) {
            $("#sndSelBtn").attr("onclick", 'loadSelected(' + "'" + inputx + "'" + ')');
        } else {
            $('#resultGrid').append('<div style="clear:both;"></div><div id="rowSel"><br/><div class="input-group pull-left"><button type="button" id="sndSelBtn" onclick="loadSelected(' + "'" + inputx + "'" + ')" class="form-control btn btn-primary">Cargar</button></div></div>');
        }
        $('#modalMatcBox').modal('show');
    }

    function loadSelected(sel) {
        var idx = $('#resultGrid').jexcel('getSelectedRows', true);
        var dx = $('#resultGrid').jexcel('getRowData', idx);
        //            console.log("val "+idx);
        //           console.log("val "+dx);
        document.getElementById(sel).value = dx[0];
        $('#resultGrid').jexcel('setData', []);
        $('#modalMatcBox').modal('hide');
    }

    function searchMatchModal() {
        var strin = $("#strin").val();
        var column = $("#column").val();
        var table = $("#table").val();
        var limAr = $("#limAr").val();
        if (strin != '') {
            $.ajax({
                url: 'requests/getMatchResult.php',
                type: 'POST',
                data: {
                    strin: strin,
                    column: column,
                    table: table,
                    limAr: limAr
                },
                success: function(response) {
                    var dt = JSON.parse(response);
                    //console.log(dt);
                    $('#resultGrid').jexcel('setData', dt);

                }
            });
        }
    }

    function printElement() {
        var newWindow = window.open();
        newWindow.document.write(document.getElementById("modal-body-id").innerHTML);
        newWindow.print();
    }

    function closeSession() {
        $.ajax({
            url: 'rqst.php',
            type: 'POST',
            data: {
                logout: 'yes'
            },
            success: function(response) {
                var dt = JSON.parse(response);
                if (dt.err == 0) {
                    window.location.replace("login.php");
                } else {
                    alert(dt.msg);
                }

            }
        });
    }
    gebo_chosen = {
        init: function() {
            $(".chzn_a").chosen({
                allow_single_deselect: true
            });
            $(".chzn_b").chosen();
        }
    };
    $(document).ready(function() {
        jexcel(document.getElementById('resultGrid'), {
            allowInsertColumn: true,
            allowInsertRow: false,
            allowDeletingAllRows: false,
            allowDeleteColumn: true,
            allowDeleteRow: false,
            tableOverflow: true,
            //                allowRenameColumn:false,
            lazyLoading: true,
            //                fullscreen:true,
            wordWrap: true,
            loadingSpin: true,
            copyCompatibility: false,
            minDimensions: [1, 1],
            wordWrap: true,
            allowManualInsertColumn: true,
            colAlignments: ['left'],
            columns: [{
                    type: 'text',
                    readOnly: true
                }

            ],
            defaultColWidth: "400px",
            colHeaders: ['Resultado', 'Descripcion'],
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
        });

        gebo_chosen.init();

        //            loadLbl()
        //            setInterval(function(){
        //                loadHCPie();
        //                loadLbl();
        //           },300000);

        //        });
        //        function loadLbl(){
        //
        //           $.ajax({ 
        //                url: 'adaia_pedidos.php', 
        //                type: 'POST',
        //
        //                success: function (data) {
        //                    var dt = JSON.parse(data);
        //                    console.log(dt);
        //                    var dif = parseInt(dt.total) - parseInt(dt.totalp);
        //                    $("#lblPend").empty().append(dif);
        //                    $("#lblProc").empty().append(dt.totalp);
        //                    $("#lblTot").empty().append(dt.total);
        //                }
    });
    //
</script>