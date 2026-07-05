<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        header('Location: /home');
        exit;
    }
?>
