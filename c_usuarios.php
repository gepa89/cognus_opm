<?php
require ('conect.php');
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
}

function gtROL($r){
//    echo $r;
    switch ($r){
        case null:
            $desc = ' ';
            break;
        case 0:
            $desc = '00 - SUPERVISOR';
            break;
        case 1:
            $desc = '01 - EMPAQUETADOR';
            break;
        case 2:
            $desc = '02 - CHOFER';
            break;
        case 3:
            $desc = '03 - PREPARADOR';
            break;
        default:
            $desc = ' ';
            break;
    }
    
    return $desc;
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
                                        . '<th colspan="2">Usuarios</th>'
                                        . '</tr>'
                                    . '</thead>'
                                . '</table>';
                            
                            ?>
                            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 12px !important;">
                                <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Acci&oacute;n</th>                                    
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $sq = "select * from usuarios";
//                                            echo $sq;
                                            $rs = $db->query($sq);
                                            while($row = $rs->fetch_assoc()){
                                                echo "<tr>";
                                                echo "<td>". utf8_encode($row['pr_user'])."</td>";
                                                echo "<td>".$row['pr_nombre']."</td>";
                                                echo "<td>".$row['pr_apellido']."</td>";
                                                echo "<td>".$row['pr_email']."</td>";
                                                echo "<td>". gtROL("".$row['pr_rol']."")."</td>";
                                                echo "<td>".'<a title="Modificar" onclick="updUSR('."'".$row['pr_user']."'".",'".$row['pr_nombre']."'".",'".$row['pr_apellido']."'".",'".$row['pr_email']."'".",'".$row['pr_rol']."'"."".')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>'."</td>";
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
                                    <form method="post" id="eForm1">
                                        <div class="col-lg-12 col-sm-12" >
                                            <div class="input-group">                                                    
                                                <input type="text" id="email" name="email" class="form-control" placeholder="Email" value=""/>
                                                <input type="hidden" id="uuser" name="uuser" class="form-control" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="nombre" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="text" id="apellido" name="apellido" class="form-control" placeholder="apellido" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="password" id="pass" name="pass" class="form-control" placeholder="Contraseña" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="password" id="pass2" name="pass2" class="form-control" placeholder="Repetir Contraseña" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">
                                                <select class="form-control" id="rol" name="rol">
                                                    <option value="">Seleccionar</option>
                                                    <option <?php echo ($_SESSION["user_rol"]==0)?"selected":"";?> value="0">00 - SUPERVISOR</option>
                                                    <option <?php echo ($_SESSION["user_rol"]==1)?"selected":"";?> value="1">01 - EMPAQUETADOR</option>
                                                    <option <?php echo ($_SESSION["user_rol"]==2)?"selected":"";?> value="2">02 - CHOFER</option>
                                                </select>
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
                                        <div class="col-lg-6 col-sm-6" >
                                            <div class="input-group">                                                    
                                                <input type="text" id="add_uuser" name="add_uuser" class="form-control" placeholder="Usuario" value=""/>
                                            </div>
                                            <br/>
                                            
                                            <div class="input-group">                                                    
                                                <input type="text" id="add_email" name="add_email" class="form-control" placeholder="Email" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="password" id="add_pass" name="add_pass" class="form-control" placeholder="Contraseña" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">
                                                <select class="form-control" id="add_rol" name="add_rol">
                                                    <option value="">Seleccionar</option>
                                                    <option value="0">00 - SUPERVISOR</option>
                                                    <option value="1">01 - EMPAQUETADOR</option>
                                                    <option value="2">02 - CHOFER</option>
                                                </select>
                                            </div>
                                            <br/>
                                        </div>
                                        <div class="col-lg-6 col-sm-6" >
                                            <div class="input-group">                                                    
                                                <input type="text" id="add_nombre" name="add_nombre" class="form-control" placeholder="nombre" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="text" id="add_apellido" name="add_apellido" class="form-control" placeholder="apellido" value=""/>
                                            </div>
                                            <br/>
                                            <div class="input-group">                                                    
                                                <input type="password" id="add_pass2" name="add_pass2" class="form-control" placeholder="Repetir Contraseña" value=""/>
                                            </div> 
                                        </div>
                                            <div style="clear:both;"></div>
                                        <div class="col-lg-6 col-sm-6" >
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
            $(document).on('change', '#add_rol', function(){
                if($('#add_rol option:selected').val() == 1){
                    $('#add_rel').prop("disabled", false)   
                }else{
                    $('[name=add_rel]').val( '' );
                    $('#add_rel').prop("disabled", true);                    
                }
            });
            $(document).on('change', '#rol', function(){
                if($('#rol option:selected').val() == 1){
                    $('#rel').prop("disabled", false)   
                }else{
                    $('[name=rel]').val( '' );
                    $('#rel').prop("disabled", true);                    
                }
            });
            $(document).ready(function(){
                $('#eusu').addClass('active');
                $('#c1').click();
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
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todo"]],
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
                $("#addUsr .modal-title").empty().append('Añadir usuario ');
                $('#addUsr').modal('show');
            }
            function updUSR(usr,nombre,apellido,email,rol){
                $(".modal-title").empty().append('Modificar usuario <br/><b>'+usr+'</b>');
                $("#uuser").attr('value',usr);
                $("#nombre").attr('value',nombre);
                $("#apellido").attr('value',apellido);
                $("#email").attr('value',email);
//                $("#rol").attr('value',rol);
                $("#rol").val(rol);
                $('#editUsr').modal('show');
            }
            function saveAdd(){
                var user = $("#add_uuser").val();
                var nombre = $("#add_nombre").val();
                var apellido = $("#add_apellido").val();
                var email = $("#add_email").val();
                var pass = $("#add_pass").val();
                var pass2 = $("#add_pass2").val();
                var rol = $("#add_rol").val();
                if(pass == pass2){
                    $.ajax({ 
                        type: 'POST',
                        url: 'sendUser.php', 
                        data: {
                            action:'add_user',
                            user: user,
                            email: email,
                            nombre:nombre,
                            apellido:apellido,
                            pass:pass,
                            rol:rol
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 'c_usuarios.php';
                            }
                        }
                    });   
                }else{
                    alert('Las contraseñas no coinciden');
                }
            }
            function saveUpd(){
                var user = $("#uuser").val();
                var nombre = $("#nombre").val();
                var apellido = $("#apellido").val();
                var pass = $("#pass").val();
                var pass2 = $("#pass2").val();
                var email = $("#email").val();
                var rol = $("#rol").val();
                var flg = 0
                if(pass != ''){
                    if(pass == pass2){
                        flg = 0;
                    }else{
                        alert('Las contraseñas no coinciden');
                        flg = 1;
                    }
                }
                if(flg == 0){
                    $.ajax({ 
                        type: 'POST',
                        url: 'sendUser.php', 
                        data: {
                            action:'edit_usr',
                            user: user,
                            email: email,
                            nombre:nombre,
                            apellido:apellido,
                            pass:pass,
                            pass2:pass2,
                            rol:rol
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 'c_usuarios.php';
                            }
                        }
                    }); 
                }
            }
        </script>
    </body>
</html>
