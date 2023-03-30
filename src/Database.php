<?php

//Declaring namespace
namespace LaswitchTech\phpDB;

//Import phpConfigurator class into the global namespace
use LaswitchTech\phpConfigurator\phpConfigurator;

//Import phpLogger class into the global namespace
use LaswitchTech\phpLogger\phpLogger;

//Import phpNet class into the global namespace
use LaswitchTech\phpNet\phpNet;

//Import mysqli class into the global namespace
use \mysqli;

//Import Exception class into the global namespace
use \Exception;

class Database {

  private $connection = null;
  private $character = 'utf8mb4';
  private $collate = 'utf8mb4_general_ci';

	// Logger
	private $Logger;
	private $Level = 1;

	// NetTools
	private $NetTools;

  // Configurator
  private $Configurator = null;

  // Database
  private $Host = null;
  private $Username = null;
  private $Password = null;
  private $Database = null;

  /**
   * Create a new Database instance.
   *
   * @param  string|null  $Host
   * @param  string|null  $Username
   * @param  string|null  $Password
   * @param  string|null  $Database
   * @return void
   * @throws Exception
   */
  public function __construct($Host = null, $Username = null, $Password = null, $Database = null) {

    // Initialize Configurator
    $this->Configurator = new phpConfigurator('database');

    // Retrieve Log Level
    $this->Level = $this->Configurator->get('logger', 'level') ?: $this->Level;

    // Initiate phpLogger
    $this->Logger = new phpLogger('database');

    // Initiate phpNet
    $this->NetTools = new phpNet();

    // Retrieve Parameters
    $this->Host = $Host ?: $this->Host;
    $this->Username = $Username ?: $this->Username;
    $this->Password = $Password ?: $this->Password;
    $this->Database = $Database ?: $this->Database;

    // Set default parameter values if not specified
    if($this->Host === null){
      $this->Host = $this->Configurator->get('database', 'host') ?: $this->Host;
    }
    if($this->Username === null){
      $this->Username = $this->Configurator->get('database', 'username') ?: $this->Username;
    }
    if($this->Password === null){
      $this->Password = $this->Configurator->get('database', 'password') ?: $this->Password;
    }
    if($this->Database === null){
      $this->Database = $this->Configurator->get('database', 'database') ?: $this->Database;
    }

    // Attempt a connection
    if($this->Host !== null && $this->Username !== null && $this->Password !== null && $this->Database !== null){
      $this->connect($this->Host,$this->Username,$this->Password,$this->Database);
    }
  }

  /**
   * Configure Library.
   *
   * @param  string  $option
   * @param  bool|int  $value
   * @return void
   * @throws Exception
   */
  public function config($option, $value){
		try {
			if(is_string($option)){
	      switch($option){
	        case"host":
	          if(is_string($value)){

							// Logging Level
	            $this->Host = $value;

              // Save to Configurator
              $this->Configurator->set('database',$option, $value);
	          } else{
	            throw new Exception("2nd argument must be a string.");
	          }
	          break;
	        case"username":
	          if(is_string($value)){

							// Logging Level
	            $this->Username = $value;

              // Save to Configurator
              $this->Configurator->set('database',$option, $value);
	          } else{
	            throw new Exception("2nd argument must be a string.");
	          }
	          break;
	        case"password":
	          if(is_string($value)){

							// Logging Level
	            $this->Password = $value;

              // Save to Configurator
              $this->Configurator->set('database',$option, $value);
	          } else{
	            throw new Exception("2nd argument must be a string.");
	          }
	          break;
	        case"database":
	          if(is_string($value)){

							// Logging Level
	            $this->Database = $value;

              // Save to Configurator
              $this->Configurator->set('database',$option, $value);
	          } else{
	            throw new Exception("2nd argument must be a string.");
	          }
	          break;
	        case"level":
	          if(is_int($value)){

							// Logging Level
	            $this->Level = $value;

							// Configure phpNet
              $this->NetTools->config('level',$this->Level);

							// Configure phpLogger
					    $this->Logger->config('level',$this->Level);
	          } else{
	            throw new Exception("2nd argument must be an integer.");
	          }
	          break;
	        default:
	          throw new Exception("unable to configure $option.");
	          break;
	      }
	    } else{
	      throw new Exception("1st argument must be as string.");
	    }
		} catch (Exception $e) {
			$this->Logger->error('Error: '.$e->getMessage());
		}

    return $this;
  }

