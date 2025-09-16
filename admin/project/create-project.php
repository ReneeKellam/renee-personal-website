<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['project-name']);
        $super_description = trim($_POST['project-super']);
        $description = trim($_POST['project-description']);
        $link = trim($_POST['project-link']);
        $link_text = trim($_POST['project-link-text']);
        $date_of_project = trim($_POST['project-date']);
        $skills = trim($_POST['project-skills']);

        if (!empty($name) && !empty($description) && !empty($date_of_project) && !empty($skills)) {
            $query = "INSERT INTO `projects` (`name`, `super-description`, `description`, `link`, `link-text`, `date-of-project`, `skills`, `hidden`, `display-order`, `created`, `modified`) 
                      VALUES (:name, :super_description, :description, :link, :link_text, :date_of_project, :skills, 0, 99, NOW(), NOW())";
            try {
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':super_description', $super_description);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':link', $link);
                $stmt->bindParam(':link_text', $link_text);
                $stmt->bindParam(':date_of_project', $date_of_project);
                $stmt->bindParam(':skills', $skills);
                $stmt->execute();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }

            echo "<script>alert('Project created successfully!'); window.location.href = 'project-builder.php';</script>";
            exit();
        } else {
            echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
            exit();
        }
    } else {
        header("Location: new-project.php");
        exit();
    }