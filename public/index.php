<?php
require_once 'db_config.php';

$status = 'Database connection successful!';
try {
    $pdo->query('SELECT 1');
} catch (Exception $e) {
    $status = 'Database connection failed: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Connection Test</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Database Connection Test</h1>
        <p class="status <?php echo ($status === 'Database connection successful!') ? 'success' : 'error'; ?>">
            <?php echo $status; ?>
        </p>
    </div>
</body>
</html>
