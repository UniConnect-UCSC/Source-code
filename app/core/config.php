<?php

// Set the server name based on each user's virtual host or host name.
if ($_SERVER['SERVER_NAME'] == 'uniconnect.local') {
    define('ROOT', 'http://uniconnect.local');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

define('DBHOST', "localhost");
define('DBPORT', 5432);
define('DBNAME', "uniconnect");
define('DBUSER', "postgres");
define('DBPASSWORD', "0000");

define('DEBUG', true);