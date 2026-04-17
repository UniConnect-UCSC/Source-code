#!/usr/bin/env php
<?php


// Change to the script directory for proper relative path resolution
chdir(__DIR__);

$logFileLocation = __DIR__ . '/../logs/notification_worker.log';

ini_set('log_errors', 1);
ini_set('error_log', $logFileLocation);
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        error_log("FATAL: " . print_r($error, true));
    }
});

// Load the notification worker
require_once __DIR__ . '/../../app/workers/notificationWorker.php';

// Log the start of the job
$logMessage = "[" . date('Y-m-d H:i:s') . "] Starting notification worker\n";
error_log($logMessage);

try {
    $channelNames = ["inAppNotification"]; 

    // Initialize the notification worker
    $worker = new NotificationWorker($channelNames);
    
    // Execute queued notifications
    $worker->executeQueuedNotifications();
    
    $successMessage = "[" . date('Y-m-d H:i:s') . "] Notification worker completed successfully\n";
    error_log($successMessage);
    
    exit(0); // Success
    
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    $errorMessage .= "Stack trace: " . $e->getTraceAsString() . "\n";
    error_log($errorMessage);
    
    exit(1); // Error
}
