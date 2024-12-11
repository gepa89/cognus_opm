<?php // $shell = true;
//phpinfo();
require_once("hanaDB.php");
ini_set('memory_limit', '1024M');


$oci_qry = "SELECT distinct a.EXPORDREF, a.MUEEXP, c.ESTDES FROM adaia.EXPORDCAB a
left join adaia.ALMUBI b ON a.MUEEXP = b.ESTCOD
left join adaia.ALMPRV c ON b.ALMUBICOD = c.ALMUBICOD
WHERE a.ALMCOD = '2'
AND a.EXPORDSIT = 'CE'
AND a.EXPORDTIP IN ('NORM', 'TRAS')
AND a.FECORD >= trunc(sysdate)
   And a.FECORD < trunc(sysdate) + 1";
$dt = oci_parse($conn2, $oci_qry);
oci_execute($dt);
$tt = 0;

while($row = oci_fetch_array($dt, OCI_ASSOC+OCI_RETURN_NULLS)){
    $muelles[str_pad((int)trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT)]['cod'] = trim($row["MUEEXP"]);
    $muelles[str_pad((int)trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT)]['desc'] = trim($row["ESTDES"]);
    $data[] = str_pad((int)trim($row["EXPORDREF"]), 10, '0', STR_PAD_LEFT);
    $tt++;
}
oci_free_statement($dt);
oci_close($prd);
$sqhan = "select distinct vblen, lddat from sapabap1.ZMM_BULTOS where lddat = TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )";
//                echo $sqhan;
$rst = odbc_exec($prd, $sqhan);
$t_pend = 0;
while ($rw = odbc_fetch_object($rst)){
    $cod = str_pad($rw->VBLEN, 10, '0', STR_PAD_LEFT);
//    echo $cod;
    $data2[] = str_pad((int)trim($cod), 10, '0', STR_PAD_LEFT);
}

foreach($data as $k => $v){
    if(!in_array($v, $data2)){
        $pendientes[] = $v;
        $t_pend++;
    }
}


$cc = $ct = 0;
foreach($pendientes as $k => $v){
//    echo $v."<br/>";
    if((int)$v >= 4800000000){
//        echo $v;
//        $spd['transferencia'][] = $v;
        $sqh = "select distinct a.vsbed, f.vtext from sapabap1.likp a
                    left join sapabap1.tvsbt f on f.vsbed = a.vsbed and f.spras = 'S' and f.mandt = '300'
                 where vbeln = '{$v}'";
//        echo $sqh."<br/>";
        $rt = odbc_exec($prd, $sqh);
        $ax = odbc_fetch_object($rt);
        $spd['transferencia'][$ct]['cod'] = $v;
        $spd['transferencia'][$ct]['desc'] = $ax->VTEXT;
        $spd['transferencia'][$ct]['muelle'] = $muelles[$v]['desc'];
        $ct++;
    }else{
        $sqh = "select distinct a.vsbed, f.vtext from sapabap1.likp a
                    left join sapabap1.tvsbt f on f.vsbed = a.vsbed and f.spras = 'S' and f.mandt = '300'
                 where vbeln = '{$v}'";
//        echo $sqh."<br/>";
        $rt = odbc_exec($prd, $sqh);
        $ax = odbc_fetch_object($rt);
        if(!isset($ax->VSBED)){
            $spd['entrega']['Sin Franja'][$cc]['cod'] = $v;
            $spd['entrega']['Sin Franja'][$cc]['desc'] = $ax->VTEXT;
            $spd['entrega']['Sin Franja'][$cc]['muelle'] = $muelles[$v]['desc'];
        }else{
            $spd['entrega'][$ax->VSBED."-".$ax->VTEXT][$cc]['cod'] = $v;
            $spd['entrega'][$ax->VSBED."-".$ax->VTEXT][$cc]['desc'] = $ax->VTEXT;
            $spd['entrega'][$ax->VSBED."-".$ax->VTEXT][$cc]['muelle'] = $muelles[$v]['desc'];
        }
        
//        var_dump($ax);
        $cc++;
    }
}
//echo "<pre>";var_dump($spd['transferencia']);echo "</pre>";
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
                        <div class="row">
                            <div class="col-sm-6 col-md-6">
                                <h3 class="heading">Pedidos pendientes - Total: <?php echo ''.$t_pend.''?></h3>
                                <div id="accordion1" class="panel-group accordion">
                                    <?php 
                                    $cct = 0;
                                    ksort($spd['entrega']);
                                        foreach($spd['entrega'] as $k => $fr ){
                                    ?>
                                        <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <a href="#collapse<?php echo $cct;?>" data-parent="#accordion1" data-toggle="collapse" class="accordion-toggle collapsed">
                                                                <?php echo $k . ' ('.count($fr).')';?>
                                                        </a>
                                                </div>
                                                <div class="panel-collapse collapse" id="collapse<?php echo $cct;?>">
                                                        <div class="panel-body">
                                                                <table class="table table-hover table-striped table-bordered  table-condensed dTableR"  style="font-size: 12px !important;">
                                                                <thead>
                                                                        <tr>
                                                                            <th>Pedido</th>
                                                                            <th>Muelle</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php 
                                                                            foreach($fr as $k2 => $frs ){
                                                                                echo "<tr>";
                                                                                echo "    <th>".$frs['cod']."</th>";
                                                                                echo "    <th>".$frs['muelle']."</th>";
                                                                                echo "</tr>";
                                                                            }
                                                                        ?>    
                                                                    </tbody>
                                                            </table>
                                                        </div>
                                                </div>
                                        </div>
                                    <?php 
                                            $cct++;
                                        }
                                        ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <a href="#collapseTrans" data-parent="#accordion1" data-toggle="collapse" class="accordion-toggle collapsed">
                                                Transferencias (<?php echo count($spd['transferencia']);?>)
                                            </a>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseTrans">
                                            <div class="panel-body">
                                                    <table class="table table-hover table-striped table-bordered  table-condensed dTableR"  style="font-size: 12px !important;">
                                                    <thead>
                                                        <tr>
                                                            <th>Transferencia</th>
                                                            <th>Muelle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
