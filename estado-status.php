<?php
require ('conect.php');
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$fl = 0;
$data_usr = $totales = array();
$db = new mysqli($SERVER,$USER,$PASS,$DB);
if(isset($_POST['dFecCre']) && isset($_POST['hFecCre'])){
    if(($_POST['dFecCre'] != '') && ($_POST['hFecCre'] != '')){
        if(strtotime($_POST['dFecCre']) < strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
            $dt2 = date('Y-m-d', strtotime($_POST['hFecCre']));
           $dtd1 =  " and date(ct_ts) BETWEEN  '{$dt1}' AND  '{$dt2}' ";
        }
        if(strtotime($_POST['dFecCre']) == strtotime($_POST['hFecCre'])){
            $dt1 = date('Y-m-d', strtotime($_POST['dFecCre']));
           $dtd1 =  " and date(ct_ts) = '{$dt1}' ";
        }
        $fl = 1;
    }
}
//var_dump($_POST["selCto"]);
if(isset($_POST["selCto"])){
    $usrs = " and ct_user IN ( ";
    foreach($_POST["selCto"] as $k => $v){
        if($k == 0){
            $usrs .= "'".trim($v)."'";
        }else{
            $usrs .= ", '".trim($v)."'";
        }
    }
    $usrs .=  ")";
    $fl = 1;
}
if($fl == 1){
    $sq = "SELECT distinct ct_empaque, lg_c_mat, lg_c_mat_desc, lg_c_cant, ct_user, ct_ts  FROM log_cierre inner join log_material on ct_empaque = lg_c_emp where 1=1 {$dtd1} {$usrs}";
    //echo $sq;
    $rs = $db->query($sq);
    while($row = $rs->fetch_assoc()){
        $data_usr[$row['ct_user']][$row['ct_empaque']][$row['lg_c_mat']]['total']+=(int)$row['lg_c_cant'];
        $data_usr[$row['ct_user']][$row['ct_empaque']][$row['lg_c_mat']]['desc']=$row['lg_c_mat_desc'];
        $data_usr[$row['ct_user']][$row['ct_empaque']][$row['lg_c_mat']]['fech']=$row['ct_ts'];
    }
}



