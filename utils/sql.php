<?php
require_once(__DIR__ . "/../logger/logger.php");
class EjecutorSQL
{
    private $db;
    private $sql;
    public function __construct($db, $sql)
    {
        $this->db = $db;
        $this->sql = $sql;
    }
    public function ejecutar()
    {
        guardar_sql_log($this->sql);
        $query = $this->db->query($this->sql);
        if ($query === false) {
            guardar_error_log("ejecucion sql", $this->sql);
            guardar_error_log("ejecucion sql", $this->db->error);
        }
        return $query;
    }
}

?>