<?php
include __DIR__ . '/admin-checker.php';
include __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = __DIR__ . '/../product-images/';
    $file = $_FILES['image'];
    $fileName = basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is an image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        $msg = urlencode("File is not an image.");
        header("Location: admin-index.php?msg=$msg");
        exit;
    }

    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowedTypes)) {
        $msg = urlencode("Sorry, only JPG, JPEG, PNG, GIF, and WEBP files are allowed.");
        header("Location: admin-index.php?msg=$msg");
        exit;
    }

    // Check file size (e.g., max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $msg = urlencode("Sorry, your file is too large.");
        header("Location: admin-index.php?msg=$msg");
        exit;
    }

    // Prevent overwrite
    if (file_exists($targetFile)) {
        $msg = urlencode("Sorry, file already exists.");
        header("Location: admin-index.php?msg=$msg");
        exit;
    }

    // Move file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $msg = urlencode("The file ". htmlspecialchars($fileName) . " has been uploaded.");
        header("Location: admin-index.php?msg=$msg");
        exit;
        // Optionally, redirect or add to database here
    } else {
        $msg = urlencode("Sorry, there was an error uploading your file.");
        header("Location: admin-index.php?msg=$msg");
        exit;
    }
}
?>