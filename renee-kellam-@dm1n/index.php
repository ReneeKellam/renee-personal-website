<?php
    require_once __DIR__ . '/../config/new-config.php';
    require_once __DIR__ . '/api-calls/functions-passwords.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        $msg = login($_POST['password']);
        
        if ($msg === true) {
            header("location: pages/dashboard.php");
            exit();
        }

        unset($_POST); // Clear POST data to prevent resubmission and security
    }

    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("location: pages/dashboard.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/config/styles.css">
</head>
<body>
    <?php if (isset($msg)) modalDisplay($msg); ?>
    <div class="page-content">
        <h1 class="centered">Admin Login</h1>
     <div class="card">
        <form method="POST">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
     </div>
</body>
</html>