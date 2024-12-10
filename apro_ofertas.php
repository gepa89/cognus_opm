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
            td.dt-nowrap { white-space: nowrap }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <h3 class="heading">Pedido de Oferta</h3>
                            <div class="row">
                                <div id="cntnt_in">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    <?php 
        include 'sidebar_pd.php';
        include 'js_in.php';
    ?>
        <script>
            function currencyFormatPY(num) {
                return (
                  num
                    .toFixed(0) // always two decimal digits
                    .replace('.', ',') // replace decimal point character with ,
                    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
                ) // use . as a separator
            }
            $(document).on('keyup', '#btnBuscar', function() {
                console.log($(this).val());
                loadDataSearch($(this).val());
            });
            $(document).ready(function() {$('#mped').addClass('active');
                loadData();
            });
            function ldPedido(id){
            
            $.ajax({ 
                url: 'get_pedido.php', 
                type: 'POST',
                data: {
                    id:id
                },
                success: function (data) {
//                    alert("entro");
                    var ids = [];
                    var pedido = '';
                    var cntnt = '';
                    var cntnt2 = cntnt3 = '';
                    var dt = JSON.parse(data);
                    $.each(dt.pedidos, function(idx, obj){
                        if(obj.c_oferta > 0 && obj.o_aprobada < 1){
                            pedido = idx;
                             cntnt += ' <div class="col-lg-12 col-sm-12 col-xs-12">';
                            cntnt += '<h2>Pedido #'+idx+'</h2>\n\
                                \n\<h2><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.69em !important;">'+dt.datos[idx].usr+'</i><i class="pull-right" style="right:4%; widht:49%!important; position: absolute !important; !important;text-align:right;font-size: 0.69em !important;">Creado: '+dt.datos[idx].fecha+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">Cta.: '+dt.datos[idx].cuenta+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">Des.: '+dt.datos[idx].descuenta+'</i> <br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">CeCo: '+dt.datos[idx].ceco+'</i> <br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">Cta.: '+dt.datos[idx].desceco+'</i> <br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">O. Interna: '+dt.datos[idx].ordint+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; !important;font-size: 0.5em !important;">Cta.: '+dt.datos[idx].desordenin+'</i> <br/></h2>\n\
\n\
                                        <p></p>\n\
                                    <div class="line"></div>';
                                            cntnt += ' </div>';
                        }                        
                    });
                    cntnt2 += '<br/>';
                    cntnt2 += '<div class="col-lg-12 col-sm-12 col-xs-12 cntnt_out_box ">';
                    
                    $.each(dt.ofertas, function(idx, obj){      
                        
                        $.each(obj, function(idx2, obj2){
                            cntnt2 += '<div class="col-lg-4 col-sm-12 col-xs-12" style="background-color: #ccc !important; border-radius: 5px; margin-bottom: 5px; margin-right: 5px; padding-bottom: 17px;">';
                            if(dt.d_ofertas.hasOwnProperty(idx2)){
                                var links = get_attachment(idx2);
                                var total = 0;
                                cntnt2 += '<br/><br/>';
                                cntnt2 += '    <div class="">';
                                cntnt2 += '    <table class="table table-striped table-sm display nowrap ">';
                                cntnt2 += '    <thead>';

                                cntnt2 += '            <tr>';
                                cntnt2 += '                <th colspan=""><b>Oferta #'+idx2+'</b>  </th>';
                                cntnt2 += '            </tr>';
                                cntnt2 += '            <tr>';
                                cntnt2 += '                <th colspan=""><h6><i>Prov.: '+dt.provs[idx2]+'</i></h6></th>';
                                cntnt2 += '            </tr>';
                                cntnt2 += '    </thead>';
                                cntnt2 += '    </table>';
                                cntnt2 += '    </div>';
                                
                                cntnt2 += '    <div class="col-lg-12 col-sm-12 col-xs-12"  style="background-color: #eee !important;">';
                                cntnt2 += '    <table class="table table-striped table-sm display nowrap" id="tbl'+idx2+'">';
                                ids.push(idx2);
                                cntnt2 += '    <thead>';
                                cntnt2 += '            <tr>';
                                cntnt2 += '                <th class="all"></th><th class="all" style=""><b>Descripción</b></th><th class="none" style="font-size:11px;text-align:right"><b>Cant.</b></th><th class="none" style="font-size:11px;text-align:right"><b>Moneda</b></th><th class="none" style="font-size:11px;text-align:right"><b>Unitario S/ Desc.</b></th><th class="none" style="font-size:11px;text-align:right"><b>S/ Desc.</b></th><th class="none" style="font-size:11px;text-align:right"><b>Unitario C/ Desc.</b></th><th class="none" style="font-size:11px;text-align:right"><b>C/ Desc.</b></th><th class="none" style="font-size:11px;text-align:right"><b>%</b></th>';
                                cntnt2 += '            </tr>';
                                cntnt2 += '    </thead>';
                                cntnt2 += '        <tbody>';
                                $.each(dt.d_ofertas[idx2], function(idx3, obj3){
//                                    console.log(Number(obj3.RAW));
                                    total += Number(obj3.RAW);
                                    cntnt2 += '       <tr id="cklbl-'+idx2+'"><td></td><td style="font-size:12px;text-align:left;white-space: nowrap;">'+obj3.DESC+'</td><td style="font-size:10px;text-align:right">'+obj3.CANT+'</td><td style="font-size:10px;text-align:right">'+obj3.MON+'</td><td style="font-size:10px;text-align:right">'+obj3.MAYORU+'</td><td style="font-size:10px;text-align:right">'+obj3.MAYOR+'</td><td style="font-size:10px;text-align:right">'+obj3.MENORU+'</td><td style="font-size:10px;text-align:right">'+obj3.MENOR+'</td><td style="font-size:10px;text-align:right">'+obj3.PORC+'</td></tr>';
                                    
                                }); 
                                
    //                            console.log("salgo");
                                cntnt2 += '        </tbody>';
                                cntnt2 += '    </table>';
                                cntnt2 += '        <table class="table table-striped table-sm"><tbody>';
                                cntnt2 += '       <tr><td colspan="6" style="text-align:left;">Total</td><td colspan="1" style="font-size:11px;text-align:right">'+currencyFormatPY(total)+'</td><td colspan="1" style="font-size:11px;text-align:left"></td></tr>';
                                cntnt2 += '       <tr><td colspan="6"></td><td colspan="2" style="font-size:11px;text-align:left"></td></tr>';
                                cntnt2 += '       <tr><td colspan="6" style="font-size: 25px !important;">'+links+'</td><td colspan="2" style="font-size:11px;text-align:left"></td></tr>';
                                cntnt2 += '       <tr><td colspan="1"></td><td colspan="3" style="font-size: 25px !important;"> </td><td colspan="1"><button type="button" onclick="fnAprobar('+"'"+pedido+"'"+','+"'"+idx2+"',"+"'<?php echo $_SESSION['user']?>'"+''+')" class="btn btn-success pull-right">Aprobar</button></td></tr>';
//                                cntnt2 += '       <tr><td colspan="1"></td><td colspan="3" style="font-size: 25px !important;"> </td><td colspan="1"><button type="button" onclick="fnRechazar('+"'"+pedido+"'"+','+"'"+idx2+"',"+"'<?php echo $_SESSION['user']?>'"+''+')" class="btn btn-success pull-right">Rechazar</button></td></tr>';
                                cntnt2 += '        </tbody></table>';
                                cntnt2 += '    </div>';
                            }
                            cntnt2 += '    <div class="clear:both;"></div>';
                            cntnt2 += '</div>';
                        });       
                        
                    });
                    <!--<button type="button" onclick="fnRechazar('+"'"+pedido+"'"+','+"'"+idx2+"',"+"'<?php echo $_SESSION['user']?>'"+''+')" class="btn btn-danger pull-left">Rechazar</button>-->
                    cntnt2 += '</div>';
                    
                    cntnt2 += '<div class="line"></div>';
                    $("#cntnt_in").empty().append(cntnt+cntnt2);
//                    console.log(ids);
                    for( var i = 0; i < ids.length; i++){
                        loadTable('tbl'+ids[i]);
                    }
                }
            }); 
        }
        function loadTable(id){
//                console.log(table);
           table = $('#'+id).DataTable({
               "responsive": {
                    "details": {
                        type: 'column'
                    }
                },
                columnDefs: [ 
                    
                { responsivePriority: 1, targets: 6 },
                {
                    className: 'control',
                    orderable: false,
                    targets:   0
                } ],                    
                "processing": true,             
                "autoWidth": false,
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
        function down_file(id){
            window.location = 'login.php?if='+id;
        }
        function get_attachment(of){
            var lks = '';
            $.ajax({ 
                url: 'ck_att_2.php', 
                type: 'POST',
                data: {
                    of:of
                },
                async: false,
                success: function (data) {
                    var dt = JSON.parse(data);
                    
                    if(dt.files){
                       $.each(dt.files, function(idx, obj){
                           switch(obj.file_ext){
                                case 'pdf':
                                    lks += ' <a target="_blank" title="PDF" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-pdf"></span></a>';
                                   break;
                                case 'xls':
                                    lks += ' <a target="_blank" title="Excel" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-excel"></span></a>';
                                   break;
                                case 'doc':
                                    lks += ' <a target="_blank" title="Word" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-word"></span></a>';
                                   break;
                                case 'docx':
                                    lks += ' <a target="_blank" title="Word" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-word"></span></a>';
                                   break;
                                case 'xlsx':
                                    lks += ' <a target="_blank" title="Excel" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-excel"></span></a>';
                                   break;
                                case 'png':
                                    lks += ' <a target="_blank" title="Imagen" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-image"></span></a>';
                                   break;
                                case 'jpg':
                                    lks += ' <a target="_blank" title="Imagen" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-image"></span></a>';
                                   break;
                                case 'bmp':
                                    lks += ' <a target="_blank" title="Imagen" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file-image"></span></a>';
                                   break;
                                default:
                                    lks += ' <a target="_blank" title="Archivo" href="get_att.php?id='+obj.file_id+'"><span class="fas fa-file"></span></a>';
                                   break;
                           }
                       });
                       
                    }
                }
            });
            return lks;
        }
        function fnAprobar(pd, of, usr){
//            alert("aprobar "+pd+" "+of);
            $.ajax({ 
                url: 'rqAprobacion.php', 
                type: 'POST',
                data: {
                    pd:pd,
                    of:of,
                    usr:usr
                },
                success: function (data) {
                    var dt = JSON.parse(data);
                    if(dt.err == 1){
                        alert(dt.msg);
                    }else{                        
                        alert(dt.msg);
                         window.location = 'pedidos.php';
                    }  
                }
            });
        }
        function loadData(){
            $.ajax({ 
                url: 'get_pedido.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = dt.total.total;
                    if(dt.pedidos){
                        $.each(dt.pedidos, function(idx, obj){
        //                        $.each(dt.pedidos, function(idx, obj){
        //                            if(obj.c_oferta > 0 && obj.o_aprobada < 1){
        //                                cntnt += '<li><a href="'+idx+'">'+idx+'<br/><p>'+dt.dt_ofertas[idx].usr+'</p></a></li>';
        //                            }                        
        //                        });
                            if(obj.c_oferta > 0 && obj.o_aprobada < 1){
                                cntnt += '<li><a href="javascript:void(0);" onclick="ldPedido('+"'"+idx+"'"+')"><b>'+idx+'</b><i class="pull-right" style="right:14px; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">'+dt.datos[idx].fecha+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.69em !important;">'+dt.datos[idx].usr+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.9em !important;">'+dt.datos[idx].n_op+'</i></a></li>';
                            }                        
                        });
                    }
                    $("#cntr").empty().append(ts);
                    $(".lst_cntnt").empty().append(cntnt);
                }
            }); 
        };
        function loadDataSearch(arg){
            $.ajax({ 
                url: 'get_pedido.php', 
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
                        $.each(dt.pedidos, function(idx, obj){
        //                        $.each(dt.pedidos, function(idx, obj){
        //                            if(obj.c_oferta > 0 && obj.o_aprobada < 1){
        //                                cntnt += '<li><a href="'+idx+'">'+idx+'<br/><p>'+dt.dt_ofertas[idx].usr+'</p></a></li>';
        //                            }                        
        //                        });
                            if(obj.c_oferta > 0 && obj.o_aprobada < 1){
                                cntnt += '<li><a href="javascript:void(0);" onclick="ldPedido('+"'"+idx+"'"+')"><b>'+idx+'</b><i class="pull-right" style="right:14px; widht:49%!important; position: absolute !important; display:inline-block !important;text-align:right;font-size: 0.69em !important;">'+dt.datos[idx].fecha+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.69em !important;">'+dt.datos[idx].usr+'</i><br/><i class="pull-left" style="widht:49%!important; position: relative !important; display:inline-block !important;font-size: 0.9em !important;">'+dt.datos[idx].n_op+'</i></a></li>';
                            }                        
                        });
                    }
                    $("#cntr").empty().append(ts);
                    $(".lst_cntnt").empty().append(cntnt);
                }
            }); 
        };
            
            
        </script>
    </body>
</html>
