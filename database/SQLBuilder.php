<?php
class MySqlQuery
{
    private $table;
    private $select = '*';
    private $where = '';
    private $params = [];
    private $query;
    private $conn;
    private $limit;
    private $offset;
    private $result;
    private $order_by;
    private $is_internal_db = false;
    public function __construct($conn = null)
    {
        if (isset($GLOBALS['mysql_conn'])) {
            $this->conn = $GLOBALS['mysql_conn'];
            return;
        }
        if ($conn == null) {
            $this->conn = MysqlDB::obtenerInstancia();
            $this->is_internal_db = true;
            return;
        } else {
            $this->conn = $conn;
        }
    }

    public function limit($count)
    {
        $this->limit = "LIMIT $count";
        return $this;
    }

    public function offset($count)
    {
        $this->offset = "OFFSET $count";
        return $this;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }
    public function rawQuery($query, $params = [])
    {
        $this->reset();
        $this->query = $query;
        $this->params = $params;
        $this->execute();
        return $this;
    }
    public function get_result()
    {
        return $this->result;
    }

    public function select($columns)
    {
        $this->query = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where($parametros)
    {
        $where_clause = implode(" AND ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($parametros)));
        $this->where = 'WHERE ' . $where_clause;
        $this->params = array_merge($this->params, array_values($parametros));
        return $this;
    }

    public function whereWithParams($filtros, $parametros)
    {
        $this->where = 'WHERE TRUE ' . $filtros;
        $this->params = $parametros;
        return $this;
    }

    public function executeSelect()
    {
        $this->query = "SELECT {$this->query} FROM {$this->table} {$this->where} {$this->order_by} {$this->limit} {$this->offset}";
        $this->execute();
        return $this;
    }

    public function executeUpdate()
    {
        $this->query = "{$this->query} {$this->where}";
        $res = $this->execute();
        return $res;
    }

    public function buildInsert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->params = array_values($data);
        return $this;
    }

    public function getSQL()
    {
        return $this->query;
    }

    public function buildUpdate($data)
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $this->query = "UPDATE {$this->table} SET $set ";
        $this->params = array_values($data);
        return $this;
    }

    public function buildDelete()
    {
        $this->query = "DELETE FROM {$this->table} {$this->where}";
        return $this->query;
    }

    public function getParams()
    {
        return $this->params;
    }
    public function execute()
    {
        if ($this->query === null) {
            print_r("No query to execute");
            return false; // No query to execute
        }
        $stmt = $this->conn->prepare($this->query);

        if ($stmt === false) {
            throw new Exception($this->conn->error, 1);
        }

        if ($this->params) {
            // Determine the types for bind_param based on the data types of the params
            $types = '';
            $bindParams = array_merge([], $this->params);
            foreach ($this->params as $param) {
                if (is_int($param)) {
                    $types .= 'i'; // Integer
                } elseif (is_float($param)) {
                    $types .= 'd'; // Double (float)
                } elseif (is_bool($param)) {
                    $types .= 'i'; // Boolean (treated as integer: 0 or 1)
                } elseif (is_null($param)) {
                    $types .= 's'; // Null (treated as string)
                    $param = ''; // Bind null as an empty string
                } else {
                    $types .= 's'; // String (default)
                }
            }
            $stmt->bind_param($types, ...$bindParams);
        }

        $stmt->execute();

        $this->result = $stmt;
        #$stmt->close();
        return $stmt; // Query executed successfully
    }
    public function count()
    {
        $this->query = "SELECT COUNT(*) AS cantidad FROM {$this->table} {$this->where}";
        $this->execute();
        return $this->getOne()->cantidad;
    }

    public function getAll(): array
    {

        if ($this->result == null) {
            throw new Exception("ocurrio un error", 1);
        }
        if ($this->result->errno != 0) {
            throw new Exception($this->result->error, 1);
        }

        $resultado = $this->result->get_result()->fetch_all(MYSQLI_ASSOC);
        return $resultado;
    }

    public function getSTMTObject(): object
    {

        if ($this->result == null) {
            throw new Exception("ocurrio un error", 1);
        }
        if ($this->result->errno != 0) {
            throw new Exception($this->result->error, 1);
        }

        return $this->result->get_result();
    }

    public function getOne()
    {
        if ($this->result == null) {
            return false;
        }
        $resultado = $this->result->get_result()->fetch_all(MYSQLI_ASSOC);
        return $resultado ? (object)$resultado[0] : null;
    }

    public function getCount()
    {
        if ($this->result == null) {
            return false;
        }
        return $this->result->num_rows;
    }

    public function exists()
    {
        if ($this->result == null) {
            return false;
        }
        return $this->result->num_rows > 0;
    }

    public function orderBy($parametros)
    {
        $order_clause = implode(", ", array_map(function ($key, $value) {
            return "$key $value";
        }, array_keys($parametros), array_values($parametros)));
        $this->order_by = " ORDER BY $order_clause";
        return $this;
    }

    public function reset()
    {
        $this->table = null;
        $this->select = '*';
        $this->where = '';
        $this->params = [];
        $this->query = null;
        $this->limit = null;
        $this->offset = null;
        $this->result = null;
        $this->order_by = null;
    }

    public function closeConnection()
    {
        if ($this->is_internal_db) {
            $this->conn->close();
        }
    }
}
