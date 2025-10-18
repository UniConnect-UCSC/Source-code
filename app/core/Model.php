<?php

trait Model
{
    use Database;

    public $errors = [];

    private $limit = 10;
    private $offset = 0;

    public function __construct()
    {
        $this->connect();
    }

    public function findAll($limit = null, $offset = null)
    {
        $limit = $limit ? $limit : $this->limit;
        $offset = $offset ? $offset : $this->offset;
        $sql = "SELECT * from $this->table limit $limit offset $offset";
        return $this->query($sql);
    }

    /**
     * @param array $conditions Array of conditions in format [field ,operator, value]]
     * @param int $limit [DEFAULT: queries EVERYTHING]
     * @param int $offset [DEFAULT: 0]
     * @param array $orderBy Array of fields to order by with direction [field => 'ASC|DESC']
     * Note: In this method only the condition inputs are prepared for execution
     */
    public function where($conditions, $limit = null, $offset = null, $orderBy = [])
    {
        try {
            $conditions = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    // Example: ['user_id' => [5, '!=']]
                    $conditions[] = "$key {$value[1]} :$key";
                    $params[$key] = $value[0];
                } else {
                    $conditions[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }
            $sql = "SELECT * FROM {$this->table} WHERE " . implode(" AND ", $conditions);
            return $this->query($sql, $params);
        } catch (PDOException $e) {
            die("WHERE query failed: " . $e->getMessage());
        }
    }

    public function first($data)
    {
        try {
            $keys = array_keys($data);
            $conditions = implode(" AND ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
            return $this->get_row($sql, $data);
        } catch (PDOException $e) {
            die("FIRST query failed: " . $e->getMessage());
        }
    }

    public function insert($data)
    {
        try {
            $keys = array_keys($data);
            $columns = implode(", ", $keys);
            $placeholders = implode(", ", array_map(fn($key) => ":$key", $keys));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("INSERT failed: " . $e->getMessage());
        }
    }

    public function insertAndFetch($data)
    {
        try {
            $keys = array_keys($data);
            $columns = implode(", ", $keys);
            $placeholders = implode(", ", array_map(fn($key) => ":$key", $keys));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders) RETURNING *";
            return $this->get_row($sql, $data);
        } catch (PDOException $e) {
            die("INSERT+FETCH failed: " . $e->getMessage());
        }
    }

    public function update($id, $data, $id_column = 'id')
    {
        try {
            $keys = array_keys($data);
            $set = implode(", ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "UPDATE {$this->table} SET $set WHERE $id_column = :id";
            $data['id'] = $id;
            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("UPDATE failed: " . $e->getMessage());
        }
    }

    public function delete($id, $id_column = 'id')
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE $id_column = :id";
            return $this->query($sql, ['id' => $id]);
        } catch (PDOException $e) {
            die("DELETE failed: " . $e->getMessage());
        }
    }
}
