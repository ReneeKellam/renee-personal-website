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

<?php 
    $jobId = $_GET['id'] ?? null;
    if ($jobId === null || !is_numeric($jobId)) {
        echo "<p class='centered'>Invalid job ID.</p>";
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM `jobs-resume` WHERE `Job ID` = ?");
    $stmt->execute([$jobId]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$job) {
        echo "<p class='centered'>Job not found.</p>";
        exit;
    }

    $jobTitle = $job['Title'] ?? '';
    $jobEmployer = $job['Employer'] ?? '';
    $jobStartDate = $job['Start-Date'] ?? '';
    $jobEndDate = $job['End-Date'] ?? '';
    $jobDescription = $job['Description'] ?? '';
    $jobSkills = $job['Skills'] ?? '';
    $jobId = $job['Job ID'];
?>
<h1 class="centered">Edit Job</h1>
<form action="update-job.php" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
    <label for="employer">Employer:</label>
    <input style="width: 100%;" type="text" id="employer" name="employer" value="<?php echo htmlspecialchars($jobEmployer); ?>" required>
    <br>
    <label for="job_title">Job Title:</label>
    <input style="width: 100%;" type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($jobTitle); ?>" required>
    <br>
    <label for="start_date">Start Date:</label>
    <input style="width: 100%;" type="text" id="start_date" name="start_date" value="<?php echo htmlspecialchars($jobStartDate); ?>" required>
    <br>
    <label for="end_date">End Date:</label>
    <input style="width: 100%;" type="text" id="end_date" name="end_date" value="<?php echo htmlspecialchars($jobEndDate); ?>">
    <br>
    <label for="job_description">Job Description:</label>
    <textarea style="width: 100%;" id="job_description" name="job_description" required><?php echo htmlspecialchars($jobDescription); ?></textarea>
    <br>
    <label for="skills_used">Skills Used:</label>
    <textarea style="width: 100%;" id="skills_used" name="skills_used" required><?php echo htmlspecialchars($jobSkills); ?></textarea>
    <br>
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($jobId); ?>">
    <button type="submit">Update Job</button>
</form>
