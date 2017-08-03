<?php

namespace Adi\Classes\Core;

use PDO;

class Database
{
    protected static $_instance = null;

    protected $_pdo = null;

    private function _connect(array $config)
    {
        try {
            return new PDO($config['dsn'] . 'dbname=' . $config['dbname'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        }
        catch (PDOException $e)
        {
            die('Database connection failed : ' . $e->getMessage());
        }
    }

    public static function getInstance(array $config)
    {
        if (!static::$_instance)
        {
            static::$_instance = new static($config);
        }
        return static::$_instance;
    }

    private function getDuplicateStmt(array $duplicate)
    {
        return "ON DUPLICATE KEY UPDATE " . implode(', ', $this->_prepareDuplicateKeys($duplicate));
    }

    public function insert(string $table, array $parameters, array $duplicate = [], bool $ignore = false)
    {
        $ignore = ($ignore) ? "IGNORE" : "";

        $duplicate_stmt = (!empty($duplicate)) ? $this->getDuplicateStmt($duplicate) : '';

        $query = "INSERT {$ignore} INTO %s (%s) VALUES (%s) {$duplicate_stmt}";
        $query = sprintf($query,
            $table,
            implode(', ', array_keys($parameters)),
            implode(', ', $this->_prepareParams($parameters))
        );
        $this->_query($query, $parameters);
        return $this->_pdo->lastInsertId();
    }

    public function selectAll(string $table)
    {
        return $this->_query("SELECT * FROM {$table}");
    }

    protected function _query(string $query, array $parameters = [])
    {
        if ($this->_pdo && !empty($query))
        {
            try {
                $stmt = $this->_pdo->prepare($query);

                if (!empty($parameters))
                {
                    $stmt->execute($parameters);
                }
                else
                {
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_CLASS);
                }
            }
            catch (PDOException $e)
            {
                die('Failed to execute query : ' . $e->getMessage());
            }
        }
    }

    private function __construct(array $config)
    {
        if (!$this->_pdo)
        {
            $this->_pdo = $this->_connect($config);
        }
        return $this->_pdo;
    }

    /**
     * To format duplicate keys as "$key=VALUES($key)"
     * @param  array   $duplicate keys
     * @return array
     */
    private function _prepareDuplicateKeys(array $duplicate)
    {
        return array_map(function (string $param)
        {
            return "{$param}=VALUES({$param})";
        }, $duplicate);
    }

    /**
     * To attach colon ":" before every parameter array key.
     * @param  array   $parameters
     * @return array
     */
    private function _prepareParams(array $parameters)
    {
        return array_map(function (string $param)
        {
            return ":{$param}";
        }, array_keys($parameters));
    }
}