//                                                                var_dump($spd['transferencia']);
                                                            foreach($spd['transferencia'] as $k => $fr ){
                                                                echo "<tr>";
                                                                echo "    <th>".$fr['cod']."</th>";
                                                                echo "    <th>".$fr['muelle']."</th>";
                                                                echo "</tr>";
                                                            }
                                                        ?>    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12">
                                    <h3 class="heading">Entregas</h3>
                                    <div id="gr_8" style="height:380px;width:100%;"></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12">
                                    <h3 class="heading">Transferencias</h3>
                                    <div id="gr_9" style="height:380px;width:100%;"></div>
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
//        include 'js_fn.php';
    ?>
        <script type="text/javascript">
            function ckFields(){
                var flg = 0;
                
                if($("input[name='desArt']").val() == ''){
                    if($("input[name='dFecCre']").val() == '' || $("input[name='dFecCre']").val() == ''){
                        alert("Favor insertar rango de fechas");
                        flg = 1;
                    }
                    if ($('#selCto option:selected').length == 0) { 
                        alert('Favor seleccionar almenos un usuario.'); 
                        flg = 1;
                    }
                }
                
//                alert(flg);
                if(flg == 0){
//                    alert('entro');
                    $("#eForm").submit();
                }
            }
            function loadHCPieEntrega(){
//                console.log('llamado');
               $.ajax({ 
                    url: 'adaia_pedidos_ent.php', 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        console.log(dt);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        $('#gr_8').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: [{
                                    name: 'Controlados ['+ dt.totalp+']',
                                    y: dt.totalp
                                }, {
                                    name: 'Pendientes ['+ dif +']',
                                    y: dif
                                }]
                            }]
                        });
                    }
                });
//                
            }
            function loadHCPieTransf(){
//                console.log('llamado');
               $.ajax({ 
                    url: 'adaia_pedidos_tra.php', 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        console.log(dt);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        $('#gr_9').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: [{
                                    name: 'Controlados ['+ dt.totalp+']',
                                    y: dt.totalp
                                }, {
                                    name: 'Pendientes ['+ dif +']',
                                    y: dif
                                }]
                            }]
                        });
                    }
                });
//                
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
                $('#epend').addClass('active');
                loadHCPieEntrega();
                loadHCPieTransf();
                setInterval(function(){
//                    loadHCPieEntrega();
//                    loadHCPieTransf();
                    window.location = 'estado-pendientes.php';
                },300000);
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
