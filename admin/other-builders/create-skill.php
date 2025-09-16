<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php 
    if (isset($_GET['skill-name'])) {
        $skill_name = trim($_GET['skill-name']);
            if (!empty($skill_name)) {
                $query = "INSERT INTO `Skills` (`skill-name`) VALUES (:skill_name)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':skill_name', $skill_name);
                $stmt->execute();
                $msg = "Skill '$skill_name' added successfully.";
            } else {
                $msg = "Skill name cannot be empty.";
            }
        } else {
        $msg = "No skill name provided.";
    }

    echo "<script>alert(" . json_encode($msg) . "); window.location.href = 'other-builder.php';</script>";
    exit();
?>