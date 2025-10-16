<?php

require(__DIR__ . "/loadENV.php");
loadEnv(__DIR__ . "/../../.env.local");

if ($_SERVER['SERVER_NAME'] == 'uniconnect.local') {
    define('ROOT', 'http://uniconnect.local');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

define('DBHOST', getenv('DB_HOST'));
define('DBPORT', getenv('DB_PORT'));
define('DBNAME', getenv('DB_NAME'));
define('DBUSER', getenv('DB_USER'));
define('DBPASSWORD', getenv('DB_PASSWORD'));

define('DEBUG', true);