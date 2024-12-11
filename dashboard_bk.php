<?php
require ('conect.php');
//include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>
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
<?php include 'header.php' ?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-lg-2 col-sm-2 col-xs-12">
                            <h3 class="heading">Movimentos</h3>
                            <ul class="dshb_icoNav clearfix">

                                <li><a href="recepcion.php" style="background-image: url(img/gCons/connections.png);">Recepción</a></li>
                                <li><a href="expedicion.php" style="background-image: url(img/gCons/connections.png);">Expedición</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-10 col-sm-10 col-xs-12">
                            <h3 class="heading">Imprimir Etiquetas</h3>
                            <ul class="dshb_icoNav clearfix">

                                <li><a href="javascript:void(0)" onclick="swModal('etiquetaModal')" style="background-image: url(img/gCons/connections.png);">Etiqueta Re/Ex</a></li>
                                <li><a href="javascript:void(0)" onclick="swModal('ubicacionModal')" style="background-image: url(img/gCons/connections.png);">Ubicación</a></li>
                                <li><a href="javascript:void(0)" onclick="swModal('eanModal')" style="background-image: url(img/gCons/connections.png);">EAN</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <h3 class="heading">Situación Actual del almacen</h3>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Recepción</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Expedición</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Empaque</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <h3 class="heading">Devolución   </h3>
                                    <div class="sidebar_info">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span id='lblPend' class="act act-danger"></span>
                                                <strong>Total</strong>
                                            </li>
                                            <li>
                                                <span id='lblProc' class="act act-success"></span>
                                                <strong>Muelle</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Pendiente</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Ubicado</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <h3 class="heading">General Movimientos   </h3>
                                    <div class="sidebar_info">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span id='lblPend' class="act act-danger"></span>
                                                <strong>Total</strong>
                                            </li>
                                            <li>
                                                <span id='lblProc' class="act act-success"></span>
                                                <strong>Muelle</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Pendiente</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Ubicado</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                        </div>
                        <div style="clear:both;"></div><br/><br/>
                         <div class="col-lg-12 col-sm-12 col-xs-12">
                            <h3 class="heading">Resultados dia anterior</h3>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Recepción</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Expedición</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                <h3 class="heading">Empaque</h3>
                                <div class="sidebar_info">
                                    <ul class="list-unstyled">
                                        <li>
                                            <span id='lblPend' class="act act-danger"></span>
                                            <strong>Total</strong>
                                        </li>
                                        <li>
                                            <span id='lblProc' class="act act-success"></span>
                                            <strong>Muelle</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Pendiente</strong>
                                        </li>
                                        <li>
                                            <span id='lblTot' class="act act-primary"></span>
                                            <strong>Ubicado</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <h3 class="heading">Devolución   </h3>
                                    <div class="sidebar_info">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span id='lblPend' class="act act-danger"></span>
                                                <strong>Total</strong>
                                            </li>
                                            <li>
                                                <span id='lblProc' class="act act-success"></span>
                                                <strong>Muelle</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Pendiente</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Ubicado</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <h3 class="heading">General Movimientos   </h3>
                                    <div class="sidebar_info">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span id='lblPend' class="act act-danger"></span>
                                                <strong>Total</strong>
                                            </li>
                                            <li>
                                                <span id='lblProc' class="act act-success"></span>
                                                <strong>Muelle</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Pendiente</strong>
                                            </li>
                                            <li>
                                                <span id='lblTot' class="act act-primary"></span>
                                                <strong>Ubicado</strong>
                                            </li>
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
include 'js_fn.php';
?>
        <script>
            var dex = [['','','','','']];
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
                $('#dashb').addClass('active');
//                $('#c1').click();
            });
        </script>
    </body>
</html>
