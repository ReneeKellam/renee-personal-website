<?php

// Change the admin password in the .env file
function changeAdminPassword(string $current, string $new, string $confirm) {
    loadEnv();
    $old = env('ADMIN_PASSWORD');

    if (!$current || !$new || !$confirm) {
        return "Error - All fields are required.";
    }

    if ($current !== $old) {
        return "Error - Current password is incorrect.";
    }

    if ($new !== $confirm) {
        return "Error - New passwords do not match.";
    }

    $password_hash = password_hash($new, PASSWORD_ARGON2ID);

    // Update .env file
    $envPath = realpath(__DIR__ . '/../.env');
    if (!$envPath || !is_writable($envPath)) {
        return "Error - Cannot write to .env file.";
    }

    $env = file($envPath, FILE_IGNORE_NEW_LINES);
    foreach ($env as &$line) {
        if (strpos($line, 'ADMIN_PASSWORD=') === 0) {
            $line = 'ADMIN_PASSWORD=' . $password_hash;
        }
    }
    file_put_contents($envPath, implode(PHP_EOL, $env) . PHP_EOL);

    return "Admin password changed successfully.";
}

// Login to admin panel by verifying the password against the hashed password in the .env file
function login(string $inputPassword) {
    loadEnv();
    $admin_password = env('ADMIN_PASSWORD', 'changeme');

    if (password_verify($inputPassword, $admin_password)) {
        $_SESSION['admin_authenticated'] = true;
        return true;
    } else {
        return false;
    }
}