<?php
require ('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("configuraciones")) {
    echo "No tiene acceso";
    exit;
}
include 'requests/GO/Crontab.php';
use GO\Crontab;
$cronController = new Crontab();


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
                                        . '<th colspan="2">Tareas Programadas</th>'
                                        . '</tr>'
                                    . '</thead>'
                                . '</table>';
                            
                            ?>
                            <table class="table table-hover table-striped table-bordered dTableR"  style="font-size: 12px !important;">
                                <thead>
                                        <tr>
                                            <th>Tarea</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $jobs = $cronController->getScriptsData();
//                                            var_dump($jobs);
                                            foreach($jobs as $k => $jb){
                                                if(is_numeric(strpos($jb['name'], 'wms_'))){
                                                        $act='play';
                                                        $lblclr='primary';
                                                        $tittle=$act='';
                                                        if($jb['status']){
                                                            $scptst = 'Activo';
                                                            $act = 'pause';
                                                            $lblclr = 'danger';
                                                            $title = 'Detener tarea';
                                                            $actx = 'pause';
                                                        }else{
                                                            $scptst = 'Detenido';
                                                            $act = 'play';
                                                            $lblclr = 'success';
                                                            $title = 'Reanudar tarea';
                                                            $actx = 'play';
                                                        }
                                                        echo "<tr>";
                                                        echo "<td>". ($jb['name'])."</td>";
                                                        echo "<td>". ($scptst)."</td>";
                                                        echo "<td>". 
                                                                '<a title="'.$title.'" class="badge badge-pill badge-'.$lblclr.'" onclick="actJob('."'".$actx."'".",'".$jb['script']."'".')"><span style="font-size:10px; padding:5px;" class="-success glyphicon glyphicon-'.$act;                                                
                                                        echo '"></span></a> '."</td>";
                                                        echo "</tr>";
                                                }
                                                
                                            }
                                        ?>
                                    </tbody>    
                            </table>
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
            function actJob(act, script){
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/actOverJobs.php', 
                    data: {
                        action:act,
                        script:script
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.err == 0){
                            window.location = 'p_jobs.php';
                        }
                    }
                });  
            }
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
                $('#param9').addClass('active');
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
                                window.location = 's_usuarios.php';
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
                var email = $("#email").val();
                var rol = $("#rol").val();
                var flg = 0
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
                            rol:rol
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 's_usuarios.php';
                            }
                        }
                    }); 
                }
            }
        </script>
    </body>
</html>
