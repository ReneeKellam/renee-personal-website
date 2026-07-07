<?php

// Change the admin password
function changeAdminPassword(string $current, string $new, string $confirm) {
    global $pdo; // Use the global PDO instance for database operations

    $stmt = $pdo->prepare("SELECT `value` FROM `settings` WHERE `key` = 'ADMIN_PASSWORD'");
    $stmt->execute();
    $old = $stmt->fetchColumn();

    if (!$current || !$new || !$confirm) {
        return "Error - All fields are required.";
    }

    if (!password_verify($current, $old)) {
        return "Error - Current password is incorrect.";
    }

    if ($new !== $confirm) {
        return "Error - New passwords do not match.";
    }

    $password_hash = password_hash($new, PASSWORD_ARGON2ID);

    $stmt = $pdo->prepare("UPDATE `settings` SET `value` = :value1 WHERE `key` = 'ADMIN_PASSWORD'");
    $stmt->execute([
        ':value1' => $password_hash,
    ]);

    return "Admin password changed successfully.";
}

// Login to admin
function login(string $inputPassword) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT `value` FROM `settings` WHERE `key` = 'ADMIN_PASSWORD'");
    $stmt->execute();
    $admin_password = $stmt->fetchColumn();

    if (password_verify($inputPassword, $admin_password)) {
        $_SESSION['admin_authenticated'] = true;
        return true;
    } else {
        return "Password is incorrect.";
    }
}