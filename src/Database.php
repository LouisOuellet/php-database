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

  private function executeStatement($query = "" , $params = []) {
    try {
      $stmt = $this->connection->prepare( $query );
      if($stmt === false) {
        throw New Exception("Unable to do prepared statement: " . $query);
      }
      if( $params ) {
        $stmt->bind_param($params[0], $params[1]);
      }
      $stmt->execute();
      return $stmt;
    } catch(Exception $e) {
      throw New Exception( $e->getMessage() );
    }
  }
}
