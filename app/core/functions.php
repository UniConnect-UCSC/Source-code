<?php
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