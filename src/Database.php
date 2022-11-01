<?php

//Declaring namespace
namespace LaswitchTech\phpDB;

//Import mysqli class into the global namespace
use \mysqli;

class Database {

  protected $connection = null;

  public function __construct($host = null, $username = null, $password = null, $database = null) {
    if($host == null && defined('DB_HOST')){ $host = DB_HOST; }
    if($username == null && defined('DB_USERNAME')){ $username = DB_USERNAME; }
    if($password == null && defined('DB_PASSWORD')){ $password = DB_PASSWORD; }
    if($database == null && defined('DB_DATABASE_NAME')){ $database = DB_DATABASE_NAME; }
    try {
      $this->connection = new mysqli($host, $username, $password, $database);
      if ( mysqli_connect_errno()) {
        throw new Exception("Could not connect to database.");
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function insert($query = "" , $params = []) {
    try {
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
      $stmt = $this->executeStatement( $query , $params );
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  private function executeStatement($query = "" , $params = []) {
    try {
      $stmt = $this->connection->prepare( $query );
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
        $stmt->bind_param($types, ...$params);
      }
      $stmt->execute();
      return $stmt;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
  }
}
