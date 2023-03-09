<?php

//Declaring namespace
namespace LaswitchTech\phpDB;

//Import phpLogger class into the global namespace
use LaswitchTech\phpLogger\phpLogger;

//Import mysqli class into the global namespace
use \mysqli;
use \Exception;

class Database {

  private $connection = null;
  private $debug = false;
  private $character = 'utf8mb4';
  private $collate = 'utf8mb4_general_ci';

  private $Logger;

  /**
   * Create a new Database instance.
   *
   * @param  string|null  $host
   * @param  string|null  $username
   * @param  string|null  $password
   * @param  string|null  $database
   * @param  boolean|null  $debug
   * @return void
   * @throws Exception
   */
  public function __construct($host = null, $username = null, $password = null, $database = null, $debug = null) {

    // Initiate phpLogger
    $this->Logger = new phpLogger(['database' => 'log/database.log']);

    // Set default parameter values if not specified
    if($host == null && defined('DB_HOST')){ $host = DB_HOST; }
    if($username == null && defined('DB_USERNAME')){ $username = DB_USERNAME; }
    if($password == null && defined('DB_PASSWORD')){ $password = DB_PASSWORD; }
    if($database == null && defined('DB_DATABASE_NAME')){ $database = DB_DATABASE_NAME; }
    if($debug == null && defined('DB_DEBUG')){ $debug = DB_DEBUG; }
    if(is_bool($debug)){ $this->debug = $debug; }

    // Attempt a connection to the database
    try {

      // Turn off warnings
      error_reporting(E_ALL ^ E_WARNING);

      // Create a new mysqli connection
      $this->Logger->info("Establishing connection to database.");
      if($this->debug){
        $this->Logger->debug("Host: " . $host);
        $this->Logger->debug("Username: " . $username);
        $this->Logger->debug("Password: " . $password);
        $this->Logger->debug("Database: " . $database);
      }
      $this->connection = new mysqli($host, $username, $password, $database);

      // Throw an exception if connection failed
      if(mysqli_connect_errno()){
        $this->Logger->error("Could not connect to database.");
        throw new Exception("Could not connect to database.");
      } else {
        $this->Logger->success("Database connected.");
      }

      // Turn on warnings
      error_reporting(E_ALL);
    } catch (Exception $e) {
      $this->connection = null;
      if($this->debug){

        // Log any errors and throw an exception
        $this->Logger->error($e->getMessage());
        throw new Exception($e->getMessage());
      }
    }
  }

  /**
   * Error handling.
   *
   * @param  string  $method
   * @param  string|array|null  $arguments
   * @return Exception
   * @throws Exception
   */
  public function __call($method, $arguments) {

    // Log any errors and throw an exception
    $this->Logger->error("Method $method does not exist.");
    throw new Exception("Method $method does not exist.");
  }

  /**
   * Test if a connection has been established.
   *
   * @return boolean
   */
  public function isConnected(){
    return ($this->connection != null);
  }

  /**
   * Begin transactions on database.
   *
   * @throws Exception
   */
  public function begin() {
    if (!$this->isConnected()) {

      // Log any errors and throw an exception
      $this->Logger->error("Not connected to database.");
      throw new Exception("Not connected to database.");
    }
    $this->connection->begin_transaction();
  }

  /**
   * Commit transactions to database.
   *
   * @throws Exception
   */
  public function commit() {
    if (!$this->isConnected()) {

      // Log any errors and throw an exception
      $this->Logger->error("Not connected to database.");
      throw new Exception("Not connected to database.");
    }
    $this->connection->commit();
  }

  /**
   * Rollback transactions to database.
   *
   * @throws Exception
   */
  public function rollback() {
    if (!$this->isConnected()) {

      // Log any errors and throw an exception
      $this->Logger->error("Not connected to database.");
      throw new Exception("Not connected to database.");
    }
    $this->connection->rollback();
  }

