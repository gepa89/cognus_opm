<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include '../Classes/MatchSearch.php';
use MS\MatchSearch;
require ('../conect.php');


$db = new mysqli($SERVER,$USER,$PASS,$DB);
$string = $_POST['strin'];
$column = $_POST['column'];
$table = $_POST['table'];
        


$matchSearch = new MatchSearch($db);
$resSearch = $matchSearch->searchItem($string, $column, $table);
echo json_encode($resSearch);
        