<?php

// $file should be passed the $_FILE['name'] associative array
function uploadImageToCloudinary($file, $locationFolder): ?string
{
    if(empty($file)){
        error_log("No file uploaded by user " . ($_SESSION['user_id']));
        return null;
    }

    if($file['error'] !== UPLOAD_ERR_OK){
        error_log("File upload to server failed: " . $file['error'] . 'by user ' . $_SESSION['user_id']);
        return null;
    }

    $mediaStorageService = new CloudinaryMediaStorageService();
    $uploadedUrl = $mediaStorageService->uploadMedia($file['tmp_name'], $locationFolder);

    if(!$uploadedUrl){
        error_log('Cloudinary upload failed for post by user ' . ($_SESSION['user_id'] ?? 'unknown'));
        return null;
    }

    return $uploadedUrl;
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
