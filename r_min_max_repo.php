<?php
//echo 1;
require('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("reportes")) {
    echo "No tiene acceso";
    exit;
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);


?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
    <style>
        .hiddn {
            display: none;
        }

        .c_red {
            background-color: #ffafaf;
        }

        .c_yellow {
            background-color: #effc8b;
        }

        .c_green {
            background-color: #8eff91;
        }

        .c_blue {
            background-color: #717eff;
        }

        .c_gray {
            background-color: #878787;
        }

        .c_orange {
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
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <?php

                    echo '<table class="table">'
                        . '<thead>'
                        . '<tr>'
                        . '<th colspan="3">Máximos y Mínimos para Reposición</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <div class="col-lg-12 col-sm-12 former">
                        <form method="post" id="eForm">
                            <div class="col-lg-12 col-sm-12">
                                <!--                                    <div class="col-lg-2 col-sm-2">
                                        <div class="input-group ">
                                            
                                        </div>
                                    </div>-->
                                <div class="col-lg-3 col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="mat" id="mat" class="form-control" placeholder="Material(es)" value="" />
                                        <!--                                            <small>Para varios materiales, ingresar separados entre ','(comas)</small>-->
                                        <span class="input-group-btn">
                                            <!--poner siempre la columna clave primero cuando hay mas de una-->
                                            <button class="btn btn-default" onclick="loadMatchModal('mat','artrefer,artdesc', 'arti', 1)" type="button"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-1 col-sm-1">
                                    <!--<label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>-->
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="loadConfig()" class="form-control btn btn-primary">Buscar</button>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <!--<label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>-->
                                    <div class="input-group">
                                        <img id="loading" style="position:relative; width: 40px !important; height: 40px !important;" class="hiddn" src="images/cargando1.gif" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div style="clear:both;"></div><br />

                <div class="row">
                    <!--<div style="clear:both;"></div><br /><br />-->
                    <div class="col-md-12 col-lg-12 col-xs-12">
                        <div style="clear:both;"></div><br /><br />
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
    include 'modal_match.php';
    include 'sidebar.php';
    include 'js_in.php';
    include 'js_fn.php';
    ?>
    <script>
        var t1;
        var t2;
        var t3;
        var t4;

        function ldGrid(instance, data) {
            console.log(data);
            $('#' + instance).jexcel(
                'setData',
                data,
                true);
        }

        function loadConfig() {
            var mat = $("#mat").val();

            $.ajax({
                type: 'POST',
                url: 'requests/getMinMaxRepo.php',
                data: {
                    mat: mat
                },
                success: function(data) {
                    var dt = JSON.parse(data);
                    if (dt.err == 0) {
                        ldGrid('demo1', dt.dat.almacenaje);
                    } else {
                        alert(dt.msg);
                    }
                }
            });
        }
        var dex = [
            ['', '', '', '', '', '']
        ];

        function arrayHasEmptyStrings(datArr) {
            for (var index = 0; index < datArr.length; index++) {
                if (jQuery.inArray('', datArr[index]) == -1) {
                    for (var ix = 0; ix < datArr[index].length; ix++) {
                        if (datArr[index][ix] == '') {
                            //                                console.log(true);
                            return true;
                        } else {
                            //                                console.log(false);
                            return false;
                        }
                    }
                } else {
                    //                        console.log(true);
                    return true;
                }
            }
        }

        function svLocation() {
            var a_hash_estanteria = $('#a_cod_hash').val();
            var a_cod_estanteria = $('#a_cod_estanteria').val();
            var a_c_desde = $('#a_c_desde').val();
            var a_c_hasta = $('#a_c_hasta').val();
            var a_nivel = $('#a_nivel').val();
            var almacenaje = $('#demo1').jexcel('getData', false);
            var picking = $('#demo2').jexcel('getData', false);
            var subniveles = $('#demo3').jexcel('getData', false);
            var clases = $('#demo4').jexcel('getData', false);
            //                console.log(almacenaje);
            //                console.log(picking);
            //                console.log(subniveles);
            //                console.log(clases);
            var flg = 0;
            if (arrayHasEmptyStrings(almacenaje)) {
                flg = 1;
                alert("Favor completar datos de almacenaje");
            } else if (arrayHasEmptyStrings(picking)) {
                flg = 1;
                alert("Favor completar datos de picking");
            } else if (arrayHasEmptyStrings(subniveles)) {
                flg = 1;
                alert("Favor completar datos de subniveles");
            } else if (arrayHasEmptyStrings(clases)) {
                flg = 1;
                alert("Favor completar datos de clases");
            }
            if (flg == 0) {
                alert('datos guardados');
                $.ajax({
                    type: 'POST',
                    url: 'requests/upateLocation.php',
                    data: {
                        a_hash: a_hash_estanteria,
                        a_cod_estanteria: a_cod_estanteria,
                        a_c_desde: a_c_desde,
                        a_c_hasta: a_c_hasta,
                        a_nivel: a_nivel,
                        almacenaje: almacenaje,
                        picking: picking,
                        subniveles: subniveles,
                        clases: clases
                    },
                    success: function(data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if (dt.err == 0) {
                            window.location = 'p_ubicacion.php';
                        }
                    }
                });
            }

        }

        function loadOrigin(id) {
            var res = $('#' + id).jexcel('getRowData', row);
            var c_desde = res[0];
            var n_desde = res[1];
            var c_hasta = res[2];
            var n_hasta = res[3];
            var zona = res[4];
            var error = [];
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {
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
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        switch (zona) {
                            case 'A':
                                $('#coord_alm_' + i + '_' + j).addClass('c_red');
                                break;
                            case 'B':
                                $('#coord_alm_' + i + '_' + j).addClass('c_yellow');
                                break;
                            case 'C':
                                $('#coord_alm_' + i + '_' + j).addClass('c_green');
                                break;
                        }
                        $('#coord_alm_' + i + '_' + j).empty().text('AL');
                    }
                }
                //                            $("#demo1 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
                //                    }
            }
        }
        var changed = function(instance, cell, x, y, value) {
            var cellName = jexcel.getColumnNameFromId([x, y]);
            //                $('#log').append('<br/>New change on cell ' + y + ' to: ' + value + '');
        }
        var changed2 = function(instance, cell, x, y, value) {
            var cellName = jexcel.getColumnNameFromId([x, y]);
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {

                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        switch (zona) {
                            case 'A':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_red');
                                break;
                            case 'B':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_yellow');
                                break;
                            case 'C':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_green');
                                break;
                        }
                        $('#coord_alm_' + i + '_' + j).empty().text('0');
                    }
                }
                return true;
            } else {
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {

                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        switch (zona) {
                            case 'A':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_red');
                                break;
                            case 'B':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_yellow');
                                break;
                            case 'C':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_green');
                                break;
                        }
                        $('#coord_alm_' + i + '_' + j).empty().text('0');
                    }
                }
                return true;
            } else {
                return true;
            }

        }
        var beforedeleterow3 = function(instance, row) {
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && sub_niveles != '') {

                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_sub_'+i+'_'+j + ' vale ' + $('#coord_sub_'+i+'_'+j).text());
                        $('#coord_sub_' + i + '_' + j).removeClass('c_blue');
                        $('#coord_sub_' + i + '_' + j).empty().text('0');
                    }
                }
                return true;
            } else {
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {
                //                    al borrar una fila, se debe buscar el rango original de columnas y filas de la ubicación para restablecer
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        switch (zona) {
                            case 'NE':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_gray');
                                break;
                            case 'RE':
                                $('#coord_alm_' + i + '_' + j).removeClass('c_orange');
                                break;
                        }
                        $('#coord_alm_' + i + '_' + j).empty().text('0');
                    }
                }
                return true;
            } else {
                return true;
            }

        }
        var beforeInsRow = function(instance, row) {
            //                console.log(instance);
            //                console.log(row);
            //                return true;
            //                var cellName = jexcel.getColumnNameFromId([x,y]);     
            var res = $('#demo1').jexcel('getRowData', row);
            console.log(res);
            var c_desde = res[0];
            var n_desde = res[1];
            var c_hasta = res[2];
            var n_hasta = res[3];
            var zona = res[4];
            var error = [];
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        console.log('#coord_alm_' + i + '_' + j + ' vale ' + $('#coord_alm_' + i + '_' + j).text());
                        if ($('#coord_alm_' + i + '_' + j).text() != '0') {
                            error.push(1);
                        } else {
                            error.push(0);
                        }
                    }
                }
                if (error.includes(1)) {
                    alert('Rango inválido');
                    return false;
                } else {
                    for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                        for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                            //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            switch (zona) {
                                case 'A':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_red');
                                    break;
                                case 'B':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_yellow');
                                    break;
                                case 'C':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_green');
                                    break;
                            }
                            $('#coord_alm_' + i + '_' + j).empty().text('AL');
                        }
                    }
                    //                        lockRow(row);
                    //                        $.each("td[data-y='"+row+"']",function(){
                    //                            $("#demo1 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
                    //                        });
                    return true;
                }
            } else {
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
            console.log(res);
            var c_desde = res[0];
            var n_desde = res[1];
            var c_hasta = res[2];
            var n_hasta = res[3];
            var zona = res[4];
            var error = [];
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        if ($('#coord_alm_' + i + '_' + j).text() != '0') {
                            error.push(1);
                        } else {
                            error.push(0);
                        }
                    }
                }
                if (error.includes(1)) {
                    alert('Rango inválido');
                    return false;
                } else {
                    for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                        for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                            //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                            switch (zona) {
                                case 'A':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_red');
                                    break;
                                case 'B':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_yellow');
                                    break;
                                case 'C':
                                    $('#coord_alm_' + i + '_' + j).addClass('c_green');
                                    break;
                            }
                            $('#coord_alm_' + i + '_' + j).empty().text('PI');
                        }
                    }
                    //                        lockRow(row);
                    //                        $.each("td[data-y='"+row+"']",function(){
                    //                            $("#demo2 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
                    //                        });
                    return true;
                }
            } else {
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && sub_niveles != '') {
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_sub_'+i+'_'+j + ' vale ' + $('#coord_sub_'+i+'_'+j).text());
                        if ($('#coord_sub_' + i + '_' + j).text() != '0') {
                            error.push(1);
                        } else {
                            error.push(0);
                        }
                    }
                }
                if (error.includes(1)) {
                    alert('Rango inválido');
                    return false;
                } else {
                    for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                        for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                            $('#coord_sub_' + i + '_' + j).addClass('c_blue');
                            $('#coord_sub_' + i + '_' + j).empty().text(sub_niveles);
                        }
                    }
                    //                        lockRow(row);
                    //                        $.each("td[data-y='"+row+"']",function(){
                    //                            $("#demo3 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
                    //                        });
                    return true;
                }
            } else {
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
            if (c_desde != '' && n_desde != '' && c_hasta != '' && n_hasta != '' && zona != '') {
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
                for (i = parseInt(c_desde); i <= parseInt(c_hasta); i++) {
                    for (j = parseInt(n_desde); j <= parseInt(n_hasta); j++) {
                        //                        console.log('#coord_alm_'+i+'_'+j + ' vale ' + $('#coord_alm_'+i+'_'+j).text());
                        switch (zona) {
                            case 'NE':
                                $('#coord_alm_' + i + '_' + j).addClass('c_gray');
                                break;
                            case 'RE':
                                $('#coord_alm_' + i + '_' + j).addClass('c_orange');
                                break;
                            case 'PI':
                                //                                        $('#coord_alm_'+i+'_'+j).addClass('c_orange');
                                break;
                        }
                        $('#coord_alm_' + i + '_' + j).empty().text(zona);
                    }
                }
                //                        lockRow(row);
                //                        $.each("td[data-y='"+row+"']",function(){
                //                            $("#demo4 .jexcel_content table tbody tr td[data-y='"+row+"']").addClass('readonly');
                //                        });
                return true;
                //                    }
            } else {
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
        $(document).on("click", '#validate_wizard-next-0', function() {
            var columna_desde = $('#a_c_desde').val();
            var columna_hasta = $('#a_c_hasta').val();
            var filas = $('#a_nivel').val();
            console.log('columna desde ' + parseInt(columna_desde) + ' hasta ' + parseInt(columna_hasta));
            var cntnt = '<table class="table" style="font-size:9px !important; width:80% !important; margin: 0 auto !important;">';
            cntnt += '<thead>';
            cntnt += '<tr>';
            cntnt += '<th></th>';
            for (i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++) {
                cntnt += '<th id="col_alm_' + i + '">' + i + '</th>';
            }
            cntnt += '</tr>';
            cntnt += '</thead>';
            cntnt += '<tbody>';

            for (j = 1; j <= parseInt(filas); j++) {
                cntnt += '<tr>';
                cntnt += '<td id="row_alm_' + j + '">' + j + '</td>';
                for (i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++) {
                    cntnt += '<td id="coord_alm_' + i + '_' + j + '">' + 0 + '</td>';
                }
                cntnt += '</tr>';
            }
            cntnt += '</tbody>';
            cntnt += '</table>';
            var cntntsb = '<table class="table" style="font-size:9px !important; width:80% !important; margin: 0 auto !important;">';
            cntntsb += '<thead>';
            cntntsb += '<tr>';
            cntntsb += '<th></th>';
            for (i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++) {
                cntntsb += '<th id="col_sub_' + i + '">' + i + '</th>';
            }
            cntntsb += '</tr>';
            cntntsb += '</thead>';
            cntntsb += '<tbody>';

            for (j = 1; j <= parseInt(filas); j++) {
                cntntsb += '<tr>';
                cntntsb += '<td id="row_sub_' + j + '">' + j + '</td>';
                for (i = parseInt(columna_desde); i <= parseInt(columna_hasta); i++) {
                    cntntsb += '<td id="coord_sub_' + i + '_' + j + '">' + 0 + '</td>';
                }
                cntntsb += '</tr>';
            }
            cntntsb += '</tbody>';
            cntntsb += '</table>';
            $('#vw_table').empty().append(cntnt);
            $('#vw_table_sub').empty().append(cntntsb);
        });
        $(document).ready(function() {
            $('#repor3').addClass('active');
            $('#c9').click();
            var rowdeleted = function(el, rowNumber, numOfRows) {
                //                
                var data = $('#demo1').jexcel('getRowData', rowNumber);
                var r = confirm(`Desea borrar el registro de la ubicacion ${data[1]} del material ${data[0]}?`);
                if (r) {
                    $.ajax({
                        type: 'POST',
                        url: '/wmsd/api/v1/eliminar_minimo_maximo.php',
                        data: {
                            ubicacion: data[1],
                            cod_articulo: data[0]
                        },
                        success: function(data) {
                            alert(data.mensaje);
                            return true;
                        },
                        error: function() {
                            console.log("error");
                            return false;
                        }
                    });
                }
            }
            $("#validate_wizard").submit(function() {
                return false;
            });
            t1 = jexcel(document.getElementById('demo1'), {
                //                $('#demo1').jexcel({ 
                data: dex,
                allowInsertColumn: false,
                allowInsertRow: true,
                allowDeletingAllRows: false,
                allowDeleteColumn: false,
                allowDeleteRow: true,
                tableOverflow: false,
                allowRenameColumn: false,
                copyCompatibility: false,
                minDimensions: [5, 1],
                onbeforedeleterow: rowdeleted,
                allowManualInsertColumn: false,
                colAlignments: ['left', 'left', 'left', 'left', 'left', 'left'],
                columns: [{
                        type: 'text'
                    },
                    {
                        type: 'text'
                    },
                    {
                        type: 'number'
                    },
                    {
                        type: 'number'
                    },
                    {
                        type: 'text'
                    },
                    {
                        type: 'text'
                    }
                ],
                //                    onchange: changed,  
                //                    onevent: onEvent,
                onbeforeinsertrow: beforeInsRow,
                colHeaders: ['Material', 'Ubicacion', 'Mínimo', 'Máximo', 'Centro', 'Almacen'],
                colWidths: [150, 150, 150, 150, 150, 150],
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
        });
    </script>
</body>

</html>