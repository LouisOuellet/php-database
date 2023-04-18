<?php

//Declaring namespace
namespace LaswitchTech\phpDatabase;

class QueryBuilder {
    protected $table;
    protected $columns;
    protected $conditions;
    protected $limit;
    protected $action;

    public function __construct() {
        $this->columns = [];
        $this->conditions = [];
        $this->limit = null;
        $this->action = null;
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function setConditions($conditions) {
        $this->conditions = $conditions;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function buildQuery() {
        // Build the query string based on the properties
    }
}