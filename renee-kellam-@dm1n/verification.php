<?php
if (!isset($_SESSION)) session_start();
include __DIR__ . '/../config/config.php';

loadEnv();
$admin_password = env('ADMIN_PASSWORD', 'changeme');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = isset($_POST['password']) ? $_POST['password'] : '';
    if ($input === $admin_password) {
        $_SESSION['admin_authenticated'] = true;
        header('Location: /renee-kellam-@dm1n/verification.php?success=1');
        exit;
    } else {
        header('Location: /renee-kellam-@dm1n/index.php?error=1');
        exit;
    }
} else {
    // If accessed directly, check session and show success or redirect
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        header('Location: /renee-kellam-@dm1n/index.php');
        exit;
    }
    header('Location: /renee-kellam-@dm1n/renee-kellam-@dm1n-index.php');
}
?>