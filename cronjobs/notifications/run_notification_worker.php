#!/usr/bin/env php
<?php

// Change to the script directory for proper relative path resolution
chdir(__DIR__);

// Load the notification worker
require_once __DIR__ . '/../../app/workers/notificationWorker.php';
$logFileLocation = __DIR__ . '/../notification_worker.log';

// Log the start of the job
$logMessage = "[" . date('Y-m-d H:i:s') . "] Starting notification worker\n";
error_log($logMessage);
file_put_contents($logFileLocation, $logMessage, FILE_APPEND);

try {
    $channelNames = ["inAppNotification"]; 

    // Initialize the notification worker
    $worker = new NotificationWorker($channelNames);
    
    // Execute queued notifications
    $worker->executeQueuedNotifications();
    
    $successMessage = "[" . date('Y-m-d H:i:s') . "] Notification worker completed successfully\n";
    error_log($successMessage);
    file_put_contents($logFileLocation, $successMessage, FILE_APPEND);
    
    exit(0); // Success
    
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    $errorMessage .= "Stack trace: " . $e->getTraceAsString() . "\n";
    error_log($errorMessage);
    file_put_contents($logFileLocation, $errorMessage, FILE_APPEND);
    
    exit(1); // Error
}