foreach($data_usr as $usr => $dat){
    $totales[$usr]['pedidos'] = count($dat);
    foreach($dat as $pedido => $dat2){
        $totales[$usr]['materiales'] += count($dat2);
        foreach($dat2 as $cantidad => $dat3){
            $totales[$usr]['cantidad'] += $dat3['total'];
        }
    }
}
//echo "<pre>";var_dump($totales);echo "</pre>";
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            td.details-control {
                background: url('images/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.shown td.details-control {
                background: url('images/details_close.png') no-repeat center center;
            }
            .label {
                 color: #000; 
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
                                    . '<th colspan="3">Productividad por Empaquetador</th>'
                                    . '</tr>'
                                . '</thead>'
                            . '</table>';?>
                        <div class="col-lg-12 col-sm-12 former">
                            <form method="post" id="eForm">
                                <div style="clear:both;"></div>

                                <div class="col-lg-5 col-sm-5">
                                    <label class="label">Fecha Creación:</label><div style="clear:both;"></div><br/>
                                    <div class="input-group input-daterange">
                                        <input type="text" name="dFecCre" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["dFecCre"]?>"/>
                                        <div class="input-group-addon"> hasta </div>
                                        <input type="text" name="hFecCre" class="form-control" data-date-format="dd-mm-yyyy" placeholder="Seleccionar" value="<?php echo @$_POST["hFecCre"]?>"/>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2" >
                                    <div class="input-group">
                                        <label class="label">Clase de Doc.:</label><div style="clear:both;"></div><br/>
                                        <select class="form-control" id="selCto" name="selCto[]"  multiple="multiple">
                                            <?php
                                                $sq = "SELECT distinct pr_user, pr_nombre, pr_apellido FROM usuarios where pr_pass is not null or pr_pass <> ''";
    //                                            echo $sq;
                                                $rs = $db->query($sq);

                                                while($row = $rs->fetch_assoc()){
                                                    echo '<option value="'.$row['pr_user'].'">'.$row['pr_user'].' - '.$row['pr_nombre'].' '.$row['pr_apellido'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-sm-1">
                                    <label class="label" style="color:#b9cfe5 !important;">_________</label><div style="clear:both;"></div><br/>
                                    <div class="input-group">
                                        <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Buscar</button>
                                    </div>
                                </div>
                            </form> 
                        </div><div style="clear:both;"></div>
                    </div><br />
                    <div class="row">
                    <?php

                        echo '<table class="table">'
                            . '<thead>'
                                . '<tr>'
                                . '<th colspan="3"></th>'
                                . '</tr>'
                            . '</thead>'
                        . '</table>';?>
                        <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list2" style="font-size: 10px !important;">
                            <thead>
                                    <tr>
                                        <th>Empaquetador</th>
                                        <th>Ct. Pedido</th>
                                        <th>Ct. SKU</th>
                                        <th>Ct. Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($totales as $usu => $dat){
                                            echo "<tr>";
                                            echo "<td>".$usu."</td>";
                                            echo "<td>".$dat['pedidos']."</td>";
                                            echo "<td>".$dat['materiales']."</td>";
                                            echo "<td>".$dat['cantidad']."</td>";
                                            echo "</tr>";
                                        }
                                    ?>    
                                </tbody>    
                        </table>
                        <table class="table table-hover table-striped table-bordered  table-condensed dTableR" id="tbl_list" style="font-size: 9px !important;">
                            <thead>
                                    <tr>
                                        <th>Empaquetador</th>
                                        <th>Pedido</th>
                                        <th>Material</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($data_usr as $usr => $dat){
                                            foreach($dat as $pedido => $dat2){
                                                foreach($dat2 as $id => $dat3){
                                                    echo "<tr>";
                                                    echo "<td>".$usr."</td>";
                                                    echo "<td>".$pedido."</td>";
                                                    echo "<td>".$id."</td>";
                                                    echo "<td>".$dat3['desc']."</td>";
                                                    echo "<td>".$dat3['total']."</td>";
                                                    echo "<td>".$dat3['fech']."</td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        }
                                    ?>    
                                </tbody>
                        </table>
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
            function ckFields(){
                var flg = 0;
                if($("input[name='dFecCre']").val() == '' || $("input[name='dFecCre']").val() == ''){
                    alert("Favor insertar rango de fechas");
                    flg = 1;
                }
                if ($('#selCto option:selected').length == 0) { 
                    alert('Favor seleccionar almenos un usuario.'); 
                    flg = 1;
                }
//                alert(flg);
                if(flg == 0){
//                    alert('entro');
                    $("#eForm").submit();
                }
            }
            function swOC(oc,pd){
                $.ajax({ 
                    type: 'POST',
                    url: 'alertas_oc.php', 
                    data: {
                        oc:oc,
                        pd:pd
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        $("#ocCont").empty().append(dt.cntn);
                    }
                }); 
            }
            $(document).ready(function(){
                $('#eprod').addClass('active');
                $('#c1').click();
                $('.input-daterange input').datepicker({ dateFormat: 'dd-mm-yy' });
                table = $('#tbl_list').DataTable( {
                    "processing": true,
                    "serverSide": false,
                    "bFilter": true,
                   dom: '<"top"B<lfrtip>><"clear">',
                "sScrollX": "100%",
                "sScrollXInner": '110%',
                "sPaginationType": "bootstrap",
                "bScrollCollapse": true ,
                    buttons: [
                         'excel'
                    ],
                    "bInfo": true,
                    "bLengthChange": true,
                    "destroy": true,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
                    "language": {
                        "processing":     "Procesando...",
                        "lengthMenu":     "Mostrar _MENU_ registros",
                        "zeroRecords":    "No se encontraron resultados",
                        "emptyTable":     "Ningún dato disponible en esta tabla",
                        "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
                        "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "infoPostFix":    "",
                        "search":         "Buscar:",
                        "Url":            "",
                        "infoThousands":  ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first":    "Primero",
                            "last":     "Último",
                            "next":     "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
    //                300000
                });
                table2 = $('#tbl_list2').DataTable( {
                    "processing": true,
                    "serverSide": false,
                    "bFilter": true,
                   dom: '<"top"B<lfrtip>><"clear">',
                "sScrollX": "100%",
                "sScrollXInner": '110%',
                "sPaginationType": "bootstrap",
                "bScrollCollapse": true ,
                    buttons: [
                         'excel'
                    ],
                    "bInfo": true,
                    "bLengthChange": true,
                    "destroy": true,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
                    "language": {
                        "processing":     "Procesando...",
                        "lengthMenu":     "Mostrar _MENU_ registros",
                        "zeroRecords":    "No se encontraron resultados",
                        "emptyTable":     "Ningún dato disponible en esta tabla",
                        "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
                        "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "infoPostFix":    "",
                        "search":         "Buscar:",
                        "Url":            "",
                        "infoThousands":  ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first":    "Primero",
                            "last":     "Último",
                            "next":     "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
    //                300000
                });
                $( "#selCodExp" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
                
                $( "#selCto" ).multiselect({
                    selectAllText: 'Todos',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonText: function(options) {
                                if (options.length === 0) {
                                    return 'Ninguno';
                                }
                                else if (options.length > 0) {
                                    return options.length + ' selecionado(s)';
                                }
                            }
                });
                <?php
                    if(isset($_POST["selCto"])){
                        foreach($_POST["selCto"] as $k => $v){
                            echo '$( "#selCto" ).multiselect('."'".'select'."'".', "'.$v.'");';
                        }
                    }
                ?>
//               
//                $('#selCompMot option:selected').each(function() {
//                    mot.push($(this).val());
//                });
            });
            function loadLilTable3(){
                   table = $('#mdlTableTL23').DataTable( {
                        "processing": true,
                        "serverSide": false,
                        "bFilter": true,
                        dom: '<"top"B<lfrtip>><"clear">',
                        buttons: [
                            'csv', 'excel'
                        ],
                        "bInfo": true,
                        "bLengthChange": true,
                        "destroy": true,
                        "lengthMenu": [[25, 50, -1], [25, 50, "Todo"]],
                        "language": {
                            "processing":     "Procesando...",
                            "lengthMenu":     "Mostrar _MENU_ registros",
                            "zeroRecords":    "No se encontraron resultados",
                            "emptyTable":     "Ningún dato disponible en esta tabla",
                            "info":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                            "infoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
                            "infoFiltered":   "(filtrado de un total de _MAX_ registros)",
                            "infoPostFix":    "",
                            "search":         "Buscar:",
                            "Url":            "",
                            "infoThousands":  ",",
                            "loadingRecords": "Cargando...",
                            "paginate": {
                                "first":    "Primero",
                                "last":     "Último",
                                "next":     "Siguiente",
                                "previous": "Anterior"
                            },
                            "aria": {
                                "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
                                "sortDescending": ": Activar para ordenar la columna de manera descendente"
                            }
                        }
        //                300000
                    });
                }
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
