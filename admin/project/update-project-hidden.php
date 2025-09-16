<?php
include __DIR__ . '/../admin-checker.php';
include __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['hidden']) || !is_numeric($data['id'])) {
    http_response_code(400);
    echo "Invalid data.";
    exit;
}

$stmt = $pdo->prepare("UPDATE `projects` SET `hidden` = ? WHERE `id` = ?");
try {
    $stmt->execute([$data['hidden'], $data['id']]);
    echo "Project visibility updated!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>