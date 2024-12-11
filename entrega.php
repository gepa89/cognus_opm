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
            .btn_ex{
                -webkit-box-shadow: inset 0px 1px 0px 0px #fff;
                box-shadow: inset 0px 1px 0px 0px #fff;
                text-align: center;
                list-style: none;
                display: inline-block;
                margin: 0 5px 10px;
                background: #f9f9f9;
                background: -moz-linear-gradient(top, #f9f9f9 0%, #efefef 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9f9f9), color-stop(100%,#efefef));
                background: -webkit-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: -o-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: -ms-linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                background: linear-gradient(top, #f9f9f9 0%,#efefef 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#efefef',GradientType=0 );
                border: 1px solid #e2e2e2;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                -ms-border-radius: 6px;
                border-radius: 6px;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">                            
                            <div class="row">
                                <div class="col-lg-6 col-sm-6 col-xs-6">
                                    <h3 class="heading">Entrega</h3>
                                    <div class="col-sm-3 col-md-3"></div>
                                    <div class="col-lg-6 col-sm-6 col-xs-6">
                                        <?php var_dump($_SESSION);?>
                                        <form class="form-horizontal" role="form">
                                            <div class="form-group">
                                                <div class="col-lg-10">
                                                    <input type="entrega" class="form-control" id="inputEntrega" placeholder="Entrega">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-10">
                                                    <button type="button" onclick="sndEntrega()" class="btn btn-default btn_ex">Controlar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-sm-3 col-md-3"></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-6">
                                    <h3 class="heading">Herramientas</h3>
                                    <ul class="dshb_icoNav clearfix">
                                            <li><a href="solicitudes.php" style="background-image: url(img/gCons/edit.png)">VF01</a></li>
                                            <li><a href="pedidos.php" style="background-image: url(img/gCons/connections.png);">Etiquetas</a></li>
                                            <li><a href="facturas.php" style="background-image: url(img/gCons/copy-item.png);">VL02N</a></li>
                                            <li><a href="javascript:void(0)" style="background-image: url(img/gCons/van.png)"> MANIFIESTO</a></li>
<!--                                                <li><a href="javascript:void(0)" style="background-image: url(img/gCons/pie-chart.png)">Charts</a></li>
                                            <li><a href="javascript:void(0)" style="background-image: url(img/gCons/edit.png)">Add New Article</a></li>
                                            <li><a href="javascript:void(0)" style="background-image: url(img/gCons/add-item.png)"> Add New Page</a></li>
                                            <li><a href="javascript:void(0)" style="background-image: url(img/gCons/chat-.png)"><span class="label label-danger">26</span> Comments</a></li>-->
                                    </ul>
                                    <div class="col-sm-12 col-md-12">                                        
                                            <div class="col-sm-1 col-md-1"></div>
                                            <div class="col-sm-10 col-md-10">
                                                <input value="Etiquetas para Transferencias " style="" class="btn form-control btn-default btn_ex" type="submit">
                                            </div>
                                            <div class="col-sm-1 col-md-1">
                                                    
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
    ?>
        <script>
            $(document).ready(function() {
                $('#mped').addClass('active');
                
            });
            function sndEntrega(entrega, of, usr){
    //            alert("aprobar "+pd+" "+of);
                $.ajax({ 
                    url: 'get_entrega.php', 
                    type: 'POST',
                    data: {
                        entrega:pd,
                        usuario:'<?php echo $_SESSION["user"]?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 1){
                            alert(dt.msg);
                        }else{             
                            window.location = 'pedidos.php';
                        }  
                    }
                });
            }
            
        </script>
    </body>
</html>
