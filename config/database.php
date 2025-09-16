<?php
    include 'config.php';
    loadEnv();
    $dbHost = env('DB_HOST', 'localhost');
    $dbUser = env('DB_USER', 'root');
    $dbPass = env('DB_PASS', '');
    $dbName = env('DB_NAME', 'test');
    
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
    $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
?>
