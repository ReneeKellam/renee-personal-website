<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        $name = trim($_POST['project-name']);
        $super_description = trim($_POST['project-super']);
        $description = trim($_POST['project-description']);
        $link = trim($_POST['project-link']);
        $link_text = trim($_POST['project-link-text']);
        $date_of_project = trim($_POST['project-date']);
        $skills = trim($_POST['project-skills']);
        $project_id = trim($_POST['project-id']);



        $stmt = $pdo->prepare("UPDATE `projects` SET `name` = ?, `super-description` = ?, `description` = ?, `link` = ?, `link-text` = ?, `date-of-project` = ?, `skills` = ?, `modified` = NOW() WHERE `id` = ?");
        try {
            $stmt->execute([$name, $super_description, $description, $link, $link_text, $date_of_project, $skills, $project_id]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
        echo "<script>alert('Project updated successfully!'); window.location.href = 'project-builder.php';</script>";
        exit();
    }
