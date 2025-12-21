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

require '../app/core/init.php';

// Singleton Notification service
$notificationService = new NotificationService();

if ($_ENV['INSTANT_NOTIFICATION']) {
    $notificationService->enableInstantNotifications();
    error_log("Instant notifications enabled");
}

handleAuth();

// Load controllers
$app = new App;
$app->loadController();
