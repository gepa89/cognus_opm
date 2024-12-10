<?php
namespace GO;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Crontab {
    
    // In this class, array instead of string would be the standard input / output format.
    
    // Legacy way to add a job:
    // $output = shell_exec('(crontab -l; echo "'.$job.'") | crontab -');
    
    static private function stringToArray($jobs = '') {
        $array = explode("\n", trim($jobs)); // trim() gets rid of the last \r\n
        foreach ($array as $key => $item) {
            if ($item == '') {
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    static private function arrayToString($jobs = array()) {
        $string = implode("\n", $jobs);
        return $string;
    }
    
    static public function getJobs($jobname = '') {
        $output = shell_exec('crontab -l');
        return self::stringToArray($output);
    }
    
    static public function getScripts() {
        $output = shell_exec('crontab -l');
        $jobsarray = self::stringToArray($output);
        foreach($jobsarray as $key => $job){
            $jobarray = explode("/",$job);
            $script = end($jobarray);
            $scriptname = explode(".",$script);
            $return[] = $scriptname[0];
        }
        return $return;
    }
    static public function getScriptsStatus($job = '') {
        $return = true;
        if($job[0] == '#'){
            $return = false;
        }
        return $return;
    }
    static public function getScriptsData() {
        $output = shell_exec('crontab -l');
        $jobsarray = self::stringToArray($output);
        $cc = 0;
        foreach($jobsarray as $key => $job){
            $jobarray = explode("/",$job);
            $script = end($jobarray);
            $scriptname = explode(".",$script);
            $return[$cc]['name'] = trim($scriptname[0]);
            $return[$cc]['schedule'] = trim(str_replace('#', '', $jobarray[0]));
            $return[$cc]['bin'] = trim($jobarray[1]);
            $return[$cc]['script'] = trim(end($jobarray));
            $return[$cc]['status'] = self::getScriptsStatus($job);;
            $cc++;
        }
        return $return;
    }
    
    static public function saveJobs($jobs = array()) {
        $output = shell_exec('echo "'.self::arrayToString(($jobs)).'" | crontab -');
        if($output){            	
            return false;
        }else{
            return true;	
        }
//        return $output;	
    }
    
    static public function doesJobExist($job = '') {
        $jobs = self::getJobs();
        if (in_array($job, $jobs)) {
            return true;
        } else {
            return false;
        }
    }
    
    static public function doesJobNameExist($scname = '') {
        $jobs = self::getJobs();
        $flag = 0;
        foreach($jobs as $key => $job){
            if(strpos($job, $scname)){
                $flag = 1;
            }
        }
        if($flag == 1){
            if (in_array($job, $jobs)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        
    }
    static public function getJobString($scname = '') {
        $jobs = self::getJobs();
        $flag = 0;
        foreach($jobs as $key => $job){
            if(strpos($job,$scname)){
                $flag = 1;
                $eljob = $job;
            }
        }
        if($flag == 1){
            if (in_array($eljob, $jobs)) {
                return $eljob;
            }
        }
        
    }
    
    static public function addJob($job = '') {
        if (self::doesJobExist($job)) {
            return false;
        } else {
            $jobs = self::getJobs();
            $jobs[] = $job;
            return self::saveJobs($jobs);
        }
    }
    
    static public function removeJob($job = '') {
        if (self::doesJobExist($job)) {
            $jobs = self::getJobs();
            unset($jobs[array_search($job, $jobs)]);
            return self::saveJobs($jobs);
        } else {
            return false;
        }
    }
    
    static public function updateJob($jobin = '',$jobout = '') {
//        echo $jobout;
        if (self::doesJobExist($jobout)) {
            $jobs = self::getJobs();
            unset($jobs[array_search($jobout, $jobs)]);
            array_push($jobs, trim($jobin));
            return self::saveJobs($jobs);            
        } else {
            return false;
        }
    }
    
}








