<?php
include __DIR__ . '/../admin-checker.php';
include __DIR__ . '/../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    http_response_code(400);
    echo "Invalid data.";
    exit;
}

?>
<?php

try {
    $stmt = $pdo->prepare("UPDATE `jobs-resume` SET `display-order` = ? WHERE `Job ID` = ?");
    foreach ($data as $item) {
        if (!isset($item['order']) || !isset($item['id']) || !is_numeric($item['id']) || $item['id'] === '') continue;
        $stmt->execute([$item['order'], $item['id']]);
    }
    echo "Job order saved!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
