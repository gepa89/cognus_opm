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
        </style>
        <div id="maincontainer" class="clearfix">
            <?php
                $swChPsw = '';
//                var_dump($_SESSION);
                if($_SESSION["user_pss_upd"]==0){
                    $swChPsw = "cngPwd()";
                }

            ?>
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <h3 class="heading">Herramientas</h3>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-xs-12">
                                        <ul class="dshb_icoNav clearfix">
                                                <li><a href="nuevo_pedido.php" style="background-image: url(img/gCons/add-item.png)">Crear Pedido</a></li>
                                                <li><a href="recepcion.php" style="background-image: url(img/gCons/edit.png)"><span id="cnsol" class="label label-info">0</span>Purchase.Requisit.</a></li>
                                                <li><a href="apro_ofertas.php" style="background-image: url(img/gCons/reload.png);"><span id="cntr" class="label label-info">0</span>Ped. Aprobados</a></li>
                                                <?php
                                                if($_SESSION['user_rol'] == 1){
                                                ?>
                                                <li><a href="ordenes.php" style="background-image: url(img/gCons/connections.png);"><span id="cnoc" class="label label-info">0</span>Órd. de Compra</a></li>
                                                <?php
                                                }
                                                ?>
                                                
                                                <li><a href="dashboard2.php" style="background-image: url(img/gCons/processing.png)">Seguimiento</a></li>
                                                <!--<li><a href="facturas.php" style="background-image: url(img/gCons/copy-item.png);">Facturas</a></li>-->
<!--                                                </li><li><a href="javascript:void(0)" style="background-image: url(img/gCons/van.png)"><span class="label label-success">$2851</span> Delivery</a></li>
                                                <li><a href="javascript:void(0)" style="background-image: url(img/gCons/pie-chart.png)">Charts</a></li>
                                                <li><a href="javascript:void(0)" style="background-image: url(img/gCons/edit.png)">Add New Article</a></li>
                                                <li><a href="javascript:void(0)" style="background-image: url(img/gCons/add-item.png)"> Add New Page</a></li>
                                                <li><a href="javascript:void(0)" style="background-image: url(img/gCons/chat-.png)"><span class="label label-danger">26</span> Comments</a></li>-->
                                        </ul>
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
    ?>
        <script>
            $(document).ready(function() {$('#mped').addClass('active');
                loadData();
            });
        function loadData(){
            $.ajax({ 
                url: 'get_pedido.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = dt.total.total;
                    
                    $("#cntr").empty().append(ts);
                }
            }); 
            $.ajax({ 
                url: 'get_solicitud.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = dt.total.total;
                    
                    $("#cnsol").empty().append(ts);
                }
            }); 
            $.ajax({ 
                url: 'get_opago.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = dt.total.total;
                    
                    $("#cnop").empty().append(ts);
                }
            }); 
            
            $.ajax({ 
                url: 'get_oc.php', 
                success: function (data) {
//                    alert("entro");
                    var cntnt = '';
                    var cntnt2 = '';
                    var dt = JSON.parse(data);
                    var ts = dt.total.total;
                    
                    $("#cnoc").empty().append(ts);
                }
            }); 
        };
            
            
        </script>
    </body>
</html>
