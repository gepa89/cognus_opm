<?php 


require('conect.php');
//require_once("modelos/almacen.php");
//include 'src/adLDAP.php';
require_once(__DIR__ . "/utils/auth.php");

verificarUsuarioLogueado();
//verificarPermisoEscritura("externo", "ingreso_pedido");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
//$almacenesDB = Almacenes::obtenerAlmacenes();
$almacen_seleccionado = 'CD11';
$sq = "SELECT DISTINCT
pedexentre,
codenv,
clinom,
almrefer 
FROM
pedexcab
LEFT JOIN clientes ON pedexcab.clirefer = clientes.clirefer 
WHERE
siturefe NOT IN ( 'CE', 'AN' ) 
AND pedexfec >= CURDATE()
AND almrefer='$almacen_seleccionado'";
//
//AND pedexfec =date(now())
//GROUP BY codenv
$ren = $db->query($sq);
$cc = 0;
$spd = array();
while ($ax = $ren->fetch_assoc()) {
  if ($ax['codenv'] == '') {
    $spd['entrega'][99][$cc]['cod'] = $ax['pedexentre'];
    $spd['entrega'][99][$cc]['cli'] = $ax['clinom'];
  } else {
    $spd['entrega'][$ax['codenv']][$cc]['cod'] = $ax['pedexentre'];
    $spd['entrega'][$ax['codenv']][$cc]['cli'] = $ax['clinom'];
  }
  $cc++;
}



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

        <form class="form-horizontal" method="GET">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-addon">Almacén:</span>
                    <select class="form-control" name="almacen" id="almacen">
                      <option <?php if ($almacen_seleccionado == "todos")
                        echo "selected" ?> value="">Todos</option>
                        <?php
                      if ($almacenesDB) {
                        foreach ($almacenesDB as $almacenDB) {
                          ?>
                          <option <?php if ($almacen_seleccionado == $almacenDB->almcod)
                            echo "selected" ?>
                            value="<?php echo $almacenDB->almcod; ?>">
                            <?php echo $almacenDB->almdes ?>
                          </option>
                          <?php
                        }
                      } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="input-group">
                    <button class="btn btn-primary">Buscar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>
        <div class="row">
          <div class="col-lg-8 col-sm-8 col-xs-12">
            <div class="row">
              <h3 class="heading">Expedición</h3>
              <div class="col-lg-6 col-sm-6 col-xs-12">
                <h3 class="heading">Situación de Pedidos</h3>
                <div class="col-sm-12">
                  <div id="gr_1" style="height:400px;width:100%;margin:25px auto 0"></div>
                </div>
              </div>
              <div class="col-lg-6 col-sm-6 col-xs-12">
                <h3 class="heading">Ventas</h3>
                <div class="col-sm-12">
                  <div id="gr_2" style="height:200px;width:100%;"></div>
                </div>
                <h3 class="heading">Transferencias</h3>
                <div class="col-sm-12">
                  <div id="gr_3" style="height:200px;width:100%;"></div>
                </div>
              </div>
            </div>
            <div class="row">
              <h3 class="heading">Recepción</h3>
              <div class="col-lg-6 col-sm-6 col-xs-12">
                <h3 class="heading">Compras</h3>
                <div class="col-sm-12">
                  <div id="gr_5" style="height:200px;width:100%;"></div>
                </div>
                <h3 class="heading">Transferencias</h3>
                <div class="col-sm-12">
                  <div id="gr_6" style="height:200px;width:100%;"></div>
                </div>
              </div>
              <div class="col-lg-6 col-sm-6 col-xs-12">
                <h3 class="heading">Situación de Pedidos</h3>
                <div class="col-sm-12">
                  <div id="gr_4" style="height:400px;width:100%;margin:25px auto 0"></div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 col-sm-12 col-xs-12">
                <h3 class="heading">Ranking</h3>
                <div class="col-sm-12">
                  <div id="gr_20" style="height:400px;width:100%;margin:25px auto 0"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-sm-4 col-xs-12">
            <div class="row">
              <h3 class="heading">Pendientes Por Franjas</h3>
              <div class="col-lg-12 col-sm-12 col-xs-12">
                <h3 class="heading">Total Pedidos:
                  <?php echo '' . $cc . '' ?>
                </h3>
                <div id="accordion1" class="panel-group accordion">
                  <?php
                  $cct = 0;
                  if ($spd) {
                    ksort($spd['entrega']);
                    foreach ($spd['entrega'] as $k => $fr) {
                      ?>
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <a href="#copse<?php echo $cct; ?>" data-parent="#accordion1" data-toggle="collapse"
                            class="accordion-toggle collapsed">
                            <?php echo ($k == 99) ? "Sin Franja" : $k;
                            echo ' (' . count($fr) . ')'; ?>
                          </a>
                        </div>
                        <div class="panel-collapse collapse" id="copse<?php echo $cct; ?>">
                          <div class="panel-body">
                            <table class="table table-hover table-striped table-bordered  table-condensed dTableR"
                              style="font-size: 12px !important;">
                              <thead>
                                <tr>
                                  <th>Pedido</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                foreach ($fr as $k2 => $frs) {
                                  echo "<tr>";
                                  echo "    <th>" . $frs['cod'] . " - " . $frs['cli'] . "</th>";
                                  echo "</tr>";
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <?php
                      $cct++;
                    }
                  }
                  ?>
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
      $('#dashb').addClass('active');
      //                $('#c1').click();
    });
  </script>
</body>

</html>