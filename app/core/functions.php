<?php

// $file should be passed the $_FILE['name'] associative array
function uploadImageToCloudinary($file, $locationFolder, $signed = false): ?string
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
    $uploadedKey = $signed 
        ? $mediaStorageService->uploadSignedMedia($file['tmp_name'], $locationFolder)
        : $mediaStorageService->uploadMedia($file['tmp_name'], $locationFolder);

    if(!$uploadedKey){
        error_log('Cloudinary upload failed for post by user ' . ($_SESSION['user_id'] ?? 'unknown'));
        return null;
    }

    return $uploadedKey;
}

function getCloudinarySignedURL($secretKey, $expireInSeconds = 600): ?string{
    $mediaStorageService = new CloudinaryMediaStorageService();
    $responseUrl = $mediaStorageService->getMediaSignedURL($secretKey, $expireInSeconds);

    if(!$responseUrl){
        error_log('Cloudinary get signed URL failed for key ' . $secretKey);
        return null;
    }

    return $responseUrl;
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
    $adminLoggedIn = !empty($_SESSION['is_admin']);

    $isAdminRoute = str_starts_with($currentPath, '/admin');

    if ($isAdminRoute) {
        if (!$adminLoggedIn && !in_array($currentPath, ['/admin', '/admin/login', '/admin/loginAdmin'])) {
            header("Location: /admin");
            exit;
        }

        return;
    }

    if ($loggedIn && in_array($currentPath, ['/login', '/signup'])) {
        header("Location: /");
        exit;
    }

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

function parseRequestData(){

    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    $parts = explode(';', $contentType);
    $baseType = isset($parts[0]) ? strtolower(trim($parts[0])) : '';

    error_log("Parsing request data of type: " . $baseType);

    $data = [];

    switch($baseType){
        case 'application/json':
            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, true);
            break;

        case 'application/x-www-form-urlencoded':
            $data = $_POST;
            break;

        case 'multipart/form-data':
            $data = $_POST;
            $data["FILES"] = $_FILES;
            break;
        
        case 'text/plain':
            $rawData = file_get_contents('php://input');
            $data = $rawData;
            break;

        default:
            //Not set
    }

    error_log("Parsed request data: " . print_r($data, true));

    return $data;
}