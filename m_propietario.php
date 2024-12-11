<?php 
 require ('conect.php');
 require_once(__DIR__ . "/utils/auth.php");
 session_start();
 verificarUsuarioLogueado();
 if (!tieneAccesoAModulo("maestros")) {
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
                                        . '<th colspan="2">Creacion de Propietarios</th>'
                                        . '</tr>'
                                    . '</thead>'
                                . '</table>';
                            
                            ?>
                            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
                                <thead>
                                        <tr>
                                            <th>Propietario</th>
                                            <th>Nombre</th>
                                            <th>Direccion</th>
                                            <th>Telefono</th>   
                                            <th>Almacen</th> 
                                            <th>Acci&oacute;n</th>                                    
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px !important;">
                                        <?php
                                            $sq = "select * from clientes";
                                            $rs = $db->query($sq);
                                            while($row = $rs->fetch_assoc()){
                                                $terminales[$row['clirefer']] = $row;
                                                echo "<tr>";
                                                echo "<td>".utf8_encode($row['clirefer'])."</td>";
                                                echo "<td>".utf8_encode($row['clinom'])."</td>";
                                                echo "<td>".utf8_encode($row['clidirec'])."</td>";
                                                echo "<td>".utf8_encode($row['clitel'])."</td>";
                                                echo "<td>".utf8_encode($row['cod_alma'])."</td>";
                                                echo "<td>".'<a title="Modificar Cliente  " onclick="updDat('."'".$row['clirefer']."'".",'".$row['clinom']."'".",'".$row['clidirec']."'".",'".$row['clitel']."'".",'".$row['cod_alma']."'".')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>'."</td>";
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
                    <div class="modal fade bd-example-modal-md" id="editUsr"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="eForm">
                                        <div class="col-lg-40 col-sm-40" >
                                            <div class="col-lg-4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Propietario:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="updPro" name="updPro" readonly class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                            <div class="ol-lg4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Nombre:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="updNom" name="updNom" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div><br/><br/>
                                            <div class="ol-lg4 col-sm-4" >
                                                <div class="">                                                            
                                                    <label class="label" style="color:#000;">Direccion:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="updDir" name="updDir" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                            <div class="ol-lg4 col-sm-4" >
                                                <div class="">                                                            
                                                    <label class="label" style="color:#000;">Telefono:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="updTel" name="updTel" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                                                                     
                                           
                                            <div class="col-lg4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Almacen:</label><div style="clear:both;"></div><br/>
                                                    <select id="updAlm" class="form-control">
                                                        <?php 
                                                        $sq = "select * from alma";
                                                        $rs = $db->query($sq);
                                                        echo '<option value="">Seleccionar</option>';
                                                        while($ax = $rs->fetch_assoc()){
                                                            echo '<option value="'.$ax['almcod'].'">'.$ax['almcod'].' - '. utf8_encode($ax['almdes']).'</option>';
                                                        }
                                                        ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div><br/><br/>
                                            <div class="col-lg4 col-sm-4" >
                                                <div class="input-group">
                                                    <div class="input-group">
                                                        <button type="button" onclick="saveUpd()" class="form-control btn btn-primary">Guardar</button>
                                                    </div>
                                                </div>
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
                    <div class="modal fade bd-example-modal-md" id="addUsr"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="eForm">
                                        <div class="col-lg-26 col-sm-26" >
                                            <div class="col-lg-4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Propietario:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="addPro" name="addPro" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-lg-26 col-sm-26" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Nombre:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="addNom" name="addNom" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                             <div class="col-lg-30 col-sm-30" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Direccion:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="addDir" name="addDir" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                             <div class="col-lg-4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Telefono:</label><div style="clear:both;"></div><br/>
                                                    <input type="text" id="addTel" name="addTel" class="form-control" placeholder="" value=""/>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg4 col-sm-4" >
                                                <div class="input-group">                                                            
                                                    <label class="label" style="color:#000;">Almacen:</label><div style="clear:both;"></div><br/>
                                                    <select id="addAlm" class="form-control">
                                                        <?php 
                                                        $sq = "select * from alma ";
                                                        $rs = $db->query($sq);
                                                        echo '<option value="">Seleccionar</option>';
                                                        while($ax = $rs->fetch_assoc()){
                                                            echo '<option value="'.$ax['almcod'].'">'.$ax['almcod'].' - '. utf8_encode($ax['almdes']).'</option>';
                                                        }
                                                        ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div><br/><br/>
                                            <div class="col-lg4 col-sm-4" >
                                                <div class="input-group">
                                                    <div class="input-group">
                                                        <button type="button" onclick="saveAdd()" class="form-control btn btn-primary">Guardar</button>
                                                    </div>
                                                </div>
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
                $('#maest8').addClass('active');
                $('#c4').click();
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
                $("#addUsr .modal-title").empty().append('Añadir ');
                $('#addUsr').modal('show');
            }
            function updDat(addPro,addNom,addDir,addTel,addAlm){
                $("#editUsr .modal-title").empty().append('Editar ');
                $("#updPro").val(addPro);
                $("#updNom").val(addNom);
                $("#updDir").val(addDir);
                $("#updTel").val(addTel);
                $("#updAlm").val(addAlm);
//                $("#updAlm").val(addAlm).change();
              
                $('#editUsr').modal('show');
            }
            function saveAdd(){
                var Pro = $("#addPro").val();
                var Nom = $("#addNom").val();
                var Dir = $("#addDir").val();
                var Tel = $("#addTel").val();
                var Alm = $("#addAlm").val();

                
                if(Pro != ''){
                    $.ajax({ 
                        type: 'POST',
                    url: 'requests/savePropietario.php', 
                        data: {
                            action:'add',
                            Pro:Pro,
                            Nom:Nom,
                            Dir:Dir,
                            Tel:Tel,   
                            Alm:Alm,
                            table: 'clientes',
                            fields: 'clirefer,clinom,clidirec,clitel,cod_alma'
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 'm_propietario.php';
                            }
                        }
                    }); 
                }else{
                    alert('Favor ingresar una descripción');
                }
            }
            function saveUpd(){
                var Pro = $("#updPro").val();
                var Nom = $("#updNom").val();
                var Dir = $("#updDir").val();
                var Tel = $("#updTel").val();
                var Alm = $("#updAlm").val();

//                if(desc != ''){
                    $.ajax({ 
                        type: 'POST',
                    url: 'requests/savePropietario.php', 
                        data: {
                            action:'upd',
                            Pro:Pro,
                            Nom:Nom,
                            Dir:Dir,
                            Tel:Tel,
                            Alm:Alm,
                            table: 'clientes',
                            fields: 'clirefer,clinom,clidirec,clitel,cod_alma'
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 'm_propietario.php';
                            }
                        }
                    }); 
//                }else{
//                    alert('Favor ingresar una descripción');
//                }
            }
        </script>
    </body>
</html>
