<?php
require ('conect.php');
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}

//$db = new mysqli($SERVER,$USER,$PASS,$DB);

//$sq = "select * from log_contrato where prt_prov = '{$_SESSION['user_cd']}' and prt_status = 0";
//echo $sq;
//$rs = $db->query($sq);
//$tt = $rs->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            td.details-control {
                background: url('images/details_open.png') no-repeat center center;
                cursor: pointer;
                width: 28px !important;
            }
            tr.shown td.details-control {
                background: url('images/details_close.png') no-repeat center center;
            }
            /*.dataTables_wrapper{position:relative;overflow: visible !important;}*/

        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <h3 class="heading">Solicitudes</h3>
                            <div class="row">
                                <div id="cntnt_in">
                                </div>
                                <div id="cntnt_in2">
                                    <div class="col-lg-12 col-sm-12 col-xs-12">
                                        <table class="table table-striped table-sm" id="tblSolPed">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                <th><b>Posición</b></th>
                                                 <th><b>Descripcion</b></th>
                                                 <th><b>Cant.</b></th>
                                                 <th><b>Cuenta</b></th>
                                                  <th><b>CeCo</b></th>
                                                 <th><b>O. Interna</b></th>
                                                  <th><b>Precio</b></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>                                    
                                </div>
                                <div id="cntnt_in3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    <?php 
        include 'sidebar_sl.php';
        include 'js_in.php';
    ?>
        <script>
                            var table = $('#tblSolPed').DataTable();

            $(document).on('keyup', '#btnBuscar', function() {
                console.log($(this).val());
                loadDataSearch($(this).val());
            });
            $(document).ready(function(e) {
                e.preventDefault; // prevent form submission
    //            alert("entro");
                loadData();
            });
            function loadTable(id){
//                console.log(table);
                table.destroy();
               table = $('#'+id).DataTable({
                   "responsive": {
                        "details": {
                            type: 'column'
                        }
                    },
                    columnDefs: [ {
                        className: 'control',
                        orderable: false,
                        targets:   0
                    } ],                    
                    "processing": true,             
                    "autoWidth": true,
                    "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "Todo"]],
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
//                    ,
//                    "order": [[1, 'asc']]
                } );
            }
            function currencyFormatPY(num) {
                return (
                  num
                    .toFixed(0) // always two decimal digits
                    .replace('.', ',') // replace decimal point character with ,
                    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
                ) // use . as a separator
            }

            function ldPedido(id){

                $.ajax({ 
                    url: 'get_solicitud.php', 
                    type: 'POST',
                    data: {
                        id:id
                    },
                    success: function (data) {
    //                    alert("entro");
                        var pedido = '';
                        var cntnt = cntntbtn = '';
                        var cntnt2 = '';
                        var dt = JSON.parse(data);
                        $.each(dt.pedidos, function(idx, obj){
                                pedido = idx;
                                cntnt += '<div class="col-lg-12 col-sm-12 col-xs-12">';
                                cntnt += '<h2>Solicitud #'+idx+'</h2>\n\
                                    \n\<h3><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.69em !important;">'+dt.datos[idx].usr_name+'</i><i class="pull-right" style="right:4%; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">Creado: '+dt.datos[idx].fecha+'</i><br/><i class="pull-right" style="right:4%; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">Moneda: <b>'+dt.datos[idx].moneda+'</b></i></h3>\n\
    \n\
                                            <p></p>\n\
                                       <div class="line"></div>';
                                cntnt += '</div">';
                        });
                        
                        
                                    cntnt2 += '    <thead>';
                                    cntnt2 += '            <tr>';
                                    cntnt2 += '                <th></th><th style="width:25px !important;font-size:15px;text-align:left"><b>Posición</b></th>\n\
                                                                <th style="font-size:15px;text-align:left"><b>Descripcion</b></th>\n\
                                                                <th style="font-size:15px;text-align:left"><b>Cant.</b></th>\n\
                                                                <th style="font-size:15px;text-align:left"><b>Cuenta</b></th>\n\
                                                                <th style="font-size:15px;text-align:left"><b>CeCo</b></th>\n\
                                                                <th style="font-size:15px;text-align:left"><b>O. Interna</b></th>';
                                    cntnt2 += '                 <th style="font-size:15px;text-align:left"><b>Precio</b></th>';    
                                    cntnt2 += '            </tr>'
                                    cntnt2 += '    </thead>';
                                    cntnt2 += '        <tbody>';
                                    $.each(dt.d_posicion, function(idx2, obj){
                                        cntnt2 += '       <tr id="cklbl-'+idx2+'"><td></td><td style="width:25px !important;font-size:12px;text-align:left">'+obj.pos+'</td><td style="font-size:13px;text-align:left">'+obj.desc+'</td><td style="font-size:13px;text-align:left">'+obj.cant+'</td><td style="font-size:13px;text-align:left">'+obj.cta+'</td><td style="font-size:13px;text-align:left">'+obj.ceco+'</td><td style="font-size:13px;text-align:left">'+obj.oi+'</td><td style="font-size:13px;text-align:left">'+obj.pre+'</td></tr>';
                                    });                                     
                                    cntnt2 += '        </tbody>';
                                    cntnt2 += '    </table>';
                                    console.log(cntnt2);
                                    cntntbtn = '       <div class="col-lg-12 col-sm-12 col-xs-12"><tr><td colspan="1"></td><td colspan="7" style="font-size:11px;text-align:left"></td></tr>\n\
                                    <tr><td colspan="1"><button type="button" onclick="fnAprobar('+"'"+pedido+"'"+","+"'<?php echo $_SESSION['user']?>'"+''+')" class="btn btn-outline-success pull-left">Aprobar</button></td><td colspan="1"></td><td colspan="6" style="font-size:11px;text-align:left"></td></tr></div>';
                        $("#cntnt_in").empty().append(cntnt);
                        $("#tblSolPed").empty().append(cntnt2);
                        $("#cntnt_in3").empty().append(cntntbtn);
                        loadTable('tblSolPed');
                    }
                }); 
            }
            function fnAprobar(pd, usr){
                var r = confirm('Desea aprobar el pedido '+pd);
                if(r == true){
                    $.ajax({ 
                        url: 'sndPedido.php', 
                        type: 'POST',
                        data: {
                            pd:pd,
                            usr:usr
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            if(dt.err == 1){
                                alert(dt.msg);
                            }else{                        
                                alert(dt.msg);
                                 window.location = 'solicitudes.php';
                            }  
                        }
                    });
                }

            }
            function loadData(){
            $.ajax({ 
                url: 'get_solicitud.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = ""+dt.total.total+"";
                    if(dt.pedidos){
                        $.each(dt.pedidos, function(idx){
                            console.log(idx);
                            cntnt += '<li><a href="javascript:void(0);" onclick="ldPedido('+"'"+idx+"'"+')"><b>'+idx+'</b><i class="pull-right" style="right:14px; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">'+dt.datos[idx].fecha+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.69em !important;">'+dt.datos[idx].usr+'</i></a></li>';
                        });
                    }
                    $("#cnpd").empty().append(ts);
                    $(".lst_cntnt").empty().append(cntnt);
                }
            }); 
        };
            function loadDataSearch(arg){
                $.ajax({ 
                    url: 'get_solicitud.php', 
                    type: 'POST',
                    data: {
                        arg:arg
                    }, 
                    success: function (data) {
    //                    alert("entro");
                        var cntnt = '';
                        var cntnt2 = '';
                        var dt = JSON.parse(data);
                        var ts = ""+dt.total.total+"";
                        if(dt.pedidos){
                            $.each(dt.pedidos, function(idx){
                                console.log(idx);
                                cntnt += '<li><a href="javascript:void(0);" onclick="ldPedido('+"'"+idx+"'"+')"><b>'+idx+'</b><i class="pull-right" style="right:14px; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">'+dt.datos[idx].fecha+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.69em !important;">'+dt.datos[idx].usr+'</i></a></li>';
                            });
                        }
                       $("#cnpd").empty().append(ts);
                    $(".lst_cntnt").empty().append(cntnt);
                    }
                }); 
            };  
        </script>
    </body>
</html>
