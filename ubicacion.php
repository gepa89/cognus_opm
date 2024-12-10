<?php
echo 1;
require ('conect.php');
//include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER,$USER,$PASS,$DB);


?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
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
                        <div class="col-sm-12 col-md-12">
                            <h3 class="heading">Configuración de Ubicación</h3>
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <form id="validate_wizard" class="stepy-wizzard form-horizontal form_validation_reg">
                                        <fieldset title="Creación de Ubicaciones">
                                            <legend class="hide">Dimensiones</legend>
                                            <div class="formSep form-group">
                                                <label for="a_cod_estanteria" class="col-md-2 control-label">Código de Estantería:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="a_cod_estanteria" id="a_cod_estanteria" class="input-sm form-control">
                                                </div>
                                            </div>
                                            <div class="formSep form-group">
                                                <label for="a_mod_estanteria" class="col-md-2 control-label">Modelo de Estantería:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="a_mod_estanteria" id="a_mod_estanteria" class="input-sm form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="a_c_desde" class="col-md-2 control-label">Coor. Desde:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="a_c_desde" id="a_c_desde" class="input-sm form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="a_c_hasta" class="col-md-2 control-label">Coor. Hasta:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="a_c_hasta" id="a_c_hasta" class="input-sm form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="a_nivel" class="col-md-2 control-label">Nivel:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="a_nivel" id="a_nivel" class="input-sm form-control">
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset title="Almacenaje">
                                            <legend class="hide">Definición de Zonas</legend>
                                            <div class="formSep form-group">
                                                <div class="col-md-12 col-lg-12 col-xs-12">
                                                    <h3 class="heading">Configuración</h3>
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
                                        <fieldset title="Picking">
                                            <legend class="hide">Definición de Zonas</legend>
                                            <div class="formSep form-group">
                                                <div class="col-md-12 col-lg-12 col-xs-12">
                                                    <h3 class="heading">Configuración</h3>
                                                    <div class="col-md-1">

                                                    </div>
                                                    <div class="col-md-10">
                                                        <div id="demo2"></div>
                                                    </div>
                                                    <div class="col-md-1">

                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset title="Sub Niveles">
                                            <legend class="hide">Definición de Espacio</legend>
                                            <div class="formSep form-group">
                                                <div class="col-md-12 col-lg-12 col-xs-12">
                                                    <h3 class="heading">Configuración</h3>
                                                    <div class="col-md-1">

                                                    </div>
                                                    <div class="col-md-10">
                                                        <div id="demo3"></div>
                                                    </div>
                                                    <div class="col-md-1">

                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset title="Clases">
                                            <legend class="hide">Definición de Ubicaciones</legend>
                                            <div class="formSep form-group">
                                                <div class="col-md-12 col-lg-12 col-xs-12">
                                                    <h3 class="heading">Configuración</h3>
                                                    <div class="col-md-1">

                                                    </div>
                                                    <div class="col-md-10">
                                                        <div id="demo4"></div>
                                                    </div>
                                                    <div class="col-md-1">

                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <button type="button" class="finish btn btn-primary"><i class="glyphicon glyphicon-ok"></i> Guardar</button>
                                    </form>
                                    <div class="col-md-12 col-lg-12 col-xs-12">
                                        <h3 class="heading">Mapa</h3>
                                        <div id="accordion1" class="panel-group accordion">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <a href="#collapseThree" data-parent="#accordion1" data-toggle="collapse" class="accordion-toggle">
                                                        Niveles - Vista previa
                                                    </a>
                                                </div>
                                                <div class="panel-collapse collapse" id="collapseThree">
                                                    <div class="panel-body" style="overflow-x:auto;">
                                                        <div id="vw_table">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-heading">
                                                    <a href="#collapseFour" data-parent="#accordion1" data-toggle="collapse" class="accordion-toggle">
                                                        Sub Niveles - Vista previa
                                                    </a>
                                                </div>
                                                <div class="panel-collapse collapse" id="collapseFour">
                                                    <div class="panel-body" style="overflow-x:auto;">
                                                        <div id="vw_table_sub">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            var dex = [['','','','','']];
            function loadOrigin(id){
                var res = $('#'+id).jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {
//                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
//                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
//    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
//                            if($('#coord_alm_'+i+'_'+j).text() != '0'){
//                                error.push(1);
//                            }else{
//                                error.push(0);
//                            }
//                        }
//                    }
//                    if(error.includes(1)){
//                        alert('Rango inválido');
//                        return false;
//                    }else{
                        for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                            for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                                switch(zona){
                                    case 'A':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_red');
                                        break;
                                    case 'B':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_yellow');
                                        break;
                                    case 'C':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_green');
                                        break;
                                }
                                $('#coord_alm_'+i+'_'+j).empty().text('AL');
                            }
                        }
