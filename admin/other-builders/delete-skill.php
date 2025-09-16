<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

DELETING SKILL

<?php
    if (isset($_GET['id'])) {
        $skill_id = $_GET['id'];
        
        // Prepare and execute the delete query
        $query = "DELETE FROM `Skills` WHERE `id` = :skill_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':skill_id', $skill_id);
        
        if ($stmt->execute()) {
            $msg = "Skill deleted successfully.";
        } else {
            $msg = "Error deleting skill.";
        }
    } else {
        $msg = "No skill ID provided.";
    }

    // Redirect back to the other-builder page after deletion
    echo "<script>alert('$msg'); window.location.href = 'other-builder.php';</script>";
    exit();
?>