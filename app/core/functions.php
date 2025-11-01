<?php

use Cloudinary\Api\Upload\UploadApi;

function uploadImageToCloudinary($filePath, $locationFolder): ?string
{
    $uploadApi = new UploadApi();
    try {
        $response = $uploadApi->upload($filePath, [
            'folder' => $locationFolder,
            'resource_type' => 'auto',
        ]);
        return $response['secure_url'] ?? null;
    } catch (Exception $e) {
        error_log("Cloudinary upload error: " . $e->getMessage());
        return null;
    }
}


function component($name, $data = [])
{
    // Build the path to the component file
    $path = __DIR__ . "/../views/components/{$name}.component.php";
    if (file_exists($path)) {
        // Extract variables for use inside the component
        extract($data);
        require $path;
    } else {
        throw new Exception("Component '{$name}' not found at {$path}");
    }
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

function handleAuth()
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $loggedIn = !empty($_SESSION['user_email']);

    // If user is logged in and visits login/signup → redirect to home
    if ($loggedIn && in_array($currentPath, ['/login', '/signup'])) {
        header("Location: /");
        exit;
    }

    // If user is NOT logged in and tries to access protected pages → redirect to login
    if (!$loggedIn && !in_array($currentPath, ['/login', '/signup'])) {
        header("Location: /login");
        exit;
    }
}

function getEmailDomain($email)
{
    //Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }

    $parts = explode('@', $email);
    return isset($parts[1]) ? $parts[1] : null;
}


function timeAgo($datetime)
{
    // Set timezone to match your database
    date_default_timezone_set('Asia/Colombo');

    // Remove microseconds
    $datetime = preg_replace('/\.\d+/', '', $datetime);

    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 60) {
        return $diff === 1 ? '1 second ago' : "$diff seconds ago";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes === 1 ? '1 minute ago' : "$minutes minutes ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours === 1 ? '1 hour ago' : "$hours hours ago";
    } elseif ($diff < 172800) {
        return "Yesterday";
    } else {
        $days = floor($diff / 86400);
        return "$days days ago";
    }
}