  /**
   * Execute a Query to the database.
   *
   * @param  string  $query
   * @param  array  $params
   * @return statement object
   * @throws Exception
   */
  private function execute($query = "" , $params = []) {

    // If debug mode is enabled, print the query and parameters
    if($this->debug){
      $this->Logger->debug('Query: ');
      $this->Logger->debug($query);
    }
    if($this->debug){
      $this->Logger->debug('Params: ');
      $this->Logger->debug($params);
    }

    // Convert any parameters to UTF-8 encoding
    foreach($params as $key => $value){
      if(is_string($value)){
        if($encoding = mb_detect_encoding($value)){
          $params[$key] = mb_convert_encoding($value, 'UTF-8', $encoding);
        }
      }
    }

    try {
      // Prepare the statement using the provided query
      $stmt = $this->connection->prepare($query);
      if($this->debug){
        $this->Logger->debug('Prepared Statement: ');
        $this->Logger->debug($stmt);
      }

      // Throw an exception if the statement cannot be prepared
      if($stmt === false) {
        $this->Logger->error("Unable to do prepared statement: " . $query);
        throw New Exception("Unable to do prepared statement: " . $query);
      }

       // Bind any parameters to the statement
      if($params) {
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
        if($this->debug){
          $this->Logger->debug('Bind Parameters: ');
          $this->Logger->debug($types);
        }
        $stmt->bind_param($types, ...$params);
      }

      // Execute the statement
      $stmt->execute();
      if($this->debug){
        $this->Logger->debug('Executed Statement: ');
        $this->Logger->debug($stmt);
      }
      return $stmt;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
  }

  /**
   * Execute a Query to the database and return the result set as an associative array.
   *
   * @param  string  $query
   * @param  array  $params
   * @return array
   * @throws Exception
   */
  public function query($query, $params = []){
    try {
      // Execute the query and retrieve the result set as an associative array
      $stmt = $this->execute( $query, $params );
      $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      return $result;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Execute an INSERT query to the database and return the ID of the last inserted row.
   *
   * @param  string  $query
   * @param  array  $params
   * @return int
   * @throws Exception
   */
  public function insert($query = "" , $params = []) {
    try {
      // Execute the INSERT query and retrieve the ID of the last inserted row
      $stmt = $this->execute( $query , $params );
      $last_id = $stmt->insert_id;
      $stmt->close();
      return $last_id;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Execute a SELECT query to the database and return the result set as an associative array.
   *
   * @param  string  $query
   * @param  array  $params
   * @return array
   * @throws Exception
   */
  public function select($query = "" , $params = []) {
    try {
      // Execute the SELECT query and retrieve the result set as an associative array
      $stmt = $this->execute( $query , $params );
      $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      return $result;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Execute an UPDATE query to the database and return the number of affected rows.
   *
   * @param  string  $query
   * @param  array  $params
   * @return int
   * @throws Exception
   */
  public function update($query = "" , $params = []) {
    try {
      // Execute the UPDATE query and retrieve the number of affected rows
      $stmt = $this->execute( $query , $params );
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Execute a DELETE query to the database and return the number of affected rows.
   *
   * @param  string  $query
   * @param  array  $params
   * @return int
   * @throws Exception
   */
  public function delete($query = "" , $params = []) {
    try {
      // Execute the DELETE query and retrieve the number of affected rows
      $stmt = $this->execute( $query , $params );
      $result = $stmt->affected_rows;
      $stmt->close();
      return $result;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Create a new table in the database with the specified columns.
   *
   * @param  string  $table
   * @param  array  $columns
   * @return boolean
   * @throws Exception
   */
  public function create($table, $columns){
    try {
      $query = 'CREATE TABLE `'.$table.'` (';

      // Loop through each column and add it to the query
      foreach($columns as $name => $column){
        if(isset($column['type'])){

          // If this is not the first column, add a comma
          if(substr($query, -1) != '('){ $query .= ', '; }

          // Add the column name and type to the query
          $query .= '`'.$name.'` '.strtoupper($column['type']);

          // Loop through any extra options and add them to the query
          if(isset($column['extra']) && is_array($column['extra'])){
            foreach($column['extra'] as $extra){
              if(in_array(strtoupper($extra),['NULL','NOT NULL','UNIQUE','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']) || str_contains(strtoupper($extra), 'DEFAULT')){
                $query .= ' '.strtoupper($extra);
              }
            }
          }
        }
      }

      // Add character encoding to the query
      $query .= ' ) CHARACTER SET ' . $this->character;

      // Execute the query
      $stmt = $this->execute( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Modify an existing table in the database by adding, modifying, or dropping columns.
   *
   * @param  string  $table
   * @param  array  $columns
   * @return boolean
   * @throws Exception
   */
  public function alter($table, $columns){
    try {

      // Loop through each column and add it to the query
      foreach($columns as $name => $column){
        if(isset($column['action']) && in_array(strtoupper($column['action']),['MODIFY','ADD','DROP COLUMN'])){
          if(isset($column['type'])){

            // Build the query string based on the action specified
            $query = 'ALTER TABLE `'.$table.'` DEFAULT CHARACTER SET ' . $this->character . ', '.strtoupper($column['action']).' `'.$name.'` '.strtoupper($column['type']);

            // Loop through any extra options and add them to the query
            if(isset($column['extra']) && is_array($column['extra'])){
              foreach($column['extra'] as $extra){
                if(in_array(strtoupper($extra),['NULL','NOT NULL','UNIQUE','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']) || str_contains(strtoupper($extra), 'DEFAULT')){
                  $query .= ' '.strtoupper($extra);
                }
              }
            }

            // Execute the query
            $stmt = $this->execute( $query );
            $stmt->close();
          }
        }
      }
      return true;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Drop a table from the database.
   *
   * @param  string  $table
   * @return boolean
   * @throws Exception
   */
  public function drop($table){
    try {

      // Generate the SQL query to drop the table
      $query = 'DROP TABLE `'.$table.'`';

      // Execute the query
      $stmt = $this->execute( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }

  /**
   * Truncate a table in the database.
   *
   * @param  string  $table
   * @return boolean
   * @throws Exception
   */
  public function truncate($table){
    try {

      // Generate the SQL query to truncate the table
      $query = 'TRUNCATE TABLE `'.$table.'`';

      // Execute the query
      $stmt = $this->execute( $query );
      $stmt->close();
      return true;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw New Exception( $e->getMessage() );
    }
    return false;
  }
}