//                            $("#demo1 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
//                    }
                }
            }
            var changed = function(instance, cell, x, y, value) {
                var cellName = jexcel.getColumnNameFromId([x,y]);                
//                $('#log').append('<br/>New change on cell ' + y + ' to: ' + value + '');
            }
            var changed2 = function(instance, cell, x, y, value) {
                var cellName = jexcel.getColumnNameFromId([x,y]);                
//                $('#log').append('<br/>New change on cell ' + y + ' to: ' + value + '');
            }
            
            var beforedeleterow = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo1').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {                    
                    
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            switch(zona){
                                case 'A':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_red');
                                    break;
                                case 'B':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_yellow');
                                    break;
                                case 'C':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_green');
                                    break;
                            }
                            $('#coord_alm_'+i+'_'+j).empty().text('0');
                        }
                    }
                    return true;
                }else{
                    return true;
                }
                
            }
            var beforedeleterow2 = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo2').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {                    
                    
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            switch(zona){
                                case 'A':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_red');
                                    break;
                                case 'B':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_yellow');
                                    break;
                                case 'C':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_green');
                                    break;
                            }
                            $('#coord_alm_'+i+'_'+j).empty().text('0');
                        }
                    }
                    return true;
                }else{
                    return true;
                }
                
            }
            var beforedeleterow3 = function(instance, row)  {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo3').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var sub_niveles = res[4]; 
                var error = []; 
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && sub_niveles != '' ) {                    
                    
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_sub_'+i+'_'+j + ' vale ' + $('#coord_sub_'+i+'_'+j).text());
                            $('#coord_sub_'+i+'_'+j).removeClass('c_blue');
                            $('#coord_sub_'+i+'_'+j).empty().text('0');
                        }
                    }
                    return true;
                }else{
                    return true;
                }
                
            }
            var beforedeleterow4 = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo4').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {                    
//                    al borrar una fila, se debe buscar el rango original de columnas y filas de la ubicación para restablecer
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            switch(zona){
                                case 'NE':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_gray');
                                    break;
                                case 'RE':
                                    $('#coord_alm_'+i+'_'+j).removeClass('c_orange');
                                    break;
                            }
                            $('#coord_alm_'+i+'_'+j).empty().text('0');
                        }
                    }
                    return true;
                }else{
                    return true;
                }
                
            }
            var beforeInsRow = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo1').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            if($('#coord_alm_'+i+'_'+j).text() != '0'){
                                error.push(1);
                            }else{
                                error.push(0);
                            }
                        }
                    }
                    if(error.includes(1)){
                        alert('Rango inválido');
                        return false;
                    }else{
                        for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                            for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                                switch(zona){
                                    case 'A':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_red');
                                        break;
                                    case 'B':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_yellow');
                                        break;
                                    case 'C':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_green');
                                        break;
                                }
                                $('#coord_alm_'+i+'_'+j).empty().text('AL');
                            }
                        }
