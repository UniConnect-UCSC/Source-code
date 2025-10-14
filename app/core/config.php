<?php

require(__DIR__ . "/loadENV.php");
loadEnv(__DIR__ . "/../../.env.local");

if ($_SERVER['SERVER_NAME'] == 'uniconnect.local') {
    define('ROOT', 'http://uniconnect.local');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

define('DBHOST', getenv('DB_HOST') ?: 'db');
define('DBPORT', getenv('DB_PORT') ?: 5432);
define('DBNAME', getenv('DB_NAME') ?: 'uniconnect');
define('DBUSER', getenv('DB_USER') ?: 'postgres');
define('DBPASSWORD', getenv('DB_PASSWORD') ?: 'admin');

define('DEBUG', true);