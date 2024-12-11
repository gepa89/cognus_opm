<?php
require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../database/SQLBuilder.php");

class ControlladorTrabajoPiso
{
    private $db;
    public function __construct($db = null)
    {
        $this->db = $db instanceof mysqli ? $db : MysqlDB::obtenerInstancia();
    }
    public function close()
    {
        $this->db->close();
    }
}
