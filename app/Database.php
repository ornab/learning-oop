<?php

namespace RRF;

use PDO;

/**
 *
 */
class Database
{
    private $database_name;
    private $database_user;
    private $database_pass;
    private $database_host;
    private $db;

    function __construct($array)
    {
        if (is_array($array)) {
            if (array_key_exists('database_name', $array)) {
                $this->database_name = $array['database_name'];
            }

            if (array_key_exists('database_user', $array)) {
                $this->database_user = $array['database_user'];
            }

            if (array_key_exists('database_pass', $array)) {
                $this->database_pass = $array['database_pass'];
            }

            if (array_key_exists('database_host', $array)) {
                $this->database_host = $array['database_host'];
            }

            try {
                $dsn = 'mysql:host=' . $this->database_host . ';dbname=' . $this->database_name . ';charset=utf8';
                $this->db = new PDO($dsn, $this->database_user, $this->database_pass);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }

        } else {
            echo 'An array must be provided';
        }
    }

    public function create($table, $data)
    {
        $keys = array_keys($data);
        foreach ($keys as $key) {
            $values[] = ':' . $key;
        }

        $query = 'INSERT INTO ' . $table . ' (' . implode(',', $keys) . ') VALUES (' . implode(',', $values) . ')';
        $stm = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stm->bindValue($key, $value);
        }

        $stm->execute();

        return $this->db->lastInsertId();
    }

    public function read($read, $table, $fetch = '')
    {
        if (is_array($read)) {
            $columns = implode(',', $read);
        } elseif ($read == '*') {
            $columns = '*';
        }

        $query = 'SELECT ' . $columns . ' FROM ' . $table;
        $stm = $this->db->query($query);
        if ($fetch === 'single') {
            return $stm->fetch(PDO::FETCH_ASSOC);
        } else {
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function update($table, $data, $condition)
    {
        $keys = array_keys($data);
        foreach ($keys as $key) {
            $alias[] = $key . '=' . ':' . $key;
        }

        $query = 'UPDATE ' . $table . ' SET ' . implode(',', $alias) . ' WHERE ' . $condition;
        $stm = $this->db->prepare($query);
        foreach ($data as $key => $value) {
            $stm->bindValue($key, $value);
        }
        $stm->execute();

        $query1 = 'SELECT * FROM ' . $table . ' WHERE ' . $condition;
        $stm1 = $this->db->query($query1);
        return $stm1->fetchObject();
    }
}
