<?php 
 require ('conect.php');
if(!isset($_SESSION['user'])){
    header('Location:login.php');
    exit();
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
                                        . '<th colspan="2">Documentos</th>'
                                        . '</tr>'
                                    . '</thead>'
                                . '</table>';
                            
                            ?>
                            <table class="table table-hover table-striped table-bordered dTableR" id="tbl_list" style="font-size: 16px !important;">
                                <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Acci&oacute;n</th>                                    
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px !important;">
                                        <?php
                                            $sq = "select * from ruta";
                                            $rs = $db->query($sq);
                                            while($row = $rs->fetch_assoc()){
                                                echo "<tr>";
                                                echo "<td>".utf8_encode($row['rutdes'])."</td>";
                                                echo "<td>"; echo ($row['rutdes'] == 1)? "Activo":'Inactivo'; echo "</td>";
                                                echo "<td>".'<a title="Modificar" onclick="updDat('."'".$row['id']."','".trim(utf8_encode($row['descr']))."'".')"><span style="font-size:14px" class="glyphicon glyphicon-pencil"></span></a>'."</td>";
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
                                                <input type="hidden" id="uuser" name="uuser" />
                                                <input type="text" id="desc" name="desc" class="form-control" placeholder="Descripción" value=""/>
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
                                        <div class="col-lg-12 col-sm-12" >
                                            <div class="input-group">                                                            
                                                <input type="text" id="add_desc" name="desc" class="form-control" placeholder="Descripción" value=""/>
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
                $('#par1').addClass('active');
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
            function updDat(id, desc){
                $(".modal-title").empty().append('Modificar ');
                $("#uuser").attr('value',id);
                $("#desc").attr('value',desc);
                $('#editUsr').modal('show');
            }
            function saveAdd(){
                var desc = $("#add_desc").val();
                
                if(desc != ''){
                    $.ajax({ 
                        type: 'POST',
                    url: 'requests/saveData.php', 
                        data: {
                            action:'add',
                            desc: desc,
                            table: 'pri_doc'
                        },success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                window.location = 'p_docu.php';
                            }
                        }
                    }); 
                }else{
                    alert('Favor ingresar una descripción');
                }
            }
            function saveUpd(){
                var id = $("#uuser").val();
                var desc = $("#desc").val();
                $.ajax({ 
                    type: 'POST',
                    url: 'requests/saveData.php', 
                    data: {
                        action:'upd',
                        id: id,
                        desc: desc,
                        table:'pri_doc'
                    },success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.err == 0){
                            window.location = 'p_docu.php';
                        }
                    }
                }); 
            }
        </script>
    </body>
</html>
