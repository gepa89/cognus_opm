<?php

include 'GO/Crontab.php';
use GO\Crontab;
$cronController = new Crontab();

$action = $_POST['action'];
$script = $_POST['script'];
$jobs = $cronController->getJobs();

$sctexists = $cronController->doesJobNameExist($script);
if($sctexists){
    $jobout = $cronController->getJobString($script);
    switch($action){
       case 'pause':            
            $jobin = "#".$jobout;
           break;
       case 'play':
           $jobin = str_replace('#', '', $jobout);
           break;
    }
    
    $result = $cronController->updateJob($jobin,$jobout);
//    var_dump($result);
    if($result){
        $msg = 'Tarea guardada';
        $err = 0;
    }else{
        $msg = 'Error al registrar tarea';
        $err = 1;
    }
}
echo json_encode(array( 'msg' => $msg, 'err' => $err));












