<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'GO/Crontab.php';
use GO\Crontab;
$cronController = new Crontab();


$jobs = $cronController->getJobs();

echo "<pre>";var_dump($jobs);echo "</pre>";


