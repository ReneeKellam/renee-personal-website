<?php
include __DIR__ . '/admin-checker.php';
include __DIR__ . '/../config/config.php';

loadEnv();
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$old = env('ADMIN_PASSWORD');

if (!$current || !$new || !$confirm) {
    $msg = urlencode("All fields are required.");
    header("Location: admin-index.php?msg=$msg");
    exit;
}

if ($current !== $old) {
    $msg = urlencode("Current password is incorrect.");
    header("Location: admin-index.php?msg=$msg");
    exit;
}

if ($new !== $confirm) {
    $msg = urlencode("New passwords do not match.");
    header("Location: admin-index.php?msg=$msg");
    exit;
}

// Update .env file
$envPath = realpath(__DIR__ . '/../.env');
if (!$envPath || !is_writable($envPath)) {
    $msg = urlencode("Cannot write to .env file.");
    header("Location: admin-index.php?msg=$msg");
    exit;
}

$env = file($envPath, FILE_IGNORE_NEW_LINES);
foreach ($env as &$line) {
    if (strpos($line, 'ADMIN_PASSWORD=') === 0) {
        $line = 'ADMIN_PASSWORD=' . $new;
    }
}
file_put_contents($envPath, implode(PHP_EOL, $env) . PHP_EOL);

$msg = urlencode("Admin password changed successfully.");
header("Location: admin-index.php?msg=$msg");
exit;