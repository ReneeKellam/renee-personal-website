<?php
session_start();
if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    header('Location: /renee-kellam-@dm1n/verification.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST" action="/renee-kellam-@dm1n/verification.php">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Incorrect password. Please try again.</p>
    <?php endif; ?>
</body>
</html>