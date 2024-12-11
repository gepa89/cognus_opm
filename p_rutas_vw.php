<?php
require ('conect.php');
//include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$pd = '';
$fl = 0;
$idx = trim($_REQUEST['id']);
//echo $idx;
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$sq = "select rutdes from ruta where rutcod = {$idx}";
$rs = $db->query($sq);
$ax = $rs->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                position: relative;
                padding-left: 30px;
                cursor: pointer;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
                content: "-";
                background-color: #d33333;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
                top: 33%;
                left: 5px;
                height: 1em;
                width: 1em;
                margin-top: -5px;
                display: block;
                position: absolute;
                color: white;
                border: .15em solid white;
                border-radius: 1em;
                box-shadow: 0 0 0.2em #444;
                box-sizing: content-box;
                text-align: center;
                text-indent: 0 !important;
                font-family: "Courier New",Courier,monospace;
                line-height: 1em;
                content: "+";
                background-color: #31b131;
            }
            tr.shown td .details-control {
                background: url('details_close.png') no-repeat center center;
            }
            .label {
                 color: #000; 
            }
            #tblReg tbody tr td:first-of-type{
                width:100px;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                    <?php

                        echo '<table class="table">'
                            . '<thead>'
                                . '<tr>'
                                . '<th colspan="3">Editar '.$ax['rutdes'].'</th>'
                                . '</tr>'
                            . '</thead>'
                        . '</table>';?>
                        <div class="col-md-12 col-lg-12 col-xs-12">
                            <h3 class="heading">Configuración</h3>
                            <div class="col-md-12">
                                <div id="demo1"></div>
                            </div><br/>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Guardar</button>
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
        <script type="text/javascript">
            
            var in_id = '<?php echo $idx?>';
            function ldGrid(data){
                console.log(data);
                $('#demo1').jexcel(
                    'setData', 
                    data,
                    true);
            }
//            
            
            var dex = [['','','','','','','','']];
            var arry = [];
            var table;
            var idX = idY = '';
            var flagToRow = 0;
            var afterchanged = function(instance, records){
                return false;
            }
            var onEvent = function(arguments){
//                console.log(arguments);
            }
            var changed = function(instance, cell, x, y, value) {
//                console.log(x+", "+y+", "+value);
//                console.log(idX+", "+idY+", ");
                var cellName = jexcel.getColumnNameFromId([x,y]);   
                var res = $('#demo1').jexcel('getRowData', y);
//                console.log(res);
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/checkLocation.php', 
                    data:{
                        cell:cellName,
                        row:res,
                        val:value
                    },success: function (data) {
                        var dt = JSON.parse(data);
//                        console.log($.fn.jexcel.ignoreEvents);
                        if(dt.res == 0){ 
                            flagToRow = 1;
                            alert(dt.msg);
//                                $('#demo1').jexcel('setValueFromCoords',x,y,'',true);
                        }else{
                            flagToRow = 0;
                        }             
//                        $('#demo1').jexcel('setValueFromCoords',x,y,dt.dat,true);

                    }
                });
                
                event.preventDefault();
                console.log(event);
            }
            function ckFields(){
                var res = $('#demo1').jexcel('getData', false);
                var r = confirm("Definir como ruta predeterminada?");
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/updateRuta.php', 
                    data:{
                        id:in_id,
                        data:res,
                        det:r
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                    }
                });
            }
            var beforeInsRow = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo1').jexcel('getRowData', row);
                console.log($.inArray('',res));
//                var error = [];
                var hasEmpty = $.inArray('',res);
                if( hasEmpty == -1){   
                    if(flagToRow == 0){                        
                        return true;
                    }else{
                        alert('Existen campos con datos erroneos');
                        return false;
                    }
                }else{
                    alert('Favor completar todos los campos');
                    return false;
                }
                
            }
            $(document).ready(function(){
                
                $('#param6').addClass('active');
                $('#c7').click();
                jexcel(document.getElementById('demo1'), {
//                $('#demo1').jexcel({ 
                    data:dex,
                    allowInsertColumn:false,
                    allowInsertRow:true,
                    allowDeletingAllRows:false,
                    allowDeleteColumn:false,
                    allowDeleteRow:true,
                    tableOverflow:false,
                    allowRenameColumn:false,
                    copyCompatibility:false,
                    minDimensions:[5,1],
                    allowManualInsertColumn:false,
                    colAlignments: [ 'left', 'left', 'left','left', 'left','left', 'left', 'left', 'left'],
                    columns: [
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        {
                            type: 'dropdown',
                            source: ['P','L']
                        },
                        {
                            type: 'dropdown',
                            source: ['H','V']
                        }
                    ],
                    onchange: changed,  
                    onevent: onEvent,
                    onbeforeinsertrow: beforeInsRow,
                    colHeaders: ['Est. Desde', 'Hueco. Desde', 'Niv. inicial','Est. Hasta', 'Hueco. Hasta', 'Niv. final', 'Almacen', 'Tipo', 'Sentido'],
                    colWidths: [ 150, 150, 150, 150, 150, 150, 150, 130, 130 ],
                    text:{
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
                if(in_id != ''){
//                    alert("entro");
                    $.ajax({ 
                        url: 'requests/getRuta.php', 
                        type: 'POST',
                        data: {
                            id:in_id
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            var cntnt = '';
                            var cod = '';
//                            console.log(dts);
                            if(parseInt(dt.total) > 0){
                                dts = dt.dat;
                            }else{
                                data = ['','','','','','','',''];
                                dts.push(data);
                            }
//                            console.log(dts);
                            ldGrid(dts)
    //                        $('#tbl_mat tbody').empty().append(cntnt);
                        }
                    });
                }
            });
            function min_to_hour(min){
                var zero2 = new Padder(2);
                var deci = min - Math.floor(min);
                deci = Math.floor(parseFloat(deci)*10);
                if(deci >= 5){
                    var realmin = Math.ceil(min % 60);
                }else{
                    var realmin = Math.floor(min % 60);
                }
                if(realmin > 59){
                    realmin = 0;
                    var hour = Math.ceil(min / 60);
                }else{
                    var hour = Math.floor(min / 60);
                }
                
                
                return zero2.pad(hour)+":"+zero2.pad(realmin);
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
