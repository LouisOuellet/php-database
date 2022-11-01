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
#### Selecting Data
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Connect SQL Database
$DB = new Database("localhost","demo","demo","demo");

//Select Query
$users = $DB->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", 10]);

//Output Result
echo json_encode($users, JSON_PRETTY_PRINT);
```
