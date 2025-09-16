<?php include __DIR__ . '/admin-checker.php'; ?>
<?php include __DIR__ . '/../config/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="/config/styles.css">
    <link rel="icon" href="/assets/small icon.jpg" type="image/jpg">
    <link rel="stylesheet" href="../config/styles.css">
    <style> .stats-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/adminheader.php'; ?>

<div class="page-content">
    <h1 class="centered">Admin Dashboard</h1>
    <div class="card">
        <h2 class="centered">Change Admin Password</h2>
        <div class="centered">
            <form class="centered" action="change-password.php" method="post" style="max-width:400px;">
                <label for="current_password">Current Password:</label><br>
                <input type="password" name="current_password" required><br>
                <label for="new_password">New Password:</label><br>
                <input type="password" name="new_password" required><br>
                <label for="confirm_password">Confirm New Password:</label><br>
                <input type="password" name="confirm_password" required><br><br>
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>
</div>
