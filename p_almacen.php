<?php 
 require ('conect.php');
 require_once(__DIR__ . "/utils/auth.php");
 session_start();
 verificarUsuarioLogueado();
 if (!tieneAccesoAModulo("configuraciones")) {
     echo "No tiene acceso";
     exit;
 }


$db = new mysqli($SERVER,$USER,$PASS,$DB);
// echo "<pre>"; var_dump($data);echo "</pre>";
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
                                        . '<th colspan="2">Almacenes</th>'
                                        . '</tr>'
                                    . '</thead>'
                                . '</table>';
                            
                            ?>
                            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
                                <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Centro</th>
                                            <th>Acci&oacute;n</th>                                    
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px !important;">
                                        <?php
                                            $sq = "select * from alma";
                                            $rs = $db->query($sq);
                                            while($row = $rs->fetch_assoc()){
                                                echo "<tr>";
                                                echo "<td>".utf8_encode($row['almcod'])."</td>";
                                                echo "<td>".utf8_encode($row['almdes'])."</td>";
                                                echo "<td>".utf8_encode($row['cencod'])."</td>";
                                                echo "<td>".'<a title="Cambiar estado" onclick="updAlm('."'".utf8_encode($row['almcod'])."'".",'".utf8_encode($row['almdes'])."'".",'".utf8_encode($row['cencod'])."'".')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>'."</td>";
                                                echo "</tr>";
                                            }
                                        ?>
                                    </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-sm"  id="editUsr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="eForm">
                                        <div class="col-lg-12 col-sm-12" >                                            
                                            <div class="input-group">
                                                <input type="text" id="uuid" name="uuid" class="form-control" placeholder="Almacen" value=""/>
                                            </div>
                                            <br/>     
                                            <div class="input-group">                                                            
                                                <input type="hidden" id="uuid" name="uuid" />
                                                <input type="text" id="uudes" name="uudes" class="form-control" placeholder="Descripción" value=""/>
                                            </div>
                                            <br/>     
                                            <div class="input-group">                                 
                                                <input type="text" id="uucen" name="uucen" class="form-control" placeholder="Centro" value=""/>
                                            </div>
                                            <br/>     
                                            <div class="input-group">
                                                <button type="button" onclick="saveUpd()" class="form-control btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </form>  
                                    <div style="clear:both;"></div>
                                </div>
                            </div>

                          </div>
                    </div>
                    <div style="clear:both;"></div>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-lg" id="addUsr"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="eForm">
                                        <div class="col-lg-12 col-sm-12" >
                                            <div class="input-group">                                    
                                                <input type="text" id="add_uuid" name="add_uuid" class="form-control" placeholder="Almacen" value=""/>
                                            </div>
                                            <br/> 
                                            <div class="input-group">                                                            
                                                <input type="hidden" id="uuid" name="uuid" />
                                                <input type="text" id="add_uudes" name="add_uudes" class="form-control" placeholder="Descripción" value=""/>
                                            </div>
                                            <br/>     
                                            <div class="input-group">                                 
                                                <input type="text" id="add_uucen" name="add_uucen" class="form-control" placeholder="Centro" value=""/>
                                            </div>
                                            <br/>           
                                            <div class="input-group">
                                                <button type="button" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </form>   
                                    <div style="clear:both;"></div>
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
        <script type="text/javascript">
            $(document).ready(function(){
                $('#param2').addClass('active');
                $('#c7').click();
//                setTimeout(function(){ window.location = 'general.php'; }, 180000);
                table = $('#tbl_list').DataTable( {
                    "processing": true,
                    "serverSide": false,
                    "bFilter": true,
                    dom: '<"top"B<lfrtip>><"clear">',
                    buttons: [
                        {
                            text: 'Añadir',
                            action: function ( e, dt, node, config ) {                                
                                addUsr();
                            }
                        },
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
            });

		</script>
        <script type="text/javascript">
            function addUsr(){
                $("#addUsr .modal-title").empty().append('Crear Almacen ');
                $('#addUsr').modal('show');
            }
            function updAlm(id, desc, cen){
                $("#editUsr .modal-title").empty().append('Actualizar Almacen '+id);
                $('#editUsr').modal('show');
                $("#uuid").val(id);
                $("#uudes").val(desc);
                $("#uucen").val(cen);
            }
//            function updDat(id, desc){
//                var mst = 'activar';
//                if(desc == 0){
//                    mst = 'inactivar';
//                }
//                var r = confirm("Esta acción cambiará el estado de las rutas. Desea "+mst+" la Ruta"+id+"?");
//                if(r == true){
//                    $.ajax({ 
//                        type: 'POST',
//                    url: 'requests/chgStatus.php', 
//                        data: {
//                            id:id,
//                            desc:desc
//                        },success: function (data) {
//                            var dt = JSON.parse(data);                            
//                            if(dt.err == 0){                                
//                                window.location = 'p_rutas_cns.php';
//                            }else{
//                                alert(dt.msg);
//                            }
//                        }
//                    }); 
//                }
//            }
            function saveAdd(){
                var id = $("#add_uuid").val();
                var desc = $("#add_uudes").val();                
                var cen = $("#add_uucen").val();
                
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/saveDataAlm.php', 
                    data: {
                        action:'add',
                        id: id,
                        desc: desc,
                        cen: cen,
                        table:'alma'
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.err == 0){
                            window.location = 'p_almacen.php';
                        }
                    }
                }); 
            }
            function saveUpd(){
                var id = $("#uuid").val();
                var desc = $("#uudes").val();                
                var cen = $("#uucen").val();
                
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/saveDataAlm.php', 
                    data: {
                        action:'upd',
                        id: id,
                        desc: desc,
                        cen: cen,
                        table:'alma'
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.err == 0){
                            window.location = 'p_almacen.php';
                        }
                    }
                }); 
            }
        </script>
    </body>
</html>
