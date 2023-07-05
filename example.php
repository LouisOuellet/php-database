<?php

// Import Database class into the global namespace
// These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initialize Database
$phpDB = new Database();

// Configure Database, you can also use a config file
$phpDB->config("host","localhost")->config("username","demo")->config("password","demo")->config("database","demo1");

// Connect to Database
$phpDB->connect();

// Example 1: Create a table
function install(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if(!$phpDB->getTable("version")){
      // Create table
      $phpDB->create('version',[
        'version' => [
          'type' => 'BIGINT(10)',
          'extra' => ['UNSIGNED','PRIMARY KEY']
        ]
      ]);
    }
  }
}

// Example 2: Alter a table
function alter(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Alter table (add column)
      $phpDB->alter('version',[
        'status' => [
          'action' => 'ADD',
          'type' => 'INT(1)',
          'extra' => ['NOT NULL','DEFAULT 0']
        ],
      ]);
      // Alter table (modify column)
      $phpDB->alter('version',[
        'status' => [
          'action' => 'MODIFY',
          'type' => 'INT(1)',
          'extra' => ['NULL']
        ],
      ]);
      // Alter table (drop column)
      $phpDB->alter('version',[
        'status' => [
          'action' => 'DROP COLUMN'
        ],
      ]);
    }
  }
}

// Example 3: Drop a table
function uninstall(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Drop table
      $phpDB->drop('version');
    }
  }
}

// Example 4: Insert Data
function insert(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Insert data
      $version = $phpDB->insert("INSERT INTO version (version) VALUES (?)", [1]);
    }
  }
}

// Example 5: Select Data
function select(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Select data
      $version = $phpDB->select("SELECT * FROM version WHERE version = ?", [1]);
    }
  }
}

// Example 6: Update Data
function update(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Update data
      $version = $phpDB->update("UPDATE version SET version = ? WHERE version = ?", [2,1]);
    }
  }
}

// Example 7: Delete Data
function delete(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Check if table exists (optional)
    if($phpDB->getTable("version")){
      // Delete data
      $version = $phpDB->delete("DELETE FROM version WHERE version = ?", [2]);
    }
  }
}

// Example 8: Backup Database
function backup(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Backup database
    $backup = $phpDB->backup();
  }
}

// Example 9: Restore Database
function restore(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Restore database
    $restore = $phpDB->restore();
  }
}

// Example 10: Creating Database Schema
function schema(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Create schema
    $schema = $phpDB->schema();
  }
}

// Example 11: Upgrade Database Schema
function upgrade(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Upgrade schema, if no version is provided, the latest version will be used
    $upgrade = $phpDB->upgrade();
  }
}

// Example 12: Getters
function get(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Get table, returns an array or null when table does not exist
    $table = $phpDB->getTable("users");
    // Get columns, returns an array or null when table does not exist
    $columns = $phpDB->getColumns("users");
    // Get required columns, returns an array or null when table does not exist
    $required = $phpDB->getRequired("users");
    // Get default columns, returns an array or null when table does not exist
    $defaults = $phpDB->getDefaults("users");
    // Get primary columns, returns an array or null when table does not exist
    $primary = $phpDB->getPrimary("users");
    // Get on update columns, returns an array or null when table does not exist
    $onupdate = $phpDB->getOnUpdate("users");
  }
}

// Example 13: Advanced
function advanced(){
  // Check if the database is connected
  if($phpDB->isConnected()){
    // Install the basics
    install();
    // Insert initial Version
    insert();
    // Save the current Schema
    schema();
    // Check if a table exists
    if($phpDB->getTable("users")){
      // Drop it
      $phpDB->drop('users');
    }
    // Add a new table
    $phpDB->create('users',[
      'id' => [
        'type' => 'BIGINT(10)',
        'extra' => ['UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']
      ],
      'username' => [
        'type' => 'VARCHAR(60)',
        'extra' => ['NOT NULL','UNIQUE']
      ],
      'password' => [
        'type' => 'VARCHAR(100)',
        'extra' => ['NOT NULL']
      ],
      'token' => [
        'type' => 'VARCHAR(100)',
        'extra' => ['NOT NULL']
      ],
      'email' => [
        'type' => 'VARCHAR(60)',
        'extra' => ['NOT NULL']
      ]
    ]);
    // Insert data
    $id = $phpDB->insert("INSERT INTO users (username, password, token, email) VALUES (?,?,?,?)", ["user","pass","token","user@domain.com"]);
    // Update version
    $phpDB->update("UPDATE version SET version = ?", ['2']);
    // Create a new schema
    schema();
    // Back up the database
    backup();
  }
}
