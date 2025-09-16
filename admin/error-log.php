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
    <style> .error-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/adminheader.php'; ?>

<body>
    <div class="content">
        <h2>Recent Errors</h2>
        <ul>
            <?php
            $errors = file(__DIR__ . '/../admin/error.log');
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
