<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['job_id'] ?? null;
        $title = $_POST['job_title'] ?? null;
        $employer = $_POST['employer'] ?? null;
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $description = $_POST['job_description'] ?? null;
        $skills = $_POST['skills_used'] ?? null;
        if (!$id || !is_numeric($id) || !$title || !$employer || !$startDate || !$description || !$skills) {
            echo "All fields except End Date are required.";
            exit;
        }
        if ($id && is_numeric($id)) {
            $stmt = $pdo->prepare("UPDATE `jobs-resume` SET `Title` = ?, `Employer` = ?, `Start-Date` = ?, `End-Date` = ?, `Description` = ?, `Skills` = ? WHERE `Job ID` = ?");
            try {
                $stmt->execute([$title, $employer, $startDate, $endDate, $description, $skills, $id]);
                $msg = "Job updated successfully!";
            } catch (Exception $e) {
                $msg = "Error: " . $e->getMessage();
            }
        } else {
            $msg = "Invalid job ID.";
        }
    } else {
        $msg = "Invalid request method.";
    }
    echo "<script>alert(" . json_encode($msg) . "); window.location.href = 'resume-builder.php';</script>";