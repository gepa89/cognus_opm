<?php $shell = true;
// phpinfo();
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Asuncion');
// var_dump($_SER)
require ('/home/u920596356/domains/cognus.com.py/public_html/rpm/conect.php');
$db = new mysqli($SERVER,$USER,$PASS,$DB);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ADAIA

$scriptname = basename(__FILE__, '.php');
    $sq = "insert into scheduled_jobs_ck set script = '{$scriptname}', last = now()";
    $rs = $db->query($sq);
echo $sq;
