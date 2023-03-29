![GitHub repo logo](/dist/img/logo.png)

# phpDB
![License](https://img.shields.io/github/license/LouisOuellet/php-database?style=for-the-badge)
![GitHub repo size](https://img.shields.io/github/repo-size/LouisOuellet/php-database?style=for-the-badge&logo=github)
![GitHub top language](https://img.shields.io/github/languages/top/LouisOuellet/php-database?style=for-the-badge)
![Version](https://img.shields.io/github/v/release/LouisOuellet/php-database?label=Version&style=for-the-badge)

## Description
This is a PHP class that provides an interface for interacting with a MySQL database using the mysqli extension. It provides methods for creating, reading, updating, and deleting data from a database, as well as for creating and modifying database tables. It also includes methods for transaction handling and error logging.

The class uses prepared statements to prevent SQL injection attacks and supports UTF-8 encoding. It also includes debugging functionality that allows you to log queries and parameters to a file.

## Features
 - Connection pooling for improved performance
 - Automatic database schema migration
 - Query builder for easier construction of complex SQL queries
 - Support for transactions and rollbacks
 - Query profiling and optimization
 - Easy and secure interaction with a SQL database
 - Debugging functionality
 - Simplified handling of common SQL tasks

## Why you might need it?
phpDB is a simple and easy-to-use PHP class that provides an interface for interacting with a MySQL database using the mysqli extension. If you're building a web application or website that needs to store and retrieve data from a MySQL database, then phpDB can save you a lot of time and effort. The class provides methods for creating, reading, updating, and deleting data from a database, as well as for creating and modifying database tables. It uses prepared statements to prevent SQL injection attacks and supports UTF-8 encoding, which ensures that your data is stored and retrieved accurately. Additionally, phpDB includes debugging functionality that allows you to log queries and parameters to a file, making it easier to troubleshoot issues with your database. Whether you're a beginner or an experienced developer, phpDB can simplify your database interactions and help you get your web application up and running quickly.

## Can I use this?
Sure!

## License
This software is distributed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) license. Please read [LICENSE](LICENSE) for information on the software availability and distribution.

## Requirements
PHP >= 5.5.0

## To Do
 - Multiple Database Types Support (ex: MariaDB, MySQL, JSON, XML, PostgreSQL, SQLite)

## Security
Please disclose any vulnerabilities found responsibly – report security issues to the maintainers privately.

## Installation
Using Composer:
```sh
composer require laswitchtech/php-database
```

## How do I use it?
In this documentations, we will use a table called users for our examples.

### Examples
#### Connecting Database
##### Using Constant
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Define Connection Information
define("DB_HOST", "localhost");
define("DB_USERNAME", "demo");
define("DB_PASSWORD", "demo");
define("DB_DATABASE_NAME", "demo");

//Optionally Output Debug Information
define("DB_DEBUG", true);

//Connect SQL Database
$phpDB = new Database();
```

##### Without Using Constant
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");
```

#### Create a Table
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Create Table
$boolean = $phpDB->create('users',[
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
    'extra' => ['NOT NULL','UNIQUE']
  ],
  'created' => [
    'type' => 'DATETIME',
    'extra' => ['DEFAULT CURRENT_TIMESTAMP']
  ]
]);

//Output Result
echo json_encode($boolean, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Alter a Table
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Alter Table
$boolean = $phpDB->alter('users',[
  'email' => [
    'action' => 'ADD',
    'type' => 'VARCHAR(60)',
    'extra' => ['NOT NULL']
  ],
  'status' => [
    'action' => 'ADD',
    'type' => 'INT(1)',
    'extra' => ['NOT NULL','DEFAULT 0']
  ]
]);

//Output Result
echo json_encode($boolean, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Truncate a Table
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Alter Table
$boolean = $phpDB->truncate('users');

//Output Result
echo json_encode($boolean, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Drop a Table
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Alter Table
$boolean = $phpDB->drop('users');

//Output Result
echo json_encode($boolean, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Insert Data
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Insert Query
$id = $phpDB->insert("INSERT INTO users (username, email, status) VALUES (?,?,?)", ["user","user@domain.com",1]);

//Output Result
echo json_encode($id, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Select Data
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Select Query
$users = $phpDB->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", 10]);

//Output Result
echo json_encode($users, JSON_PRETTY_PRINT);
```

#### Update Data
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Update Query
$result = $phpDB->update("UPDATE users SET username = ?, email = ? WHERE id = ?", ["user".$id,"user".$id."@domain.com",$id]);

//Output Result
echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
```

#### Delete Data
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$phpDB = new Database("localhost","demo","demo","demo");

//Delete Query
$result = $phpDB->delete("DELETE FROM users WHERE id = ?", [$users[0]['id']]);

//Output Result
echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
```
