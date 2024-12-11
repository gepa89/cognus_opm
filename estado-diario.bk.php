<?php
require ('conect.php');
include 'src/adLDAP.php';
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER,$USER,$PASS,$DB);

$sq = "SELECT distinct ct_empaque, lg_c_mat, lg_c_mat_desc, lg_c_cant, ct_user, ct_ts FROM log_cierre inner join log_material on ct_empaque = lg_c_emp where date(ct_ts) >= CURDATE()";
$rs = $db->query($sq);

while($row = $rs->fetch_assoc()){
    $data_usr[] = $row;
}

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
        </style>
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <?php

                        echo '<table class="table">'
                            . '<thead>'
                                . '<tr>'
                                . '<th colspan="3">Productividad por empaquetador</th>'
                                . '</tr>'
                            . '</thead>'
                        . '</table>';?>
                            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list2" style="font-size: 10px !important;">
                                <thead>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th colspan="3" style="background:#c8c1c1;text-align:center;">Total</th>
                                            <th colspan="3" style="background:#c8c1c1;text-align:center;">Hoy</th>
                                        </tr>
                                        <tr>
                                            <th>Cd. Usr.</th>
                                            <th>Ct. OC</th>
                                            <th style='text-align:right;'>Abiertos</th>
                                            <th style='text-align:right;'>Cerrados</th>
                                            <th style='text-align:right;'>Borrado</th>
                                            <th style='text-align:right;'>Abiertos</th>
                                            <th style='text-align:right;'>Cerrados</th>
                                            <th style='text-align:right;'>Borrado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 

                                        foreach($dt as $usr => $dt){
                                                echo "<tr>";
                                                echo "<td>".$usr."</td>";
                                                echo "<td>".$dt["asignado"]."</td>";
                                                $var = (int)@$dt["asignado"] - ((int)@$dt['dt']["close_pedido"] + (int)@$dt['dt']["delete_pedido"]);
                                                echo "<td>".$var."</td>";
                                                echo "<td>".@$dt['dt']["close_pedido"]."</td>";
                                                echo "<td>".@$dt['dt']["delete_pedido"]."</td>";
                                                $var2 = (int)@$dt['dt']["assign_hoy"]-((int)@$dt['dt']["close_pedido_hoy"]+(int)@$dt['dt']["delete_pedido_hoy"]);
                                                echo "<td>".$var2."</td>";
                                                echo "<td>".@$dt['dt']["close_pedido_hoy"]."</td>";
                                                echo "<td>".@$dt['dt']["delete_pedido_hoy"]."</td>";
                                            }
                                        ?>    
                                    </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <!--<form id="assgnRsr">-->
                                        <!--<div class="input-group">-->
                                        <input type="hidden" name="pedido" id="pedido" value=""/>
                                        <label style="text-align: left;" class="pull-left">Usuario:</label>
                                            <select class="form-control" id="selUsr" name="selUsr">

                                                <?php 

                                                    $ss = "SELECT * FROM prt_users where prt_rol <> 2 order by prt_id asc";

                                                    $rs = $db->query($ss);
                                                    while($row = $rs->fetch_assoc()){
                                                         // Esta es la dirección a donde enviamos
//                                                        echo $_SESSION["user_id"].' == '.$row['prt_id'];
                                                        if($_SESSION["user_id"] == $row['prt_id']){
                                                            $sel = 'selected="selected"';
                                                        }else{
                                                            $sel = '';
                                                        }
                                                        echo '<option '.$sel.' value="'.$row['prt_id'].'">'.$row['prt_user'].'</option>';

                                                    }
                                                ?>
                                            </select>
                                        <!--</div>-->
                                        <br/>
                                        <button type="submit" onclick="assignPD2()" class="btn btn-primary">Guardar</button>
                                    <!--</form>-->
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
                $('#eprod').addClass('active');
                $('#c1').click();
                $("input[name^='fil-']").on('change', function(){
                     $('#filter').submit();
                });
                setTimeout(function(){ $('#filter').submit(); }, 600000);
                $(document).on('change', "input[name^='leCkAll-']", function(){
                    var ckId = this.id;
                    var aux = ckId.split('-');
                    var list = '';
                    if($(this).is(':checked')){
                        $("input[name^='leCk-"+aux[1]+"-']").each(function(){
                            var itmID = this.id;
                            var auxInner = itmID.split('-');
                            $(this).prop('checked', true);
                            if(list == ''){
                                list = "'"+auxInner[2]+"'";   
                            }else{
                                list += ",'"+auxInner[2]+"'";   
                            }
                        });
                    }else{
                        $("input[name^='leCk-"+aux[1]+"-']").each(function(){
                            $(this).prop('checked', false);
                        });
                        list = '';
                    }
                    console.log(list);
                    $("input[name^='inck-"+aux[1]+"']").attr('value',list);
                });
                $(document).on('change', "input[name^='leCk-']", function(){
                    var itmID = this.id;
                    var auxInner = itmID.split('-');
                    var list = '';
                    var ctElm = 0;
                    var ctElmSel = 0;
                    $("input[name^='leCk-"+auxInner[1]+"-']").each(function(){
                        ctElm++;
                        var itmID = this.id;
                        var auxInner = itmID.split('-');
                        if($(this).is(':checked')){
                            ctElmSel++;
                            if(list == ''){
                                list = "'"+auxInner[2]+"'";   
                            }else{
                                list += ",'"+auxInner[2]+"'";   
                            }
                        }                        
                    });
                    if(ctElm == ctElmSel){
                        if(!$("input[name^='leCkAll-"+auxInner[1]+"']").is(':checked')){
                            $("input[name^='leCkAll-"+auxInner[1]+"']").prop('checked', true);
                        }
                    }else{
                        if($("input[name^='leCkAll-"+auxInner[1]+"']").is(':checked')){
                            $("input[name^='leCkAll-"+auxInner[1]+"']").prop('checked', false);
                        }
                    }
                    $("input[name^='inck-"+auxInner[1]+"']").attr('value',list);
                });
                var table = $('#tbl_list').DataTable( {
                    "processing": true,
                    "serverSide": false,
                    "bFilter": true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                         'csv','excel'
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
                    },
                    "order": [[1, 'asc']]
                } );
                table2 = $('#tbl_list2').DataTable( {
                    "processing": true,
                    "serverSide": false,
                    "bFilter": true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                         'csv','excel'
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
            });
            function svComment(pedido, posicion){
                var texto = $("textarea[name^='cmnt-"+pedido+"-"+posicion+"']").val();
                console.log(texto);
                $.ajax({ 
                    type: 'POST',
                    url: 'rqst.php', 
                    data: {
                        action:'stComment',
                        ped:pedido,
                        pos:posicion,
                        texto:texto
                    },success: function (data) {
                        console.log("comentario registrado");
                    }
                }); 
            }
            
            function ckPDMass(pedido, st){
                var lista = $("input[name^='inck-"+pedido+"']").val();
                if(st == 1){
                    var sttxt = "recibida"; 
                }else{
                    var sttxt = "cancelada"; 
                }
                if(lista != ''){
                    var r = confirm("Está seguro de marcar la(s) posción(es) como "+sttxt+"s. La acción no se puede revertir.");
                    if(r == true){
//                        alert("entro"0014639903);
                        $.ajax({ 
                            type: 'POST',
                            url: 'rqst.php', 
                            data: {
                                action:'stStatusMass',
                                ped:pedido,
                                lista:lista,
                                st:st
                            },success: function (data) {

                                var dt = JSON.parse(data);
                                if(dt.rt == 9){
                                    $('#filter').submit();
                                }
                            }
                        }); 
                    }
                }else{
                    alert('Debe seleccionar almenos una posición');
                }
                
            }
            function swOC(oc,pd){
                var send = '<a title="Enviar OC '+oc+'"  onclick="sndOC('+"'"+oc+"'"+','+"'"+pd+"'"+')"><span style="cursor:pointer;font-size:14px; color: green;" class="glyphicon glyphicon-send"></span></a> | <span id="btnPrint" style="cursor:pointer;font-size:14px; color: green;" class="glyphicon glyphicon-print"></span>';
                $('.modal-body > label').empty().append('Comentario OC: '+oc+' '+send);
                $('#comment_txt').val('');
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
            function printElement(elem) {
                var domClone = elem.cloneNode(true);

                var $printSection = document.getElementById("printSection");

                if (!$printSection) {
                    var $printSection = document.createElement("div");
                    $printSection.id = "printSection";
                    document.body.appendChild($printSection);
                }

                $printSection.innerHTML = "";
                $printSection.appendChild(domClone);
                window.print();
            }
            function format ( d ) {
//                alert(d.obj_id);
                return d;
                
                // `d` is the original data object for the row
                
            }
            function getChilds( d){
                var cntn = '';
                $.ajax({ 
                    type: 'POST',
                    url: 'getItemsDashboard.php', 
                    data: {
                        pedido:d.obj_id
                    },success: function (data) {
                        console.log('entro al success del ajax');
                        cntn = data;
                        console.log('cntnt tiene: '+cntn);
                        return cntn;
                    }
                }); 
                
            }
            function delPD(id){
                $.ajax({ 
                    type: 'POST',
                    url: 'rqst.php', 
                    data: {
                        action:'delete_pedido',
                        pedido:id
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.rt == 6){
                           $('#filter').submit();
                        }
                    }
                }); 
            }
            function closePD(id){
                $.ajax({ 
                    type: 'POST',
                    url: 'rqst.php', 
                    data: {
                        action:'close_pedido',
                        pedido:id
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.rt == 5){
                            $('#filter').submit();
                        }
                    }
                }); 
            }
            function ckPD(ped, pos, st){
                if(st == 1){
                    var sttxt = "recibida"; 
                }else{
                    var sttxt = "cancelada"; 
                }
                var r = confirm("Está seguro de marcar la posción como "+sttxt+". La acción no se puede revertir.");
                if(r == true){
                    $.ajax({ 
                        type: 'POST',
                        url: 'rqst.php', 
                        data: {
                            action:'stStatus',
                            ped:ped,
                            pos:pos,
                            st:st
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            if(dt.rt == 8){
                                $('#filter').submit();
                            }
                        }
                    }); 
                }
                
            }
            function sndOC(oc,pd){
                var comm = $('#comment_txt').val();
                $.ajax({ 
                    type: 'POST',
                    url: 'alertas_oc_snd.php', 
                    data: {
                        oc:oc,
                        comm:comm,
                        pd:pd
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.env == '1'){
                            alert('OC enviada.');
                            $('#filter').submit();
                        }else{
                            alert('Error al enviar OC.');
                        }                        
                    }
                });
            }
        </script>
        <script type="text/javascript">
            
            function assignPD(id){
                $(".modal-title").empty().append('Asignar pedido #'+id);
                $("#pedido").attr('value',id);
            }
            function assignPD2(){
                var pedido = $("#pedido").val();
                var id = $("#selUsr").val();
                $.ajax({ 
                    type: 'POST',
                    url: 'sendUser.php', 
                    data: {
                        action:'assign',
                        pedido:pedido,
                        id:id
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.rt == 4){
                            window.location = 'general.php';
                        }
                    }
                }); 
            }
            
        </script>
    </body>
</html>
