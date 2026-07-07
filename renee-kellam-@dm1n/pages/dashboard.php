<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();
    require_once __DIR__ . '/../api-calls/functions-passwords.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $resultMessage = changeAdminPassword($currentPassword, $newPassword, $confirmPassword);
    }
?>

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

<?php include __DIR__ . '/../admin-header.php'; ?>

<?php if (isset($resultMessage)) : ?>
    <div class="modal">
        <p><?php echo htmlspecialchars($resultMessage); ?></p>
        <button onclick="this.parentElement.style.display='none';">Close</button>
    </div>
<?php endif; ?>

<div class="page-content">
    <h1 class="centered">Admin Dashboard</h1>
    <div class="card">
        <!-- matomo card, not yet implimented -->
    </div>
    <div class="card">
        <h2 class="centered">Change Admin Password</h2>
        <div class="centered">
            <form class="centered" method="post" style="max-width:400px;">
                <input type="hidden" name="action" value="change_password">
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

<style>
    .modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .modal p {
        margin-bottom: 20px;
    }

    .modal button {
        background-color: #a630c4;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
    }

    .modal button:hover {
        background-color: #8e2ec4;
    }
</style>