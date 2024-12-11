<?php
//echo 1;
require('conect.php');
//include 'src/adLDAP.php';
if (!isset($_SESSION['user'])) {
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
    .c_red {
      background-color: #ffafaf;
    }

    .c_yellow {
      background-color: #effc8b;
    }

    .c_green {
      background-color: #8eff91;
    }

    .c_blue {
      background-color: #717eff;
    }

    .c_gray {
      background-color: #878787;
    }

    .c_orange {
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
          <div class="col-lg-12 col-sm-12 col-xs-12">
            <h3 class="heading">Imprimir Etiquetas</h3>
            <ul class="dshb_icoNav clearfix">

              <li><a href="javascript:void(0)" onclick="swModal('etiquetaModal')"
                  style="background-image: url(img/gCons/connections.png);">Etiqueta Re/Ex</a></li>
              <li><a href="javascript:void(0)" onclick="swModal('ubicacionModal')"
                  style="background-image: url(img/gCons/connections.png);">Ubicación</a></li>
              <li><a href="javascript:void(0)" onclick="swModal('eanModal')"
                  style="background-image: url(img/gCons/connections.png);">EAN</a></li>
            </ul>
          </div>
          <div class="modal fade bd-example-modal-lg" id="eanModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Etiquetas EAN</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form">
                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">
                      <input autocomplete="on" autocomplete="on" type="text"
                        class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etEanList" value="" placeholder="EAN">
                    </div>
                  </div>
                  <div style="clear:both;"></div>

                  <div style="clear:both;"></div>
                  <div id="toPrintEan" class=""></div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="previewEtEan" onclick="genEan()" class="btn btn-primary"><i
                      class="splashy-zoom"></i> Generar</button>
                  <button type="button" id="printEtEan" disabled="disabled" onclick="impEtEan()"
                    class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade bd-example-modal-lg" id="etiquetaModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Etiquetas Recepción / Expedición</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form">
                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">
                      <select class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etTip">
                        <option value="">Seleccionar</option>
                        <option value="RE">Recepción</option>
                        <option value="EX">Expedición</option>
                      </select>
                    </div>
                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">
                      <input autocomplete="on" autocomplete="on" type="number"
                        class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etCantidad" value=""
                        placeholder="Cantidad">
                    </div>
                    <div class="formSep col-lg-6 col-sm-6 col-xs-12 form-group">
                      <input autocomplete="on" autocomplete="on" type="text"
                        class="col-lg-12 col-sm-12 col-xs-12 form-control" id="etList" value=""
                        placeholder="Nro. Etiqueta">
                    </div>
                  </div>
                  <div style="clear:both;"></div>

                  <div style="clear:both;"></div>
                  <div id="toPrint" class=""></div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="previewEt" onclick="genEtiqueta()" class="btn btn-primary"><i
                      class="splashy-zoom"></i> Generar</button>
                  <button type="button" id="printEt" disabled="disabled" onclick="impEtReEx()" class="btn btn-danger"><i
                      class="splashy-printer"></i> Imprimir</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade bd-example-modal-lg" id="ubicacionModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Etiquetas Ubicación</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form">
                    <div class="row">
                      <div class="col-md-6">
                        <div style="clear:both;"></div><br />

                        <div class="input-group">
                          <label class="label">Almacen:</label>
                          <select class="form-control" id="codalma" name="codalma">
                            <?php
                            $sql = "select * from alma";
                            $rs = $db->query($sql);
                            while ($ax = $rs->fetch_assoc()) {
                              echo '<option value="' . $ax['almcod'] . '">' . $ax['almcod'] . ' - ' . $ax['almdes'] . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="clear:both;"></div><br />

                        <div class="input-group">
                          <label class="label">Formato Impresion:</label>
                          <select class="form-control" id="formato_impresion">
                           <option value="1">95mm x 50mm</option>
                           <option value="0">70mm x 20mm</option>
                           <option value="2">100mm x 130mm</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-5 col-sm-5">
                      <label class="label" style="color: #222;">Estanteria:</label>
                      <div style="clear:both;"></div><br />
                      <div class="input-group input-daterange">
                        <input autocomplete="off" type="text" name="dEstante" id="dEstante" class="form-control"
                          placeholder="Desde" />
                        <div class="input-group-addon"> hasta </div>
                        <input autocomplete="off" type="text" name="hEstante" id="hEstante" class="form-control"
                          placeholder="Hasta" />
                      </div>
                    </div>
                    <div class="col-lg-5 col-sm-5">
                      <label class="label" style="color: #222;">Hueco:</label>
                      <div style="clear:both;"></div><br />
                      <div class="input-group input-daterange">
                        <input autocomplete="off" type="text" name="dHueco" id="dHueco" class="form-control"
                          placeholder="Desde" />
                        <div class="input-group-addon"> hasta </div>
                        <input autocomplete="off" type="text" name="hHueco" id="hHueco" class="form-control"
                          placeholder="Hasta" />
                      </div>
                    </div>
                    <div style="clear:both;"></div><br />
                    <div class="col-lg-5 col-sm-5">
                      <label class="label" style="color: #222;">Nivel:</label>
                      <div style="clear:both;"></div><br />
                      <div class="input-group input-daterange">
                        <input autocomplete="off" type="text" name="dNiv" id="dNiv" class="form-control"
                          placeholder="Desde" />
                        <div class="input-group-addon"> hasta </div>
                        <input autocomplete="off" type="text" name="hNiv" id="hNiv" class="form-control"
                          placeholder="Hasta" />
                      </div>
                    </div>
                    <div class="col-lg-5 col-sm-5">
                      <label class="label" style="color: #222;">Sub Nivel:</label>
                      <div style="clear:both;"></div><br />
                      <div class="input-group input-daterange">
                        <input autocomplete="off" type="number" name="dSubNiv" id="dSubNiv" class="form-control"
                          placeholder="Desde" min="1" max="26" onkeyup="if(this.value > 26) this.value = null;" />
                        <div class="input-group-addon"> hasta </div>
                        <input autocomplete="off" type="number" name="hSubNiv" id="hSubNiv" class="form-control"
                          placeholder="Hasta" min="1" max="26" onkeyup="if(this.value > 26) this.value = null;" />
                      </div><br />
                    </div>
                  </div>
                  <div style="clear:both;"></div>

                  <div style="clear:both;"></div>
                  <div id="toPrintUbi" style="max-height: 400px !important; overflow-y: auto;" class=""></div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="previewEt" onclick="genEtiUbi()" class="btn btn-primary"><i
                      class="splashy-zoom"></i> Generar</button>
                  <button type="button" id="printEtUbi" disabled="disabled" onclick="impEtUb()"
                    class="btn btn-danger"><i class="splashy-printer"></i> Imprimir</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
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
    var dex = [['', '', '', '', '']];
    function arrayHasEmptyStrings(datArr) {
      for (var index = 0; index < datArr.length; index++) {
        if (jQuery.inArray('', datArr[index]) == -1) {
          for (var ix = 0; ix < datArr[index].length; ix++) {
            if (datArr[index][ix] == '') {
              //                                console.log(true);
              return true;
            } else {
              //                                console.log(false);
              return false;
            }
          }
        } else {
          //                        console.log(true);
          return true;
        }
      }
    }
    $(document).ready(function () {
      $('#impre').addClass('active');
      //                $('#c1').click();
    });
  </script>
</body>

</html>