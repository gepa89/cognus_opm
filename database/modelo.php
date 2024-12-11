<?php
class Modelo
{
    const INT = "int";
    const STRING = "string";

    protected $table;
    protected $select = '*';
    protected $where = '';
    protected $params = [];

    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where($conditions, $params = [])
    {
        $this->where = 'WHERE ' . $conditions;
        $this->params = $params;
        return $this;
    }

    public function getSQL()
    {
        return $this->createQuery();
    }

    protected function createQuery()
    {
        if ($this->table === null) {
            throw new Exception("Table name not specified.");
        }

        $sql = "SELECT {$this->select} FROM {$this->table} {$this->where}";
        return $sql;
    }
}

class User extends Modelo
{
    protected $table = 'user';

    public $id = Modelo::INT;
    public $username = Modelo::STRING;
    public $name = Modelo::STRING;
    public $fields = [
        $this->id => Modelo::INT
    ];
    public function test()
    {
        return $this->getSQL();
    }
}
$user = new User();
print_r($user->select()->where("username = ?", ["asd"])->getSQL());
