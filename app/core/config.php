<?php

use Dotenv\Dotenv;

//Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('DBHOST', $_ENV['DB_HOST'] ?: 'localhost');
define('DBPORT', $_ENV['DB_PORT'] ?: 5432);
define('DBNAME', $_ENV['DB_NAME'] ?: 'uniconnect');
define('DBUSER', $_ENV['DB_USER'] ?: 'postgres');
define('DBPASSWORD', $_ENV['DB_PASSWORD'] ?: '');
define('CLOUDINARY_URL', $_ENV['CLOUDINARY_URL'] ?: '');


define('DEBUG', true);