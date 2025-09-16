<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="/config/styles.css">
    <link rel="icon" href="/assets/small icon.jpg" type="image/jpg">
    <link rel="stylesheet" href="/../config/styles.css">
    <style> .project-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<?php
    $projectId = $_GET['id'] ?? null;
    if ($projectId === null || !is_numeric($projectId)) {
        echo "<p class='centered'>Invalid project ID.</p>";
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `id` = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$project) {
        echo "<p class='centered'>Project not found.</p>";
        exit;
    }

    $projectName = $project['name'] ?? '';
    $projectDate = $project['date-of-project'] ?? '';
    $projectLink = $project['link'] ?? '';
    $projectLinkText = $project['link-text'] ?? '';
    $projectSuperDesc = $project['super-description'] ?? '';
    $projectDesc = $project['description'] ?? '';
    $projectSkills = $project['skills'] ?? '';
    $projectId = $project['id'];
?>

<form action="update-project.php" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
    <label for="project-name">Project Name*:</label>
    <input style="width: 100%;" type="text" id="project-name" name="project-name" value="<?php echo htmlspecialchars($projectName); ?>" required>

    <label for="project-super">Project Super Description:</label>
    <input style="width: 100%;" type="text" id="project-super" name="project-super" value="<?php echo htmlspecialchars($projectSuperDesc); ?>">

    <label for="project-description">Project Description*:</label>
    <input style="width: 100%;" type="text" id="project-description" name="project-description" value="<?php echo htmlspecialchars($projectDesc); ?>" required>

    <label for="project-link">Project Link:</label>
    <input style="width: 100%;" type="text" id="project-link" name="project-link" value="<?php echo htmlspecialchars($projectLink); ?>">

    <label for="project-link-text">Project Link Text:</label>
    <input style="width: 100%;" type="text" id="project-link-text" name="project-link-text" value="<?php echo htmlspecialchars($projectLinkText); ?>">

    <label for="project-date">Project Date*:</label>
    <input style="width: 100%;" type="text" id="project-date" name="project-date" value="<?php echo htmlspecialchars($projectDate); ?>" required>

    <label for="project-skills">Skills Used*:</label>
    <textarea style="width: 100%;" id="project-skills" name="project-skills" required><?php echo htmlspecialchars($projectSkills); ?></textarea>

    <input type="hidden" name="project-id" value="<?php echo htmlspecialchars($projectId); ?>">
    <button type="submit">Update Project</button>
</form>