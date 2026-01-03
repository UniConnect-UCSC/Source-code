<?php
spl_autoload_register(function ($classname) {
    $filename = "../app/models/" . ucfirst($classname) . ".php";
});

require 'config.php';
require 'utils.php';
require 'functions.php';
require 'Database.php';
require 'mediaStorageService.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';

// Notification System
require 'notifications.php';