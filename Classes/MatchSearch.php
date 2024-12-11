<?php
namespace MS;
class MatchSearch {
    private $mcdb;
    
    public function __construct($db)
    {
        $this->mcdb = $db;
    }
    
    public function searchItem($string, $column, $table){
        $qrLimit = '';
        $string = str_replace('*', '%', $string);
        $colArr = explode(',',$column);
        $wrConds = "";
        foreach($colArr as $k => $val){
            if($wrConds == ''){
                $wrConds .= " $val like '{$string}'";
            }else{
                $wrConds .= " or $val like '{$string}' ";
            }
        }
        $sql = "select distinct {$column} from {$table} where 1=1 and {$wrConds}";
//        echo $sql;
        $ldb = $this->mcdb;
//        var_dump($ldb);
        $response = array();
        $rs = $ldb->query($sql);
        $cc = 0;
        while($ax = $rs->fetch_assoc()){
            $response[$cc] = array_values($ax);
            $cc++;
        }  
        return $response;
    } 
    public function checkQuery($string, $column, $table){
        $string = str_replace('*', '%', $string);
        $sql = "select {$column} from {$table} where {$column} like '{$string}'";
        return $sql;
    }
    
}









