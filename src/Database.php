<?php

//Declaring namespace
namespace LaswitchTech\phpDB;

//Import mysqli class into the global namespace
use \mysqli;
use \Exception;

class Database {

  protected $connection = null;
  protected $debug = false;
  protected $character = 'utf8mb4';
  protected $collate = 'utf8mb4_general_ci';

  public function __construct($host = null, $username = null, $password = null, $database = null, $debug = null) {
    if($host == null && defined('DB_HOST')){ $host = DB_HOST; }
    if($username == null && defined('DB_USERNAME')){ $username = DB_USERNAME; }
    if($password == null && defined('DB_PASSWORD')){ $password = DB_PASSWORD; }
    if($database == null && defined('DB_DATABASE_NAME')){ $database = DB_DATABASE_NAME; }
    if($debug == null && defined('DB_DEBUG')){ $debug = DB_DEBUG; }
    if(is_bool($debug)){ $this->debug = $debug; }
    try {
      error_reporting(E_ALL ^ E_WARNING);
      $this->connection = new mysqli($host, $username, $password, $database);
      if(mysqli_connect_errno()){
        throw new Exception("Could not connect to database.");
      }
      error_reporting(E_ALL);
    } catch (Exception $e) {
      $this->connection = null;
      if($this->Debug){
        throw new Exception($e->getMessage());
      }
    }
  }

  public function isConnected(){
    return ($this->connection != null);
  }

  public function __call($name, $arguments) {
    return [ "error" => "[".$name."] 501 Not Implemented" ];
  }

  private function executeStatement($query = "" , $params = []) {
    if($this->debug){ echo 'Query: ' . json_encode($query, JSON_PRETTY_PRINT) . PHP_EOL . '<br>'; }
    if($this->debug){ echo 'Params: ' . json_encode($params, JSON_PRETTY_PRINT) . PHP_EOL . '<br>'; }
    try {
      $stmt = $this->connection->prepare( $query );
      if($this->debug){ echo 'Prepared Statement: ' . json_encode($stmt, JSON_PRETTY_PRINT) . PHP_EOL . '<br>'; }
      if($stmt === false) {
        throw New Exception("Unable to do prepared statement: " . $query);
      }
      if( $params ) {
        $types = "";
        foreach($params as $param){
          switch(gettype($param)){
            case"boolean":
            case"integer": $types .= "i"; break;
            case"double": $types .= "d"; break;
            case"blob": $types .= "b"; break;
            case"string":
            default: $types .= "s"; break;
          }
        }
        if($this->debug){ echo 'Bind Parameters: ' . json_encode($types, JSON_PRETTY_PRINT) . PHP_EOL . '<br>'; }
        $stmt->bind_param($types, ...$params);
      }
      $stmt->execute();
      if($this->debug){ echo 'Executed Statement: ' . json_encode($stmt, JSON_PRETTY_PRINT) . PHP_EOL . '<br>'; }
      return $stmt;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
  }

  public function query($query, $params = []){
    try {
      foreach($params as $key => $value){
        if(is_string($value)){ $params[$key] = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value)); }
      }
      $stmt = $this->executeStatement( $query, $params );
      $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      return $result;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function insert($query = "" , $params = []) {
    try {
      foreach($params as $key => $value){
        if(is_string($value)){ $params[$key] = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value)); }
      }
      $stmt = $this->executeStatement( $query , $params );
      $last_id = $stmt->insert_id;
      $stmt->close();
      return $last_id;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function select($query = "" , $params = []) {
    try {
      foreach($params as $key => $value){
        if(is_string($value)){ $params[$key] = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value)); }
      }
      $stmt = $this->executeStatement( $query , $params );
      $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      return $result;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function update($query = "" , $params = []) {
    try {
      foreach($params as $key => $value){
        if(is_string($value)){ $params[$key] = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value)); }
      }
      $stmt = $this->executeStatement( $query , $params );
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function delete($query = "" , $params = []) {
    try {
      foreach($params as $key => $value){
        if(is_string($value)){ $params[$key] = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value)); }
      }
      $stmt = $this->executeStatement( $query , $params );
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function create($table, $columns){
    try {
      $query = 'CREATE TABLE `'.$table.'` (';
      foreach($columns as $name => $column){
        if(isset($column['type'])){
          if(substr($query, -1) != '('){ $query .= ', '; }
          $query .= '`'.$name.'` '.strtoupper($column['type']);
          if(isset($column['extra']) && is_array($column['extra'])){
            foreach($column['extra'] as $extra){
              if(in_array(strtoupper($extra),['NULL','NOT NULL','UNIQUE','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']) || str_contains(strtoupper($extra), 'DEFAULT')){
                $query .= ' '.strtoupper($extra);
              }
            }
          }
        }
      }
      $query .= ' ) CHARACTER SET ' . $this->character;
      $stmt = $this->executeStatement( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function alter($table, $columns){
    try {
      foreach($columns as $name => $column){
        if(isset($column['action']) && in_array(strtoupper($column['action']),['MODIFY','ADD','DROP COLUMN'])){
          if(isset($column['type'])){
            $query = 'ALTER TABLE `'.$table.'` DEFAULT CHARACTER SET ' . $this->character . ', '.strtoupper($column['action']).' `'.$name.'` '.strtoupper($column['type']);
            if(isset($column['extra']) && is_array($column['extra'])){
              foreach($column['extra'] as $extra){
                if(in_array(strtoupper($extra),['NULL','NOT NULL','UNIQUE','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']) || str_contains(strtoupper($extra), 'DEFAULT')){
                  $query .= ' '.strtoupper($extra);
                }
              }
            }
            $stmt = $this->executeStatement( $query );
            $stmt->close();
          }
        }
      }
      return true;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function drop($table){
    try {
      $query = 'DROP TABLE `'.$table.'`';
      $stmt = $this->executeStatement( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  public function truncate($table){
    try {
      $query = 'TRUNCATE TABLE `'.$table.'`';
      $stmt = $this->executeStatement( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }
}
