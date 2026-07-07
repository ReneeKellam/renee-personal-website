<?php
    require_once __DIR__ . '/../../config/new-config.php';
    require_once __DIR__ . '/../api-calls/functions-passwords.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Incorrect password. Please try again.</p>
    <?php endif; ?>
</body>
</html>