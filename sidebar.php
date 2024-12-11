<?php
require_once(__DIR__ . '/utils/auth.php');
require_once(__DIR__ . '/modelos/roles_permisos.php');

//require ('/var/www/html/hanaDB.php');
// require ('/var/www/html/conect.php');

// ["SCRIPT_NAME"]=>  string(27) "general.php"
// require_once $_SERVER["DOCUMENT_ROOT"] . 'class/crmtiDb.php';
// $SERVER = '127.0.0.1';
//$USER = 'root';
//$PASS = 'gepa5266';
//$DB = 'portal_compras';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}



if ($_SESSION['user_rol'] == 2) {
    $solicitante = " and ERNAM = '" . $_SESSION['user'] . "'";
} else {
    $solicitante = '';
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$roles_permisos = new RolesPermisos();
?>
<a href="javascript:void(0)" class="sidebar_switch on_switch bs_ttip" data-placement="auto right" data-viewport="body" title="Ocultar lateral">Sidebar switch</a>
<div class="sidebar" style="    top: 47px !important;">

    <div class="sidebar_inner_scroll">
        <div class="sidebar_inner">
            <div id="side_accordion" class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <ul class="nav nav-pills nav-stacked">
                            <li id="dashb"><a href="dashboard.php?almacen=CD11"><i class="glyphicon glyphicon-dashboard"></i>
                                    Dashboard</a></li>
                            <li id="dashb"><a target="_blank" href="dashboard_show.php"><i class="glyphicon glyphicon-stats"></i> Productividad</a></li>
                            <li id="impre"><a href="impresiones.php"><i class="glyphicon glyphicon-print"></i>
                                    Impresiones</a></li>
                            <li id="impre"><a href="panel.php"><i class="glyphicon glyphicon-stats"></i>
                                    Estadisticas</a></li>
                        </ul>
                    </div>
                </div>
                <?php
                
                if (tieneAccesoAModulo("pagos")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsepag" id="pagos" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-list-alt"></i> Pagos a Proveedores
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsepag">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("pagos", "pag1", "leer")) { ?>
                                    <li id="pag1"><a href="programa_pagos.php"><i class="glyphicon glyphicon-bookmark"></i>
                                                Programar Pagos</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="pag2"><a href="panel_calendario.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Calendario descargas</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="pag3"><a href=""><i class="glyphicon glyphicon-screenshot"></i>
                                                Reporte de Descargas</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("pisos")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsedesc" id="d1" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-list-alt"></i> Programar Descargas
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsedesc">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("pisos", "capaubi", "leer")) { ?>
                                    <li id="desca1"><a href="programa_descarga.php"><i class="glyphicon glyphicon-bookmark"></i>
                                                Programar Descargas</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="desca2"><a href="panel_calendario.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Calendario descargas</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="desca3"><a href=""><i class="glyphicon glyphicon-screenshot"></i>
                                                Reporte de Descargas</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("vui")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsevui" id="vui" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-th-large"></i> VUI
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsevui">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("vui", "vui1", "leer")) { ?>
                                        <li id="vui1"><a href="solicita_flete.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Crear Pre Declaracion</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="vui2"><a href="dimension.php"><i class="glyphicon glyphicon-fullscreen"></i>
                                                Orden de Pago</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="flet3"><a href="diasmezcla.php"><i class="glyphicon glyphicon-screenshot"></i>
                                                Documentos de Embarque</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("fletes")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsefle" id="fletes" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-th-large"></i> Fletes
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsefle">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("fletes", "flet1", "leer")) { ?>
                                        <li id="flet1"><a href="solicita_flete.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Contratar Fletes</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="flet2"><a href="consulta_pedfletes.php"><i class="glyphicon glyphicon-fullscreen"></i>
                                                Consultar Sol.Fletes</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="flet3"><a href="mod_pedfletes.php"><i class="glyphicon glyphicon-screenshot"></i>
                                                Modificar Fletes</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                
                if (tieneAccesoAModulo("seguros")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsegur" id="seguros" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-th-large"></i> Seguros
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsegur">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("seguros", "segur1", "leer")) { ?>
                                        <li id="segur1"><a href="solicita_seguro.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Contratar Seguro</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="segur2"><a href="dimension.php"><i class="glyphicon glyphicon-fullscreen"></i>
                                                Dimensiones</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="segur3"><a href="diasmezcla.php"><i class="glyphicon glyphicon-screenshot"></i>
                                                Dias Mezcla</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                 if (tieneAccesoAModulo("despachos")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsedes" id="despachos" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-th-large"></i> Despachos
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsedes">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("despachos", "despa1", "leer")) { ?>
                                        <li id="despa1"><a href="asigna_despachante.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Asignar Despachante</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="despa2"><a href="dimension.php"><i class="glyphicon glyphicon-fullscreen"></i>
                                                Dimensiones</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="despa3"><a href="diasmezcla.php"><i class="glyphicon glyphicon-screenshot"></i>
                                                Dias Mezcla</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                 if (tieneAccesoAModulo("seguimiento")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsegui" id="seguimiento" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-list-alt"></i> Seguimiento de Pedidos
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsegui">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("seguimiento", "segui1", "leer")) { ?>
                                    <li id="segui1"><a href="con_logistica.php"><i class="glyphicon glyphicon-bookmark"></i>
                                                Seguimiento Logistico</a></li>
                                      <?php } ?>           
                                     <?php if (tienePermiso("seguimiento", "segui2", "leer")) { ?>
                                    <li id="segui2"><a href="con_finanzas.php"><i class="glyphicon glyphicon-bookmark"></i>
                                                Seguimiento Financiero</a></li>            
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="segui2"><a href="panel_calendario.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Calendario descargas</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="segui1"><a href=""><i class="glyphicon glyphicon-screenshot"></i>
                                                Reporte de Descargas</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
            /*    if (tieneAccesoAModulo("pisos")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsepi" id="p1" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-th-large"></i> Trabajos en Pisos
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsepi">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                   
                                    <?php if (tienePermiso("pisos", "capaubi", "leer")) { ?>
                                        <li id="capa1"><a href="capaci_ubi.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Capacidad Ubicacion</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "dimension", "leer")) { ?>
                                        <li id="capa2"><a href="dimension.php"><i class="glyphicon glyphicon-fullscreen"></i>
                                                Dimensiones</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("pisos", "diasmezcla", "leer")) { ?>
                                        <li id="capa3"><a href="diasmezcla.php"><i class="glyphicon glyphicon-screenshot"></i>
                                                Dias Mezcla</a></li>
                                    <?php } ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }*/
                if (tieneAccesoAModulo("externo")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsex" id="c3" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-lock"></i> Administrar Pedidos
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsex">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->
                                    <!--  <?php if (tienePermiso("externo", "articulo", "leer")) { ?>
                                        <li id="ingre1"><a href="c_arti.php"><i class="glyphicon glyphicon-picture"></i> Crear
                                                Articulo</a></li>
                                    <?php } ?>-->
                                    <?php if (tienePermiso("externo", "articulos", "leer")) { ?>
                                        <li id="ingre2"><a href="crea_arti.php"><i class="glyphicon glyphicon-picture"></i> Crear
                                                Articulos</a></li>
                                    <?php } ?>
                                                
                                  <!--     <?php if (tienePermiso("externo", "ingreso_pedidos", "escribir")) { ?>
                                        <li id="ingre1"><a href="ingreso_pedidos.php"><i class="glyphicon glyphicon-user"></i>
                                                Ingresos Documentos</a></li>-->
                                    <?php } ?>
                                                
                                     <?php if (tienePermiso("externo", "pedprove", "escribir")) { ?>
                                        <li id="ingre1"><a href="crea_pedprove.php"><i class="glyphicon glyphicon-user"></i>
                                                Crear Pedido al Prov. </a></li>
                                    <?php } ?>  
                                                
                                    <?php if (tienePermiso("pedoferta", "pedoferta", "escribir")) { ?>
                                        <li id="ingre1"><a href="crea_oferta.php"><i class="glyphicon glyphicon-user"></i>
                                                Crear Oferta </a></li>
                                    <?php } ?>   
                                    
                                    <?php if (tienePermiso("externo", "pedprove", "escribir")) { ?>
                                        <li id="ingre1"><a href="crea_contratos.php"><i class="glyphicon glyphicon-user"></i>
                                                Crear Contratos </a></li>
                                    <?php } ?> 
                                                
                                      <?php if (tienePermiso("externo", "pedprove", "escribir")) { ?>
                                        <li id="ingre1"><a href="crea_oc.php"><i class="glyphicon glyphicon-user"></i>
                                                Crear Orden de Compra </a></li>
                                    <?php } ?>            
                                                
                                    <?php if (tienePermiso("externo", "egreso_pedidos", "escribir")) { ?>
                                        <li id="egre1"><a href="egreso_pedidos.php"><i class="glyphicon glyphicon-phone"></i>
                                                Exgresos Documentos</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("externo", "consulta_doc", "escribir")) { ?>
                                        <li id="consud"><a href="mod_pedidos.php"><i class="glyphicon glyphicon-search"></i>
                                                Modifica Proforma</a></li>
                                    <?php } ?>
                                                
                                    <?php if (tienePermiso("externo", "consulta_doc", "escribir")) { ?>
                                        <li id="consud"><a href="mod_ofertas.php"><i class="glyphicon glyphicon-search"></i>
                                                Modifica Odertas</a></li>
                                    <?php } ?>      
                                                
                                     <?php if (tienePermiso("externo", "consulta_doc", "escribir")) { ?>
                                        <li id="consud"><a href="mod_contratos.php"><i class="glyphicon glyphicon-search"></i>
                                                Modifica Contratos</a></li>
                                    <?php } ?>      
                                                
                                     <?php if (tienePermiso("externo", "consulta_doc", "escribir")) { ?>
                                        <li id="consud"><a href="mod_Oc.php"><i class="glyphicon glyphicon-search"></i>
                                                Modifica OC</a></li>
                                    <?php } ?>             
                                                
                                    <!--                                    <li id="segur4"><a href="#" >Perfiles</a></li>
                                    <li id="segur5"><a href="#" >Maquinas</a></li>-->
                                    <?php if (tienePermiso("externo", "stock_valor_ubi", "leer")) { ?>
                                        <li id="rep1"><a href="r_stock_monto.php"><i class="glyphicon glyphicon-usd"></i> Stock
                                                Ubicacion/Valor</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("externo", "pedidos", "leer")) { ?>
                                        <li id="rep1"><a href="pedidos.php"><i class="glyphicon glyphicon-usd"></i> Pedidos</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("recepcion")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapseone" id="c1" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-log-in"></i> Gestionar Pedidos
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapseone">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--                                    <li id="recep1"><a href="#" >Crear</a></li>
                                    <li id="recep2"><a href="#" >Modificar</a></li>-->
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep3"><a href="consulta_pedprove.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Pedido Prov.</a></li>
                                    <?php } ?>
                                     <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep3"><a href="consulta_oferta.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Ofertas</a></li>
                                    <?php } ?>  
                                                
                                     <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep3"><a href="consulta_contratos.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Contratos</a></li>
                                    <?php } ?>   
                                                
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep3"><a href="consulta_ocs.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Orden Compra</a></li>
                                    <?php } ?>    
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep3"><a href="consulta_fletes.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Contratacion Fletes</a></li>
                                    <?php } ?>              
                                                
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consulta_recepcion", $_SESSION['user'])) { ?>
                                        <li id="recep4"><a href="receobse.php"><i class="glyphicon glyphicon-ok"></i>
                                                Controles en Recepcion</a></li>
                                    <?php } ?>                
                                    <!--                                    <li id="recep4"><a href="#" >Planificar</a></li>
                                    <li id="recep5"><a href="#" >Consulta Planificación</a></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php
                }
                if (tieneAccesoAModulo("expedicion")) {
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsetwo" id="c2" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-log-out"></i> Expedición
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsetwo">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consultar", $_SESSION['user'])) { ?>
                                        <li id="exped3"><a href="expedicion.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar</a></li>
                                    <?php } ?>
                                     <?php if ($roles_permisos->tiene_permiso_lectura("aprobacion", $_SESSION['user'])) { ?>
                                        <li id="apro1"><a href="aprobacion.php"><i class="glyphicon glyphicon-search"></i>
                                                Aprobaciones</a></li>
                                    <?php } ?>           
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consultar_cajas", $_SESSION['user'])) { ?>
                                        <li id="exped4"><a href="expedicion_cajas.php"><i class="glyphicon glyphicon-search"></i>
                                                Expedicion Maquinista</a></li>
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("empaque")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsem" id="e2" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-log-out"></i> Empaque
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsem">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consulta_empaque", $_SESSION['user'])) { ?>
                                        <li id="empa3"><a href="empaque.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("con_ped_prep", $_SESSION['user'])) { ?>
                                        <li id="empa3"><a href="pedpreparados.php"><i class="glyphicon glyphicon-search"></i>
                                                Pedidos Preparados</a></li>
                                    <?php } ?>

                                    <!--                                    <li id="exped4"><a href="#" >Planificar</a></li>
                                    <li id="exped5"><a href="#" >Consulta Planificación</a></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("maquinista")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapsemaq" id="c5" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-log-out"></i> Maquinista
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapsemaq">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("consultar", $_SESSION['user'])) { ?>
                                        <li id="maqui5"><a href="maquinista.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar</a></li>
                                    <?php } ?>
                                    <!--                                    <li id="exped1"><a href="#" >Crear</a></li>
                                    <li id="exped2"><a href="#" >Modificar</a></li>-->
                                    <!--<li id="maqui5"><a href="maquinista.php"><i class="glyphicon glyphicon-search"></i>
                                            Consultar</a></li>
                                    <!--                                    <li id="exped4"><a href="#" >Planificar</a></li>
                                    <li id="exped5"><a href="#" >Consulta Planificación</a></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("seguridad")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse12" id="c6" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-lock"></i> Seguridad
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse12">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("usuarios", $_SESSION['user'])) { ?>
                                        <li id="segur3"><a href="s_usuarios.php"><i class="glyphicon glyphicon-user"></i>
                                                Usuarios</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("terminales", $_SESSION['user'])) { ?>
                                        <li id="segur6"><a href="s_terminales.php"><i class="glyphicon glyphicon-phone"></i>
                                                Terminales</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("des_termi", $_SESSION['user'])) { ?>
                                        <li id="segur7"><a href="desater.php"><i class="glyphicon glyphicon-remove-sign"></i>
                                                Des. Terminales</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("seguridad", "secciones", "leer")) { ?>
                                        <li id="segur10"><a href="program.php"><i class="glyphicon glyphicon-flash"></i>
                                                Secciones</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("seguridad", "roles", "leer")) { ?>
                                        <li id="segur11"><a href="roles.php"><i class="glyphicon glyphicon-tower"></i>
                                                Roles</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("seguridad", "roles_sec", "leer")) { ?>
                                        <li id="segur12"><a href="asigna_roles.php"><i class="glyphicon glyphicon-th-list"></i>
                                                Roles/Secciones</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("baja_stock", $_SESSION['user'])) { ?>
                                        <li id="segur13"><a href="batch_stocknega.php"><i class="glyphicon glyphicon-th-list"></i>
                                                Ajuste Sock Negativo</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("suba_stock", $_SESSION['user'])) { ?>
                                        <li id="segur14"><a href="batch_stock.php"><i class="glyphicon glyphicon-th-list"></i>
                                                Ajuste Sock Positivo</a></li>
                                    <?php } ?>

                                    <!--                                    <li id="segur1"><a href="#" >Programas</a></li>
                                    <li id="segur2"><a href="#" >Roles</a></li>-->

                                    <!--                                    <li id="segur4"><a href="#" >Perfiles</a></li>
                                    <li id="segur5"><a href="#" >Maquinas</a></li>-->

                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("maestros")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse2" id="c4" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-book"></i> Maestros
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse2">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("articulos", $_SESSION['user'])) { ?>
                                        <li id="maest1"><a href="consulta_prod.php"><i class="glyphicon glyphicon-picture"></i>
                                                Artículos</a></li>
                                    <?php }
                                    
                                    if ($roles_permisos->tiene_permiso_lectura("ean", $_SESSION['user'])) { ?>
                                        <li id="creae"><a href="crea_ean.php"><i class="glyphicon glyphicon-barcode"></i>
                                                Ean </a></li>
                                    <?php }
                                    
                                     if ($roles_permisos->tiene_permiso_lectura("proveedor", $_SESSION['user'])) { ?>
                                        <li id="maest55"><a href="crea_proveedor.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Proveedor</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("ubi_ref", $_SESSION['user'])) { ?>
                                        <li id="maest5"><a href="p_ref_ubic.php"><i class="glyphicon glyphicon-map-marker"></i>
                                                Ubi. Ref. por Artículos</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("ean", $_SESSION['user'])) { ?>
                                        <li id="creaubi"><a href="capaci_ubi.php"><i class="glyphicon glyphicon-hdd"></i>
                                                Capacidad Ubicacion </a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("min_max_rep", $_SESSION['user'])) { ?>
                                        <li id="maest3"><a href="p_min_max_rep.php"><i class="glyphicon glyphicon-stats"></i>
                                                Mín/Máx Reposición</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("min_inve", $_SESSION['user'])) { ?>
                                        <li id="maest4"><a href="p_min_pick.php"><i class="glyphicon glyphicon-stats"></i> Min.
                                                P/Inv.Picking</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("presentacion", $_SESSION['user'])) { ?>
                                        <li id="maest6"><a href="p_presen.php"><i class="glyphicon glyphicon-picture"></i>
                                                Presentación por Artículos</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("kits", $_SESSION['user'])) { ?>
                                        <li id="maest9"><a href="m_kit.php"><i class="glyphicon glyphicon-gift"></i> Kits
                                            </a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("lotes", $_SESSION['user'])) { ?>
                                        <li id="maest7"><a href="m_lote.php"><i class="glyphicon glyphicon-tags"></i> Lote de
                                                Articulos</a></li>
                                    <?php }

                                    if ($roles_permisos->tiene_permiso_lectura("propietarios", $_SESSION['user'])) { ?>
                                        <li id="maest8"><a href="m_propietario.php"><i class="glyphicon glyphicon-user"></i>
                                                Propietario
                                            </a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("configuraciones")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse5" id="c7" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-cog"></i> Configuraciones
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse5">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("centros", $_SESSION['user'])) { ?>
                                        <li id="param1"><a href="p_centro.php"><i class="glyphicon glyphicon-home"></i>
                                                Centro</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("almacen", $_SESSION['user'])) { ?>
                                        <li id="param2"><a href="p_almacen.php"><i class="glyphicon glyphicon-shopping-cart"></i> Almacen</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("bultos", $_SESSION['user'])) { ?>
                                        <li id="param3"><a href="crea_bultos.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Bultos</a></li>
                                    <?php }
                                     if ($roles_permisos->tiene_permiso_lectura("pouertos", $_SESSION['user'])) { ?>
                                        <li id="para41"><a href="crea_puertos.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Puertos</a></li>
                                    <?php }
                                     if ($roles_permisos->tiene_permiso_lectura("fletero", $_SESSION['user'])) { ?>
                                        <li id="para42"><a href="crea_fletero.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Fleteros</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("seguros", $_SESSION['user'])) { ?>
                                        <li id="para43"><a href="crea_aseguradora.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Aseguradora</a></li>
                                    <?php }
                                     if ($roles_permisos->tiene_permiso_lectura("estibador", $_SESSION['user'])) { ?>
                                        <li id="para44"><a href="crea_estibador.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Estibador</a></li>
                                    <?php }
                                     if ($roles_permisos->tiene_permiso_lectura("despachante", $_SESSION['user'])) { ?>
                                        <li id="para45"><a href="crea_despachante.php"><i class="glyphicon glyphicon-check"></i>
                                                Crear Despachante</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_escritura("forwarder", $_SESSION['user'])) { ?>
                                        <li id="param8"><a href="crea_forwarder.php"><i class="glyphicon glyphicon-edit"></i>
                                                Crear Forwarder</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("ubicaciones_con", $_SESSION['user'])) { ?>
                                        <li id="param4"><a href="p_ubicacion_cns.php"><i class="glyphicon glyphicon-search"></i>
                                                Consultar Ubicaciones</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_escritura("rutas", $_SESSION['user'])) { ?>
                                        <li id="param5"><a href="p_rutas.php"><i class="glyphicon glyphicon-road"></i> Rutas</a>
                                        <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("rutas_con", $_SESSION['user'])) { ?>
                                        <li id="param6"><a href="p_rutas_cns.php"><i class="glyphicon glyphicon-search"></i>

                                                Consulta Rutas</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("parametros", $_SESSION['user'])) { ?>
                                        <li id="param7"><a href="p_config.php"><i class="glyphicon glyphicon-compressed"></i>
                                                Parámetros</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("tareas_programadas", $_SESSION['user'])) { ?>
                                        <li id="param9"><a href="p_jobs.php"><i class="glyphicon glyphicon-time"></i> Tareas
                                                Programadas</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("inventario")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse6" id="c8" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-wrench"></i> Inventario
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse6">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <!--<li id="inven1"><a href="#" >Picking</a></li>-->
                                    <?php
                                    if ($roles_permisos->tiene_permiso_lectura("consu_inven", $_SESSION['user'])) { ?>
                                        <li id="inven5"><a href="inventarios.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Consulta Inventarios</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("consu_inven", $_SESSION['user'])) { ?>
                                        <li id="inven2"><a href="i_inventario.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Consulta Inventario Pick.</a></li>
                                    <?php }

                                    if ($roles_permisos->tiene_permiso_lectura("consu_inven", $_SESSION['user'])) { ?>
                                        <li id="inven6"><a href="ingreso_doc_inve.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Crear Doc.Inve.Material</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("consu_inven", $_SESSION['user'])) { ?>
                                        <li id="inven4"><a href="ingreso_doc_inveubi.php"><i class="glyphicon glyphicon-calendar"></i>
                                                Crear Doc.Inve.Ubicacion</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("min_inven", $_SESSION['user'])) { ?>
                                        <li id="inven3"><a href="i_min_pick.php"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i> Mín. P/Picking</a>
                                        </li>
                                    <?php } ?>
                                    <!--<li id="inven4"><a href="#" >Usuario</a></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("reportes")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse7" id="c9" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-stats"></i> Reportes
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse7">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("articulos", $_SESSION['user'])) { ?>
                                        <li id="repor4"><a href="r_articulo.php"><i class="glyphicon glyphicon-picture"></i>
                                                Artículos</a></li>
                                    <?php } ?>
                                    <?php if (tienePermiso("reportes", "r_asignados", "leer")) { ?>
                                        <li id="repor49"><a href="r_asignados.php"><i class="glyphicon glyphicon-picture"></i>
                                                Asignacion de Pedidos</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("movimiento_articulos", $_SESSION['user'])) { ?>
                                        <li id="repor5"><a href="r_movarticulo.php"><i class="glyphicon glyphicon-retweet"></i>
                                                Mov. de Artículos</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("mov_artdet_arti", $_SESSION['user'])) { ?>
                                        <li id="repor50"><a href="r_movart_det.php"><i class="glyphicon glyphicon-stats"></i> Detalle
                                                Mov. Artículo</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("stock_articulos", $_SESSION['user'])) { ?>
                                        <li id="repor1"><a href="r_stock.php"><i class="glyphicon glyphicon-stats"></i> Stock
                                                por Artículo</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("stock_articulos_ubicacion", $_SESSION['user'])) { ?>
                                        <li id="repor2"><a href="r_stock_ubi.php"><i class="glyphicon glyphicon-indent-right"></i> Stock por Ubicación</a></li>
                                    <?php }
                                   
                                    if ($roles_permisos->tiene_permiso_lectura("stock_articulos_fecha", $_SESSION['user'])) { ?>
                                        <li id="repor66"><a href="r_stock_fecha.php"><i class="glyphicon glyphicon-indent-right"></i> Stock por Fecha</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("stock_articulos_lote", $_SESSION['user'])) { ?>
                                        <li id="r_stock_lote"><a href="r_stock_lote.php"><i class="glyphicon glyphicon-indent-right"></i> Stock por Lote</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("stock_articulos_clientes", $_SESSION['user'])) { ?>
                                        <li id="r_stockc"><a href="r_stockc.php"><i class="glyphicon glyphicon-user"></i> Stock
                                                Valor/Clientes</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("min_max_reposicion", $_SESSION['user'])) { ?>
                                        <li id="repor3"><a href="r_min_max_repo.php"><i class="glyphicon glyphicon-th"></i>
                                                Mín/Máx Reposición</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("ubicaciones_referencia", $_SESSION['user'])) { ?>
                                        <li id="repor6"><a href="r_ref_ubic_upd.php"><i class="glyphicon glyphicon-pushpin"></i>
                                                Ubicaciones de referencia</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("presentacion_articulos", $_SESSION['user'])) { ?>
                                        <li id="repor7"><a href="r_presen_upd.php"><i class="glyphicon glyphicon-gift"></i>
                                                Presentaciones de Art.</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("costo_almacenamiento", $_SESSION['user'])) { ?>
                                        <li id="costo_almacenamiento"><a href="costo_almacenamiento.php"><i class="glyphicon glyphicon-gift"></i>
                                                Costo de Almacenamiento.</a></li>
                                    <?php }

                                    if ($roles_permisos->tiene_permiso_lectura("costo_almacenamiento", $_SESSION['user'])) { ?>
                                        <li id="r_leadtime"><a href="r_leadtime.php"><i class="glyphicon glyphicon-gift"></i>
                                                Tiempos.</a></li>
                                    <?php }
                                    if ($roles_permisos->tiene_permiso_lectura("productividad", $_SESSION['user'])) { ?>
                                        <li id="repor8"><a href="r_productividad.php"><i class="glyphicon glyphicon-signal"></i>
                                                Productividad</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("ajustes")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse8" id="c10" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-transfer"></i> Ajustes
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse8">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if ($roles_permisos->tiene_permiso_lectura("ajustes_stock", $_SESSION['user'])) { ?>
                                        <li id="aj1"><a href="a_ajustestock.php"><i class="glyphicon glyphicon-sort"></i> Ajuste
                                                de Stock / Ubic.</a></li>
                                    <?php } ?>
                                    <?php if ($roles_permisos->tiene_permiso_lectura("ajustes_stock", $_SESSION['user'])) { ?>
                                        <li id="aj1"><a href="seg_logistica.php"><i class="glyphicon glyphicon-sort"></i> 
                                                de Stock / Ubic.</a></li>
                                    <?php } ?>            

                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }
                if (tieneAccesoAModulo("consultas")) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#collapse9" id="c11" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                <i class="glyphicon glyphicon-search"></i> Consultas
                            </a>
                        </div>
                        <div class="accordion-body collapse" id="collapse9">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">

                                    <?php if ($roles_permisos->tiene_permiso_lectura("recursos_trabajando", $_SESSION['user'])) { ?>
                                        <li id="cons1"><a href="cons_Termi_acti.php"><i class="glyphicon glyphicon-phone"></i>
                                                Recursos Trabajando</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="push"></div>
        </div>
        <!--                <div class="sidebar_info">
                    <ul class="list-unstyled">
                        <li>
                            <span id='lblPend' class="act act-danger"></span>
                            <strong>Pendientes</strong>
                        </li>
                        <li>
                            <span id='lblProc' class="act act-success"></span>
                            <strong>Procesados</strong>
                        </li>
                        <li>
                            <span id='lblTot' class="act act-primary"></span>
                            <strong>Total Pedidos</strong>
                        </li>
                    </ul>
                </div>-->

    </div>

</div>
<script>

</script>