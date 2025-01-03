<?php

namespace Framework;

use Framework\Database;

class Controller
{

    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Create a record in the database
     *
     * @param string $table
     * @param array $params
     * @return \PDOStatement|false
     */
    protected function createRecord($table = '', $params = [])
    {
        // Prepare insert query
        $insertFields = implode(', ', array_keys($params));
        $insertValues = ':' . implode(', :', array_keys($params));
        $sql = "INSERT INTO {$table} ($insertFields) VALUES ($insertValues)";

        // Replace any empty strings with null
        $sanitizedData = array_map(fn($value) => $value === '' ? null : $value, $params);

        // Execute insert query
        $this->db->query($sql, $sanitizedData);

        // Return the ID of the inserted record
        return $this->db->conn->lastInsertId();
    }

    /**
     * Get a record from the database by its ID
     *
     * @param string $table
     * @param string $id
     * @return mixed
     */
    protected function fetchByID($table = '', $id = '')
    {
        $sql = "SELECT * FROM {$table} WHERE id = :id";
        $queryParams = ['id' => $id];
        return $this->db->query($sql, $queryParams)->fetch();
    }
}
