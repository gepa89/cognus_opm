<?php
require('conect.php');
require_once(__DIR__ . "/utils/auth.php");
session_start();
verificarUsuarioLogueado();
if (!tieneAccesoAModulo("maestros")) {
  echo "No tiene acceso";
  exit;
}

$db = new mysqli($SERVER, $USER, $PASS, $DB);
// echo "<pre>"; var_dump($data);echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

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
    <?php include 'header.php' ?>
    <div id="contentwrapper">
      <div class="main_content">
        <div class="row">
          <form id="form_filtros" method="get">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h3 class="panel-title">Administrar Proveedores</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="buscar">Buscar:</label>
                      <input type="search" class="form-control" name="buscar" required id="buscar">
                    </div>
                  </div>
                  <div class="col-md-3 ">
                    <div class="form-group">
                      <label for="buscar">&nbsp;</label>
                      <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-12">
            <?php

            echo '<table class="table">'
              . '<thead>'
              . '<tr>'
              . '<th colspan="">Agregar Proveedor</th>'
              . '</tr>'
              . '</thead>'
              . '</table>';

            ?>
              <div style="overflow-x: auto; max-width: 100%;">
                 <table class="table table-hover table-striped table-bordered dTableR dataTable no-footer" id="tbl_listb" style="font-size: 11px !important; width: 100%;" 
                    role="grid" 
                    aria-describedby="tbl_listb_info">

                  <thead>
                    <tr>
                      <th>Cod.Proveedor</th>
                      <th>Nombre</th>
                      <th>Pais</th>
                      <th>Region</th>
                      <th>Moneda</th>
                      <th>Tip.Doc.</th>
                      <th>Documento</th>
                      <th>Mail</th>
                      <th>Telefono</th>
                      <th>Incoterm</th>
                      <th>Cond.Pago</th>
                      <th>Ramo</th>
                      <th>Cuenta</th>
                      <th>Fec.Creacion</th>
                      <th>Creado Por</th>
                      <th>Fec.Modifiacion</th>
                      <th>Modificado Por</th>
                      <th>Acci&oacute;n</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
            </div>      
          </div>
        </div>
      </div>
      <div style="clear:both;"></div>
      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="editUsr" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
              <form method="post" id="eForm">
              <h3 class="heading"><strong>Datos Proveedor</strong></h3><!-- comment -->        
               <div class="row">
                   <div class="col-sm-2">
                    <div class="form-group">
                      <label for="buscar">Cod.Proveedor</label>
                      <input type="text" id="updCpr" name="updCpr" class="form-control"
                        placeholder="" value="" disabled="true" />
                    </div>
                  </div>
                   <div class="col-sm-6">
                    <div class="form-group">
                      <label for="buscar">Nombre Proveedor</label>
                      <input type="text" id="updNom" name="updNom" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>
                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Tip.Doc.:</label>
                          <select id="updTdoc" required name="updTdoc" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="CI">CI</option>
                            <option value="RUC">RUC</option>
                          </select>
                        </div>
                </div>
                   <div class="col-sm-2">
                    <div class="form-group">
                      <label for="buscar">Documento</label>
                      <input type="text" id="updDoc" name="updDoc" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>

                </div>
                  <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Pais</label>
                          <select id="updPai" class="form-control">
                            <?php
                            $sq = "select * from paises";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codpais'] . '">' . $ax['codpais'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Region</label>
                          <select id="updReg" class="form-control">
                            <?php
                            $sq = "select * from region";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codregion'] . '">' . $ax['codregion'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                       <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Incoterm</label>
                          <select id="updInc" class="form-control">
                            <?php
                            $sq = "select * from incoterm";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['incote'] . '">' . $ax['incote'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                 </div>
                
                <div class="row">
                   <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Tipo Proveedor</label>
                          <select id="updTpr" class="form-control">
                            <?php
                            $sq = "select * from tipoprov";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['tiprove'] . '">' . $ax['tiprove'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>

                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Cond.Pago:</label>
                          <select id="updCpa" required name="updCpa" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="PC00">Contado</option>
                            <option value="PC30">30 dias</option>
                            <option value="PC30">60 dias</option>
                          </select>
                        </div>
                </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Ramo</label>
                          <select id="updRam" class="form-control">
                            <?php
                            $sq = "select * from tipoprov";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['tiprove'] . '">' . $ax['tiprove'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Moneda:</label>
                          <select id="updMon" required name="updMon" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="PYG">PYG</option>
                            <option value="USD">USD</option>
                            
                          </select>
                        </div>
                </div>
                </div>  
                  <div class="row">
                  <div class="col-sm-6">
                        <div class="form-group">
                          <label for="buscar">Direccion Correo</label>
                          <input type="text" id="updDco" name="updDco" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>

                   <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Telefono</label>
                          <input type="text" id="updTel" name="updTel" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                    
                 
                </div>  
                  <h3 class="heading"><strong>Datos Bancarios</strong></h3><!-- comment -->     
                <div class="row">
                  
                    <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Banco</label>
                          <select id="updBan" class="form-control">
                            <?php
                            $sq = "select * from bancos";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codbanco'] . '">' . $ax['codbanco'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>  
                  <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Cuenta</label>
                          <input type="text" id="updCue" name="updCue" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                     <div class="col-sm-5">
                        <div class="form-group">
                          <label for="buscar">Titular</label>
                          <input type="text" id="updTit" name="updTit" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>

                </div>    
                <div class="row">
                  
                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Tip.Doc.:</label>
                          <select id="updTdo" required name="updTdo" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="CI">CI</option>
                            <option value="RUC">RUC</option>
                          </select>
                        </div>
                </div> 
                   <div class="col-sm-5">
                        <div class="form-group">
                          <label for="buscar">Documento</label>
                          <input type="text" id="updDocb" name="updDocb" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                    
                 
                  <div style="clear:both;"></div><br /><br />

                  <div style="clear:both;"></div><br /><br />
                  <div class="col-lg4 col-sm-4">
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
      <div class="modal fade bd-example-modal-lg" id="addUsr" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
              <form method="post" id="eForm">
              <h3 class="heading"><strong>Datos Proveedor</strong></h3><!-- comment -->        
               <div class="row">
                   <div class="col-sm-7">
                    <div class="form-group">
                      <label for="buscar">Nombre Proveedor</label>
                      <input type="text" id="addNom" name="addNom" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>
                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Tip.Doc.:</label>
                          <select id="addTdoc" required name="addTdoc" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="CI">CI</option>
                            <option value="RUC">RUC</option>
                          </select>
                        </div>
                </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label for="buscar">Documento</label>
                      <input type="text" id="addDoc" name="addDoc" class="form-control"
                        placeholder="" value="" />
                    </div>
                  </div>

                </div>
                  <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Pais</label>
                          <select id="addPai" class="form-control">
                            <?php
                            $sq = "select * from paises";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codpais'] . '">' . $ax['codpais'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Region</label>
                          <select id="addReg" class="form-control">
                            <?php
                            $sq = "select * from region";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codregion'] . '">' . $ax['codregion'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                       <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Incoterm</label>
                          <select id="addInc" class="form-control">
                            <?php
                            $sq = "select * from incoterm";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['incote'] . '">' . $ax['incote'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                 </div>
                
                <div class="row">
                   <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Tipo Proveedor</label>
                          <select id="addTpr" class="form-control">
                            <?php
                            $sq = "select * from tipoprov";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['tiprove'] . '">' . $ax['tiprove'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>

                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Cond.Pago:</label>
                          <select id="addCpa" required name="addCpa" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="PC00">Contado</option>
                            <option value="PC30">30 dias</option>
                            <option value="PC30">60 dias</option>
                          </select>
                        </div>
                </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Ramo</label>
                          <select id="addRam" class="form-control">
                            <?php
                            $sq = "select * from tipoprov";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['tiprove'] . '">' . $ax['tiprove'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Moneda:</label>
                          <select id="addMon" required name="addMon" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="PYG">PYG</option>
                            <option value="USD">USD</option>
                            
                          </select>
                        </div>
                </div>
                </div>  
                  <div class="row">
                  <div class="col-sm-6">
                        <div class="form-group">
                          <label for="buscar">Direccion Correo</label>
                          <input type="text" id="addDco" name="addDco" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>

                   <div class="col-sm-4">
                        <div class="form-group">
                          <label for="buscar">Telefono</label>
                          <input type="text" id="addTel" name="addTel" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                    
                 
                </div>  
                  <h3 class="heading"><strong>Datos Bancarios</strong></h3><!-- comment -->     
                <div class="row">
                  
                    <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Banco</label>
                          <select id="addBan" class="form-control">
                            <?php
                            $sq = "select * from bancos";
                            $rs = $db->query($sq);
                            echo '<option value="">Seleccionar</option>';
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['codbanco'] . '">' . $ax['codbanco'] . ' - ' . utf8_encode($ax['nombre']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>  
                  <div class="col-sm-3">
                        <div class="form-group">
                          <label for="buscar">Cuenta</label>
                          <input type="text" id="addCue" name="addCue" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                     <div class="col-sm-5">
                        <div class="form-group">
                          <label for="buscar">Titular</label>
                          <input type="text" id="addTit" name="addTit" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>

                </div>    
                <div class="row">
                  
                   <div class="col-sm-2">
                        <div class="form-group">
                          <label for="proveedor">Tip.Doc.:</label>
                          <select id="addTdo" required name="addTdo" class="js-data-example-ajax form-control">
                            <option selected disabled value=""></option>
                            <option value="CI">CI</option>
                            <option value="RUC">RUC</option>
                          </select>
                        </div>
                </div> 
                   <div class="col-sm-5">
                        <div class="form-group">
                          <label for="buscar">Documento</label>
                          <input type="text" id="addDocb" name="addDocb" class="form-control"
                            placeholder="" value="" />
                        </div>
                      </div>
                    
                 
                  <div style="clear:both;"></div><br /><br />

                  <div style="clear:both;"></div><br /><br />
                  <div class="col-lg4 col-sm-4">
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/es.js"></script>
  <script type="text/javascript">
    var table = null;
    function crearTabla(url) {
      table = $('#tbl_listb').DataTable({
        bFilter: true,
        dom: '<"top"B<lfrtip>><"clear">',
        buttons: [
          {
            text: 'Añadir',
            action: function (e, dt, node, config) {
              addUsr();
            }
          }
        ],
        bInfo: true,
        bLengthChange: true,
        destroy: true,
        processing: true,
        serverSide: true,
        paging: true,
        ajax: url,
        searching: false,
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
        },
        columns: [
          { data: 'codprove' },
          { data: 'nombre' },
          { data: 'codpais' },
          { data: 'codregion' },
          { data: 'codmone' },
          { data: 'tipdoc' },
          { data: 'documento' },
          { data: 'mail' },
          { data: 'telefono' },
          { data: 'incote' },
          { data: 'condpago' },
          { data: 'ramo' },
          { data: 'cuenta' },
          { data: 'fecre', orderable: false },
          { data: 'usuario', orderable: false },
          { data: 'fecmod', orderable: false },
          { data: 'usermod', orderable: false },
          
          {
            mRender: function (data, type, row) {
              let texto = `<a title="Modificar Material">
                                            <span style="font-size:14px" onclick="updDat('${row.codprove}','${row.nombre}','${row.codpais}','${row.codregion}','${row.codmone}','${row.tipdoc}','${row.documento}','${row.mail}','${row.telefono}','${row.incote}','${row.condpago}','${row.ramo}','${row.cuenta}','${row.tiprove}','${row.banco}','${row.titular}','${row.tipdocb}','${row.documentob}','${row.fecre}','${row.usuario}','${row.fecmod}','${row.usermod}')" class="glyphicon glyphicon-pencil"></span></a>
                                         <a title="Eliminar Bultos">
                                            <span style="font-size:14px" onclick="eliminarBultos('${row.tipbul}')" class="glyphicon glyphicon-trash"></span></a>`;
              return texto;
            }
          }
        ],
      });
    }
    $(document).ready(function () {
      $('#maest55').addClass('active');
      $('#c4').click();
      let url = 'requests/obtenerProveedor.php';
      crearTabla(url);
      $("#form_filtros").submit(function (e) {
        e.preventDefault();
        let buscar = $("#buscar").val();
        let url = `requests/obtenerProveedor.php?buscar=${buscar}`;
        if (table != null) {
          table.destroy();
          table = null;
        }
        crearTabla(url);
        return false;
      })

    });

    function addUsr() {
      $("#addUsr .modal-title").empty().append('Añadir ');
      $('#addUsr').modal('show');
    }
    function updDat(addCpr,addNom,addPai,addReg,addMon,addTdoc,addDoc,addDco,addTel,addInc,addCpa,addRam,addCue,addTpr,addBan,addTit,addTdo,addDocb) {
      $("#editUsr .modal-title").empty().append('Editar ');
      $("#updCpr").val(addCpr);
      $("#updNom").val(addNom);
      $("#updPai").val(addPai);
      $("#updReg").val(addReg);
      $("#updMon").val(addMon);
      $("#updTdoc").val(addTdoc);
      $("#updDoc").val(addDoc);
      $("#updDco").val(addDco);
      $("#updTel").val(addTel);
      $("#updInc").val(addInc);
      $("#updCpa").val(addCpa);
      $("#updRam").val(addRam);
      $("#updCue").val(addCue);
      $("#updTpr").val(addTpr);
      $("#updBan").val(addBan);
      $("#updTit").val(addTit);
      $("#updTdo").val(addTdo);
      $("#updDocb").val(addDocb);
      
      //                $("#updAlm").val(addAlm).change();

      $('#editUsr').modal('show');
    }

    function eliminarBultos(bul) {
      $.ajax({
        type: 'POST',
        url: 'requests/eliminarBultos.php',
        data: {
          pue: pue
          
        }, success: function (data) {
          console.log(data);
          table.ajax.reload();
        }
      });
    }
    function saveAdd() {
      var Cpr = $("#addCpr").val();  
      var Nom = $("#addNom").val();
      var Tdoc = $("#addTdoc").val();
      var Doc = $("#addDoc").val();
      var Pai = $("#addPai").val();
      var Reg = $("#addReg").val();
      var Inc = $("#addInc").val();
      var Mon = $("#addMon").val();
      var Tpr = $("#addTpr").val();
      var Cpa = $("#addCpa").val();
      var Ram = $("#addRam").val();
      var Dco = $("#addDco").val();
      var Tel = $("#addTel").val();
      var Ban = $("#addBan").val();
      var Cue = $("#addCue").val();
      var Tit = $("#addTit").val();
      var Tdo = $("#addTdo").val();
      var Docb = $("#addDocb").val();


      if (Cpr != '') {
        $.ajax({
          type: 'POST',
          url: 'requests/saveProveedor.php',
          data: {
            action: 'add',
            Cpr: Cpr,
            Nom: Nom,
            Tdoc: Tdoc,
            Doc: Doc,
            Pai: Pai,
            Reg: Reg,
            Inc: Inc,
            Tpr: Tpr,
            Cpa: Cpa,
            Ram: Ram,
            MOn: Mon,
            Dco: Dco,
            Tel: Tel,
            Ban: Ban,
            Cue: Cue,
            Tit: Tit,
            Tdo: Tdo,
            Docb: Docb,
            

            table: 'proveedores',
            fields: 'nombre,tipdoc,documento,codpais,codregion,incote,tiprove,condpago,codmone,ramo,mail,telefono,banco,cuenta,titular,tipdocb,documentob'
          }, success: function (data) {
            var dt = JSON.parse(data);
            alert(dt.msg);
            if (dt.err == 0) {
              window.location = 'crea_proveedor.php';
            }
          }, error: function (request) {
            alert(request.responseJSON.error);
          }
        });
      } else {
        alert('Favor ingresar una descripción');
      }
    }
    function saveUpd() {
      var Cpr = $("#updCpr").val();
      var Nom = $("#updNom").val();
      var Tdoc = $("#updTdoc").val();
      var Doc = $("#updDoc").val();
      var Pai = $("#updPai").val();
      var Reg = $("#updReg").val();
      var Inc = $("#updInc").val();
      var Mon = $("#updMon").val();
      var Tpr = $("#updTpr").val();
      var Cpa = $("#updCpa").val();
      var Ram = $("#updRam").val();
      var Dco = $("#updDco").val();
      var Tel = $("#updTel").val();
      var Ban = $("#updBan").val();
      var Cue = $("#updCue").val();
      var Tit = $("#updTit").val();
      var Tdo = $("#updTdo").val();
      var Docb = $("#updDocb").val();
     
  

      //                if(desc != ''){
      $.ajax({
        type: 'POST',
        url: 'requests/saveProveedor.php',
        data: {
          action: 'upd',
            Cpr: Cpr,
            Nom: Nom,
            Tdoc: Tdoc,
            Doc: Doc,
            Pai: Pai,
            Reg: Reg,
            Inc: Inc,
            Tpr: Tpr,
            Cpa: Cpa,
            Ram: Ram,
            Mon: Mon,
            Dco: Dco,
            Tel: Tel,
            Ban: Ban,
            Cue: Cue,
            Tit: Tit,
            Tdo: Tdo,
            Docb: Docb,
         
         
          table: 'proveedores',
          fields: 'codprove,nombre,tipdoc,documento,codpais,codregion,incote,tiprove,condpago,codmone,ramo,mail,telefono,banco,cuenta,titular,tipdocb,documentob'
        }, success: function (data) {
          var dt = JSON.parse(data);
          alert(dt.msg);
          if (dt.err == 0) {
            window.location = 'crea_proveedor.php';
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