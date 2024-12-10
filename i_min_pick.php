<?php
//echo 1;
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("inventario")) {
    echo "No tiene acceso";
    exit;
}
$db = new mysqli($SERVER,$USER,$PASS,$DB);


?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            .hiddn{
                display:none;
            }
            .c_red{
                background-color: #ffafaf;
            }
            .c_yellow{
                background-color: #effc8b;
            }
            .c_green{
                background-color: #8eff91;
            }
            .c_blue{
                background-color: #717eff;
            }
            .c_gray{
                background-color: #878787;
            }
            .c_orange{
                background-color: #f6b065;
            }
            
            .lecirc.stepNb {
                position: absolute;
                display: block;
                background: #efefef;
                color: #818181;
                -webkit-border-radius: 7px;
                -moz-border-radius: 7px;
                -ms-border-radius: 7px;
                border-radius: 7px;
                width: 34px;
                left: 0;
                top: 3px;
                line-height: 17px;
                font-size: 9px;
                text-align: center;
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
                                    . '<th>Mínimos para Picking</th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        <div class="col-lg-12 col-sm-12 former">
                            <form method="post" id="eForm">
                                <div class="col-lg-12 col-sm-12" >
                                    <div class="col-lg-2 col-sm-2">
                                        <div class="input-group ">
                                            <input type="text" name="mat" id="mat" class="form-control" placeholder="Material(es)" value=""/>
                                        </div>
                                    </div>
                                   <div class="col-lg-1 col-sm-1">
                                        <div class="input-group">
                                            <button type="button" id="sndBtn" onclick="loadConfig()" class="form-control btn btn-primary">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </form> 
                        </div><div style="clear:both;"></div>
                    </div><div style="clear:both;"></div><br /><br />
                    
                    <div class="row">
                        <div style="clear:both;"></div><br /><br />
                        <div class="col-md-12 col-lg-12 col-xs-12"><div style="clear:both;"></div><br /><br />
                            <fieldset title="Almacenaje">
                                <div class="formSep form-group">
                                    <div class="col-md-12 col-lg-12 col-xs-12">
                                        <h3 class="heading">Materiales</h3>
                                        <div class="col-md-1">

                                        </div>
                                        <div class="col-md-10">
                                            <div id="demo1"></div>
                                        </div>
                                        <div class="col-md-1">

                                        </div>
                                    </div>

<!--                                                <div class="col-md-12">
                                        <div>
                                            <button onclick="$('#log').html('')">Clear</button><br>
                                            <p>Log:</p>
                                            <div id="log" style="background-color:#c7eaff; border-radius:2px; color:#000; padding:20px"></div>
                                        </div>
                                    </div>-->

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        include 'sidebar.php';
        include 'js_in.php';
        include 'js_fn.php';
    ?>
        <script>
            var t1;
            var t2;
            var t3;
            var t4;
            function ldGrid(instance, data){
                console.log(data);
                $('#'+instance).jexcel(
                    'setData', 
                    data,
                    true);
            }
            function loadConfig(){
                var mat = $("#mat").val();
                
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/getMinPick.php', 
                    data: {
                        mat:mat
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 0){                            
                            ldGrid('demo1', dt.dat.almacenaje);
                        }else{
                            alert(dt.msg);
                        }
                    }
                });
            }
            var dex = [['','','','']];
            function arrayHasEmptyStrings(datArr) {
                for (var index = 0; index < datArr.length; index++) {
                    if(jQuery.inArray('',datArr[index]) == -1){
                        for (var ix = 0; ix < datArr[index].length; ix++) {
                            if (datArr[index][ix] == ''){
//                                console.log(true);
                                return true;
                            }else{
//                                console.log(false);
                                return false;
                            }
                        }
                    }else{
//                        console.log(true);
                        return true;
                    }                    
                }
            }
            $(document).ready(function(){
                $('#inven3').addClass('active');
                $('#c8').click();
                $("#validate_wizard").submit(function(){
                    return false;
                  });
                t1 = jexcel(document.getElementById('demo1'), {
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
                    minDimensions:[4,1],
                    allowManualInsertColumn:false,
                    colAlignments: [ 'left', 'left', 'left','left'],
                    columns: [
                        { type: 'text' },
                        { type: 'number' },
                        { type: 'text' },
                        { type: 'text' }
                    ],
//                    onchange: changed,  
//                    onevent: onEvent,
//                    onbeforeinsertrow: beforeInsRow,
                    colHeaders: ['Material', 'Mínimo', 'Centro', 'Almacen'],
                    colWidths: [ 150, 150, 150, 150],
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
            });
        </script>
    </body>
</html>
