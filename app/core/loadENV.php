<?php
function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception("Env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        if (trim($line)[0] === '#') continue;

        // Split name and value
        list($name, $value) = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, "\"' ");

        // Set environment variable
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}