//                        lockRow(row);
//                        $.each("td[data-y='"+row+"']",function(){
                            $("#demo1 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
//                        });
                        return true;
                    }
                }else{
                    alert('Favor completar todos los campos');
                    return false;
                }
                
            }
            var beforeInsRow2 = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo2').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            if($('#coord_alm_'+i+'_'+j).text() != '0'){
                                error.push(1);
                            }else{
                                error.push(0);
                            }
                        }
                    }
                    if(error.includes(1)){
                        alert('Rango inválido');
                        return false;
                    }else{
                        for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                            for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                                switch(zona){
                                    case 'A':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_red');
                                        break;
                                    case 'B':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_yellow');
                                        break;
                                    case 'C':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_green');
                                        break;
                                }
                                $('#coord_alm_'+i+'_'+j).empty().text('PI');
                            }
                        }
//                        lockRow(row);
//                        $.each("td[data-y='"+row+"']",function(){
                            $("#demo2 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
//                        });
                        return true;
                    }
                }else{
                    alert('Favor completar todos los campos');
                    return false;
                }
                
            }
            var beforeInsRow3 = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo3').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var sub_niveles = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && sub_niveles != '' ) {
                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
    //                        console.log('#coord_sub_'+i+'_'+j + ' vale ' + $('#coord_sub_'+i+'_'+j).text());
                            if($('#coord_sub_'+i+'_'+j).text() != '0'){
                                error.push(1);
                            }else{
                                error.push(0);
                            }
                        }
                    }
                    if(error.includes(1)){
                        alert('Rango inválido');
                        return false;
                    }else{
                        for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                            for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
                                $('#coord_sub_'+i+'_'+j).addClass('c_blue');
                                $('#coord_sub_'+i+'_'+j).empty().text(sub_niveles);
                            }
                        }
