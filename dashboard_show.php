<?php
require ('conect.php');
include 'src/adLDAP.php';
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
            td.details-control {
                background: url('images/details_open.png') no-repeat center center;
                cursor: pointer;
                width: 28px !important;
            }
            tr.shown td.details-control {
                background: url('images/details_close.png') no-repeat center center;
            }
            .modal-header .close {
                margin-top: -22px;
            }
            .hddn{
                display: none;
            }
            div[id^='etPrev']{
                display: block;
                position: relative;
                width: 480px;
                height: 195px;
                padding: 5px;
                background: #fff;
                border: 1px dashed #dcdcdc;
                border-radius: 10px;
                overflow: hidden;
            }
            
            .vcard .v-heading {
                background: #fff;
                font-weight: 700;
            }
            .vcard .vcard-item {
                margin-left: 62px;
            }
            .vcard{                
                position: relative;
                z-index: 0;
            }
            
            .vcard.v3 {
                padding: 3px;
                border-bottom: 0px dashed #dcdcdc;
                float: right;
                position: absolute;
                bottom: 22px;
                right: 25px;
            }
            .vcard > ul {
                list-style: none;
                margin: 0 !important;
                position: relative;
                overflow: hidden;
                left: -24px;
            }
            .vcard.v1 > ul > li:first-of-type {
                font-size: 20px;
                padding: 3px;
                border-top: 0px dashed #dcdcdc;
            }
            .vcard > ul > li:first-of-type {                
                border-top: 0px dashed #dcdcdc;
            }
            
            .vcard.v3 > ul > li:first-of-type {
                font-size: 15px;
                padding: 3px;
                border: 0px dashed #dcdcdc;
            }
            .vcard > ul > li:last-of-type {
                border-bottom: 1px dashed #dcdcdc;
            }
            .vcard > ul > li {
                padding: 2px;
                border-bottom: 1px dashed #dcdcdc;
                overflow: hidden;
                font-size: 12px;
            }#barcode, #barcodeTr, .barcode {
                position: relative;
                float: right;
                top: -95px;
                right: 0;
            }
            .barcode2 {
                position: relative !important;
                float: right !important;
                top: -90px !important;
                right: 0 !important;
            }
            #toPrint{
                display: block;
                position: relative;
                bottom:0;
                right:0;
            }
            #etiquetaModal .modal-dialog .modal-content .modal-body{
                max-height: 440px !important;
                overflow: hidden !important;
            }
/*            #manifiestoModal .modal-dialog .modal-content .modal-body #mncontent{
                max-height: 300px !important;
                overflow-y: auto !important;
            }*/
            #prMnArea, #prBlArea{
                display:none;
            }
            .blEntTbl{
                display: table;
                width: 100%;
                
            }
            .blEntTbl > li{
                display:inline-block;
                text-align: center;
                width:15%;
                -webkit-box-shadow: inset 0px 1px 0px 0px #fff;
                box-shadow: inset 0px 1px 0px 0px #fff;
                text-align: center;
                list-style: none;
                padding: 5px 0; 
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
            .blInClose{
                display: block;
                position: relative;
                float:right;
                top:0;
                right: 5px;
            }
            .sidebar_info2 {
                width: 180px;
                position: relative;
                left: 30px;
                bottom: 10px;
            }
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content" style="margin-left: 0px !important;">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-xs-12">    
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-xs-12">
                                    <h3 class="heading">Ranking</h3>
                                    <div class="col-sm-12">
                                        <div id="gr_2" style="height:400px;width:100%;margin:25px auto 0"></div>
                                    </div>
                                </div>                                
                            </div>                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    <?php 
//        include 'sidebar.php';
        include 'js_in.php';
        include 'js_fn_show.php';
    ?>
        <script>
            
        </script>
    </body>
</html>
