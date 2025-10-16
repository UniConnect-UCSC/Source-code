<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

// Session setup
$sessionPath = __DIR__ . "/../storage/sessions";
if (!is_dir($sessionPath)) mkdir($sessionPath, 0777, true);
session_save_path($sessionPath);
session_start();

require '../app/core/init.php';
handleAuth();

// Load controllers
$app = new App;
$app->loadController();