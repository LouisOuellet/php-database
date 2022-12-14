![GitHub repo logo](/dist/img/logo.png)

# phpDB
![License](https://img.shields.io/github/license/LouisOuellet/php-database?style=for-the-badge)
![GitHub repo size](https://img.shields.io/github/repo-size/LouisOuellet/php-database?style=for-the-badge&logo=github)
![GitHub top language](https://img.shields.io/github/languages/top/LouisOuellet/php-database?style=for-the-badge)
![Version](https://img.shields.io/github/v/release/LouisOuellet/php-database?label=Version&style=for-the-badge)

## Features
 - SQL Database Handling

## Why you might need it
If you are looking for an easy way to start using SQL. This PHP Class is for you.

## Can I use this?
Sure!

## License
This software is distributed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) license. Please read [LICENSE](LICENSE) for information on the software availability and distribution.

## Requirements
PHP >= 5.5.0

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
