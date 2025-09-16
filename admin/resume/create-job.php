<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

Creating Job Entry

<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer = $_POST['employer'];
    $job_title = $_POST['job_title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?: null; // Allow null for current jobs
    $job_description = $_POST['job_description'];
    $skills_used = $_POST['skills_used'];

    $stmt = $pdo->prepare("INSERT INTO `jobs-resume` (`Employer`, `Title`, `Start-Date`, `End-Date`, `Description`, `Skills`, `Display-Order`, `hidden`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$employer, $job_title, $start_date, $end_date, $job_description, $skills_used, 99, 0]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    echo "<script>alert('Job created successfully!'); window.location.href = 'resume-builder.php';</script>";
}