  /**
   * Connect Database.
   *
   * @param  string|null  $host
   * @param  string|null  $username
   * @param  string|null  $password
   * @param  string|null  $database
   * @return void
   * @throws Exception
   */
  public function connect($host, $username, $password, $database) {

    // Attempt a connection to the database
    try {

      // Debug Information
      $this->Logger->info("Establishing connection to database.");
      $this->Logger->debug("Host: " . $host);
      $this->Logger->debug("Username: " . $username);
      $this->Logger->debug("Password: " . $password);
      $this->Logger->debug("Database: " . $database);

      // Checking for an active connection
      if($this->isConnected()){
        throw new Exception("Database already connected.");
      }

      // Checking for an open port
      if(!$this->NetTools->scan($host,3306)){
        throw new Exception("SQL port on {$host} is closed or blocked.");
      }

      // Create a new mysqli connection
      $this->connection = new mysqli($host, $username, $password, $database);

      // Turn off warnings
      error_reporting(E_ALL ^ E_WARNING);

      // Throw an exception if connection failed
      if(mysqli_connect_errno()){
        $this->Logger->error("Could not connect to database.");
        throw new Exception(mysqli_connect_errno());
      } else {
        $this->Logger->success("Database connected.");
      }

      // Turn on warnings
      error_reporting(E_ALL);
    } catch (Exception $e) {

      // Clear connection
      $this->connection = null;

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
    }
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
    $this->Logger->debug('Query: ');
    $this->Logger->debug($query);
    $this->Logger->debug('Params: ');
    $this->Logger->debug($params);

    // Convert any parameters to UTF-8 encoding
    foreach($params as $key => $value){
      if(is_string($value)){
        if($encoding = mb_detect_encoding($value)){
          $params[$key] = mb_convert_encoding($value, 'UTF-8', $encoding);
        }
      }
    }

    try {

      // Check if Database is connected
      if(!$this->isConnected()){
        throw New Exception("Database is not connected.");
      }

      // Prepare the statement using the provided query
      $stmt = $this->connection->prepare($query);
      $this->Logger->debug('Prepared Statement: ');
      $this->Logger->debug($stmt);

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
        $this->Logger->debug('Bind Parameters: ');
        $this->Logger->debug($types);
        $stmt->bind_param($types, ...$params);
      }

      // Execute the statement
      $stmt->execute();
      $this->Logger->debug('Executed Statement: ');
      $this->Logger->debug($stmt);
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

      // Return result
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

      // Return last_id
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

      // Return result
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

      // Return result
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

      // Return result
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
  public function create($table, $columns, $uniqueKeys = null){
    try {

      // Check if table exist
      if($this->getTable($table)){
        throw New Exception("This table already exist");
      }

      // Start building Query
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

      // Add UNIQUE KEY constraints if provided
      if (!empty($uniqueKeys) && is_array($uniqueKeys)) {
        foreach ($uniqueKeys as $keyName => $keyColumns) {
          $query .= ', UNIQUE KEY `' . $keyName . '` (' . implode(', ', array_map(function ($col) {
            return '`' . $col . '`';
          }, $keyColumns)) . ')';
        }
      }

      // Add character encoding to the query
      $query .= ' ) CHARACTER SET ' . $this->character;

      // Execute the query
      $stmt = $this->execute( $query );

      // Return boolean
      return true;
    } catch(Exception $e) {

      // Log any errors
      $this->Logger->error($e->getMessage());

      // Throw an exception if level is higher than 5
      if($this->Level > 5){
        throw New Exception( $e->getMessage() );
      }
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

     // Check if table exist
     if(!$this->getTable($table)){
       throw New Exception("This table does not exist");
     }

     // Loop through each column and add it to the query
     foreach($columns as $name => $column){
       if(isset($column['action'])){
         // Build the query string based on the action specified
         $query = 'ALTER TABLE `'.$table.'` ';

         if(in_array(strtoupper($column['action']),['MODIFY','ADD','DROP COLUMN']) && isset($column['type'])){
           $query .= strtoupper($column['action']).' `'.$name.'` '.strtoupper($column['type']);

           // Loop through any extra options and add them to the query
           if(isset($column['extra']) && is_array($column['extra'])){
             foreach($column['extra'] as $extra){
               if(in_array(strtoupper($extra),['NULL','NOT NULL','UNIQUE','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']) || str_contains(strtoupper($extra), 'DEFAULT')){
                 $query .= ' '.strtoupper($extra);
               }
             }
           }
         } elseif (strtoupper($column['action']) == 'ADD UNIQUE KEY' && isset($column['keyName']) && isset($column['columns']) && is_array($column['columns'])) {
           // Add unique key on multiple columns
           $query .= 'ADD UNIQUE KEY `'.$column['keyName'].'` ('.implode(', ', array_map(function($col) { return "`$col`"; }, $column['columns'])).')';
         } else {
           continue; // Skip to next iteration if the action is not supported
         }

         // Execute the query
         $stmt = $this->execute( $query );
       }
     }
     return true;
   } catch(Exception $e) {

     // Log any errors
     $this->Logger->error($e->getMessage());

     // Throw an exception if level is higher than 5
     if($this->Level > 5){
       throw New Exception( $e->getMessage() );
     }
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

      // Check if table exist
      if(!$this->getTable($table)){
        throw New Exception("This table does not exist");
      }

      // Generate the SQL query to drop the table
      $query = 'DROP TABLE `'.$table.'`';

      // Execute the query
      $stmt = $this->execute( $query );

      // Return boolean
      return true;
    } catch(Exception $e) {

      // Log any errors
      $this->Logger->error($e->getMessage());

      // Throw an exception if level is higher than 5
      if($this->Level > 5){
        throw New Exception( $e->getMessage() );
      }
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

      // Check if table exist
      if(!$this->getTable($table)){
        throw New Exception("This table does not exist");
      }

      // Generate the SQL query to truncate the table
      $query = 'TRUNCATE TABLE `'.$table.'`';

      // Execute the query
      $stmt = $this->execute( $query );

      // Return boolean
      return true;
    } catch(Exception $e) {

      // Log any errors
      $this->Logger->error($e->getMessage());

      // Throw an exception if level is higher than 5
      if($this->Level > 5){
        throw New Exception( $e->getMessage() );
      }
    }
    return false;
  }

  /**
   * Check if a table exists in the database.
   *
   * @param  string  $table
   * @return bool
   * @throws Exception
   */
  public function getTable($table) {
    try {

      // Retrieve Results
      $result = $this->connection->query("SHOW TABLES LIKE '$table'");

      // Return Boolean based on table existence
      return $result->num_rows > 0;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the columns of a table.
   *
   * @param  string  $table
   * @return array
   * @throws Exception
   */
  public function getColumns($table) {
    try {

      // Retrieve the table's columns
      $result = $this->connection->query("DESCRIBE $table");

      // Initialize the columns array
      $columns = array();

      // Generate the columns array
      while ($row = $result->fetch_assoc()) {

        // Extract the data type from the Type field
        preg_match('/^([^\(]+)(\(.+?\))?/', $row['Type'], $matches);
        $dataType = $matches[1];

        // Add column to array
        $columns[$row['Field']] = $dataType;
      }

      // Return the array of columns
      return $columns;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the required columns for inserting a new row.
   *
   * @param  string  $table
   * @return array
   * @throws Exception
   */
  public function getRequired($table) {
    try {

      // Retrieve the table's columns
      $result = $this->connection->query("DESCRIBE $table");

      // Initialize the required columns array
      $requiredColumns = array();

      // Generate the required columns array
      while ($row = $result->fetch_assoc()) {

        // Check if the column is required
        if ($row['Null'] === 'NO' && $row['Default'] === null) {

          // Add column to array
          $requiredColumns[] = $row['Field'];
        }
      }

      // Return the array of required columns
      return $requiredColumns;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the required columns for inserting a new row.
   *
   * @param  string  $table
   * @return array
   * @throws Exception
   */
  public function getDefaults($table) {
    try {

      // Retrieve the table's columns
      $result = $this->connection->query("DESCRIBE $table");

      // Initialize the columns array
      $columns = array();

      // Generate the required columns array
      while ($row = $result->fetch_assoc()) {

        // Check if the column is required
        if ($row['Default'] !== null) {

          // Add column to array
          $columns[$row['Field']] = $row['Default'];
        }
      }

      // Return the array of columns
      return $columns;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the columns of a table.
   *
   * @param  string  $table
   * @return array
   * @throws Exception
   */
  public function getOnUpdate($table) {
    try {

      // Retrieve the table's columns
      $result = $this->connection->query("DESCRIBE $table");

      // Initialize the columns array
      $columns = array();

      // Generate the columns array
      while ($row = $result->fetch_assoc()) {

        // Check if the column has an "ON UPDATE" default value
        if (strpos($row['Extra'], 'on update') !== false) {

          // Extract the "ON UPDATE" default value from the column's Extra field
          preg_match('/on update\s+(.+)/i', $row['Extra'], $matches);

          // Add column to array
          $columns[$row['Field']] = $matches[1];
        }
      }

      // Return the array of columns
      return $columns;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the primary key of a table.
   *
   * @param string $table
   * @return string|null
   * @throws Exception
   */
  public function getPrimary($table) {
    try {
      // Retrieve the table's structure
      $result = $this->connection->query("DESCRIBE $table");

      // Loop through the rows to find the primary key
      while ($row = $result->fetch_assoc()) {
        if ($row['Key'] === 'PRI') {
          return $row['Field'];
        }
      }

      // If no primary key was found, return null
      return null;
    } catch (Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }

  /**
   * Retrieve the required columns for inserting a new row.
   *
   * @param  string  $table
   * @return array
   * @throws Exception
   */
  public function getNullables($table) {
    try {

      // Retrieve the table's columns
      $result = $this->connection->query("DESCRIBE $table");

      // Initialize the columns array
      $columns = array();

      // Generate the required columns array
      while ($row = $result->fetch_assoc()) {

        // Check if the column is required
        if ($row['Null'] !== 'NO') {

          // Add column to array
          $columns[] = $row['Field'];
        }
      }

      // Return the array of columns
      return $columns;
    } catch(Exception $e) {

      // Log any errors and throw an exception
      $this->Logger->error($e->getMessage());
      throw new Exception($e->getMessage());
    }
  }
}
