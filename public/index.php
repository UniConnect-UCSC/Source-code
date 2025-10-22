<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();


// Session setup
$sessionPath = __DIR__ . "/../storage/sessions";
if (!is_dir($sessionPath)) mkdir($sessionPath, 0777, true);
session_save_path($sessionPath);
session_start();


require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Cloudinary;

//Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

//Config Cloudinary
$config = new Configuration($_ENV['CLOUDINARY_URL']);
$cloudinary = new Cloudinary($config);

require '../app/core/init.php';
handleAuth();

// Load controllers
$app = new App;
$app->loadController();
