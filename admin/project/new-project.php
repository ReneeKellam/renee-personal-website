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
    <link rel="stylesheet" href="../config/styles.css">
    <style> .project-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<form action="create-project.php" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
    <label for="project-name">Project Name*:</label>
    <input style="width: 100%;" type="text" id="project-name" name="project-name" required>

    <label for="project-super">Project Super Description:</label>
    <input style="width: 100%;" type="text" id="project-super" name="project-super">

    <label for="project-description">Project Description*:</label>
    <textarea style="width: 100%;" id="project-description" name="project-description" required></textarea>

    <label for="project-link">Project Link:</label>
    <input style="width: 100%;" type="text" id="project-link" name="project-link">

    <label for="project-link-text">Project Link Text:</label>
    <input style="width: 100%;" type="text" id="project-link-text" name="project-link-text">

    <label for="project-date">Project Date*:</label>
    <input style="width: 100%;" type="text" id="project-date" name="project-date" required>

    <label for="project-skills">Skills Used*:</label>
    <textarea style="width: 100%;" id="project-skills" name="project-skills" required></textarea>

    <button type="submit">Create Project</button>
</form>