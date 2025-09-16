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
    <style> .resume-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<h1 class="centered">Job Creator</h1>

<form action="create-job.php" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
    <label for="employer">Employer:</label>
    <input style="width: 100%;" type="text" id="employer" name="employer" required>
    <br>
    <label for="job_title">Job Title:</label>
    <input style="width: 100%;" type="text" id="job_title" name="job_title" required>
    <br>
    <label for="start_date">Start Date:</label>
    <input style="width: 100%;" type="text" id="start_date" name="start_date" required>
    <br>
    <label for="end_date">End Date:</label>
    <input style="width: 100%;" type="text" id="end_date" name="end_date">
    <br>
    <label for="job_description">Job Description:</label>
    <textarea style="width: 100%;" id="job_description" name="job_description" required></textarea>
    <br>
    <label for="skills_used">Skills Used:</label>
    <textarea style="width: 100%;" id="skills_used" name="skills_used" required></textarea>
    <br>
    <button type="submit">Create Job</button>
</form>
