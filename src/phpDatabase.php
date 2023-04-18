<?php

// Declaring namespace
namespace LaswitchTech\phpDatabase;

// Import phpConfigurator class into the global namespace
use LaswitchTech\phpConfigurator\phpConfigurator;

// Import phpLogger class into the global namespace
use LaswitchTech\phpLogger\phpLogger;

// Import phpNet class into the global namespace
use LaswitchTech\phpNet\phpNet;

abstract class phpDatabase {
    protected $config;
    protected $connection;
    protected $queryBuilder;

    public function __construct($config) {
        $this->config = $config;
        $this->connect();
    }

    abstract protected function connect();

    // Add abstract methods for query building
    abstract public function select($table, $columns = '*');
    abstract public function insert($table, $data);
    abstract public function update($table, $data);
    abstract public function delete($table);

    abstract public function where($condition);
    abstract public function limit($limit);

    // Execute the built query
    abstract public function execute();

    abstract public function escape($value);
}

// SQLite.php
class SQLite extends Database {
    // Implement SQLite specific methods
}

// SQL.php
class SQL extends Database {
    // Implement SQL specific methods (MySQL or MariaDB, for example)
}

// TSQL.php
class TSQL extends Database {
    // Implement T-SQL specific methods (Microsoft SQL Server)
}