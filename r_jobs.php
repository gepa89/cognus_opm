<?php
require ('conect.php');
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("reportes")) {
    echo "No tiene acceso";
    exit;
}
$pd = '';
$fl = 0;

$db = new mysqli($SERVER,$USER,$PASS,$DB);
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        <style>
            .details-control {
                background: url('details_open.png') no-repeat center center;
                cursor: pointer;
                width: 40px !important;
                height: 40px !important;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                position: relative;
                padding-left: 30px;
                cursor: pointer;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
                content: "-";
                background-color: #d33333;
            }
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
                top: 33%;
                left: 5px;
                height: 1em;
                width: 1em;
                margin-top: -5px;
                display: block;
                position: absolute;
                color: white;
                border: .15em solid white;
                border-radius: 1em;
                box-shadow: 0 0 0.2em #444;
                box-sizing: content-box;
                text-align: center;
                text-indent: 0 !important;
                font-family: "Courier New",Courier,monospace;
                line-height: 1em;
                content: "+";
                background-color: #31b131;
            }
            tr.shown td .details-control {
                background: url('details_close.png') no-repeat center center;
            }
            .label {
                 color: #000; 
            }
            #tblReg tbody tr td:first-of-type{
                width:100px;
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
                                . '<th colspan="3">JOBS.</th>'
                                . '</tr>'
                            . '</thead>'
                        . '</table>';?>
                        <div class="col-md-12 col-lg-12 col-xs-12">
                            <h3 class="heading">Configuración</h3>
                            <div class="col-md-12">
                                <div id="">
                                    <table class="table tab">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Script</th>
                                                <th>Últ. Ejec.</th>
                                                <th>Sig. Ejec.</th>
                                                <th>Periodo</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><br/>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <button type="button" id="sndBtn" onclick="ckFields()" class="form-control btn btn-primary">Guardar</button>
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
    ?>
        <script type="text/javascript">
            
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
