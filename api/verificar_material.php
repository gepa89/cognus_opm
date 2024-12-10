<?php
require_once('../database/database.php');
require_once('../utils/respuesta.php');
$articulo = 'erdfd';
$orm = new MySqlQuery();
$res = $orm->table('arti')
    ->select(['artrefer'])
    ->where('artrefer = ?', ['LDARS'])
    ->executeSelect();
if ($res == false) {
    retorna_resultado(array("mensaje" => "ocurrio un error"));
    exit;
}
retorna_resultado(array("existe" => true));