//                        lockRow(row);
//                        $.each("td[data-y='"+row+"']",function(){
                            $("#demo3 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
//                        });
                        return true;
                    }
                }else{
                    alert('Favor completar todos los campos');
                    return false;
                }
                
            }
            var beforeInsRow4 = function(instance, row) {
//                console.log(instance);
//                console.log(row);
//                return true;
//                var cellName = jexcel.getColumnNameFromId([x,y]);     
                var res = $('#demo4').jexcel('getRowData', row);
                var c_desde = res[0];
                var n_desde = res[1];
                var c_hasta = res[2];
                var n_hasta = res[3];
                var zona = res[4];
                var error = [];
                if(c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '' ) {
//                    for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
//                        for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
//    //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
//                            if($('#coord_alm_'+i+'_'+j).text() != '0'){
//                                error.push(1);
//                            }else{
//                                error.push(0);
//                            }
//                        }
//                    }
//                    if(error.includes(1)){
//                        alert('Rango inválido');
//                        return false;
//                    }else{
                        for(i = parseInt(c_desde); i <= parseInt(c_hasta); i++ ){
                            for( j = parseInt(n_desde); j <= parseInt(n_hasta); j++ ){
        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                                switch(zona){
                                    case 'NE':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_gray');
                                        break;
                                    case 'RE':
                                        $('#coord_alm_'+i+'_'+j).addClass('c_orange');
                                        break;
                                    case 'PI':
//                                        $('#coord_alm_'+i+'_'+j).addClass('c_orange');
                                        break;
                                }
                                $('#coord_alm_'+i+'_'+j).empty().text(zona);
                            }
                        }
//                        lockRow(row);
//                        $.each("td[data-y='"+row+"']",function(){
                            $("#demo4 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
//                        });
                        return true;
//                    }
                }else{
                    alert('Favor completar todos los campos');
                    return false;
                }
                
            }
//            function lockRow(rwId){
//                $('yourId').find('.r' + row').addClass('readonly');
//            }
            var insertedRow = function(instance) {
                console.log(instance);
                $('#log').append('Row added');
            }
            var insertedRow2 = function(instance) {
                console.log(instance);
                $('#log').append('Row added');
            }
            $( document ).on( "click", '#validate_wizard-next-0', function() {
                var columna_desde = $('#a_c_desde').val();
                var columna_hasta = $('#a_c_hasta').val();
                var filas = $('#a_nivel').val();
                console.log('columna desde '+parseInt(columna_desde)+' hasta '+parseInt(columna_hasta));
                var cntnt = '<table class="table" style="font-size:9px !important; width:80% !important; margin: 0 auto !important;">';
                cntnt += '<thead>';
                cntnt += '<tr>';
                cntnt += '<th></th>';
                for( i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++ ){
                    cntnt += '<th id="col_alm_'+i+'">'+i+'</th>';
                }
                cntnt += '</tr>';
                cntnt += '</thead>';
                cntnt += '<tbody>';
                
                for( j = 1; j <= parseInt(filas); j++ ){
                    cntnt += '<tr>';
                    cntnt += '<td id="row_alm_'+j+'">'+j+'</td>';
                    for( i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++ ){
                        cntnt += '<td id="coord_alm_'+i+'_'+j+'">'+0+'</td>';
                    }
                    cntnt += '</tr>';   
                }
                cntnt += '</tbody>';
                cntnt += '</table>';
                var cntntsb = '<table class="table" style="font-size:9px !important; width:80% !important; margin: 0 auto !important;">';
                cntntsb += '<thead>';
                cntntsb += '<tr>';
                cntntsb += '<th></th>';
                for( i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++ ){
                    cntntsb += '<th id="col_sub_'+i+'">'+i+'</th>';
                }
                cntntsb += '</tr>';
                cntntsb += '</thead>';
                cntntsb += '<tbody>';
                
                for( j = 1; j <= parseInt(filas); j++ ){
                    cntntsb += '<tr>';
                    cntntsb += '<td id="row_sub_'+j+'">'+j+'</td>';
                    for( i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++ ){
                        cntntsb += '<td id="coord_sub_'+i+'_'+j+'">'+0+'</td>';
                    }
                    cntntsb += '</tr>';   
                }
                cntntsb += '</tbody>';
                cntntsb += '</table>';
                $('#vw_table').empty().append(cntnt);
                $('#vw_table_sub').empty().append(cntntsb);
            });
            $(document).ready(function(){
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
                    colAlignments: [ 'left', 'left', 'left','left', 'left'],
                    columns: [
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        {
                            type: 'dropdown',
                            source: ['A','B','C']
                        }
                    ],
                    onchange: changed,  
                    onbeforeinsertrow: beforeInsRow,
                    onbeforedeleterow:beforedeleterow,
                    colHeaders: ['Est. Desde', 'Nivel', 'Est. Hasta', 'Nivel', 'Zona'],
                    colWidths: [ 150, 150, 150, 150, 150 ],
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
                jexcel(document.getElementById('demo2'), {
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
                    colAlignments: [ 'left', 'left', 'left','left', 'left'],
                    columns: [
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        {
                            type: 'dropdown',
                            source: ['A','B','C']
                        }
                    ],
                    onchange: changed2,  
                    onbeforeinsertrow: beforeInsRow2,
                    onbeforedeleterow:beforedeleterow2,
                    colHeaders: ['Est. Desde', 'Nivel', 'Est. Hasta', 'Nivel', 'Zona'],
                    colWidths: [ 150, 150, 150, 150, 150 ],
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
                jexcel(document.getElementById('demo3'), {
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
                    colAlignments: [ 'left', 'left', 'left','left', 'left'],
                    columns: [
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' }
                    ],
                    onbeforeinsertrow: beforeInsRow3,
                    onbeforedeleterow:beforedeleterow3,
                    colHeaders: ['Est. Desde', 'Nivel', 'Est. Hasta', 'Nivel', 'Sub Niveles'],
                    colWidths: [ 150, 150, 150, 150, 150 ],
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
                jexcel(document.getElementById('demo4'), {
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
                    colAlignments: [ 'left', 'left', 'left','left', 'left'],
                    columns: [
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        { type: 'text' },
                        {
                            type: 'dropdown',
                            source: ['NE','RE','PI']
                        }
                    ],
                    onbeforeinsertrow: beforeInsRow4,
                    onbeforedeleterow:beforedeleterow4,
                    colHeaders: ['Est. Desde', 'Nivel', 'Est. Hasta', 'Nivel', 'Clase'],
                    colWidths: [ 150, 150, 150, 150, 150 ],
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
