<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../config/styles.css">
    <title>Work Experience - Renee Kellam</title>
    <style> .about-nav { background-color: #ddd; } </style>
</head>

<?php include "../config/header.php"; ?>
<?php include __DIR__ . '/../config/database.php'; ?>

<body>
    <div class='page-content'>
        <div class="card">
            <h1 class="centered">Work Experience</h1>
            <p class="centered">Welcome to my work experience page! Here, you'll find a detailed overview of my professional journey, including the roles I've held, the skills I've acquired, and the projects I've contributed to. My career has been marked by a commitment to excellence and a passion for continuous learning. Feel free to explore my experiences and see how they have shaped my expertise in the field.</p>
        </div>
        <?php
            $query = "SELECT * FROM `jobs-resume` WHERE `hidden` = 0 ORDER BY `Display-Order` ASC, `Title` DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($jobs) === 0) {
                echo "<p class='centered'>No work experience found.</p>";
            } else { 
                foreach ($jobs as $job) { 
                    $end_date = $job['End-Date'] ? $job['End-Date'] : 'Current';?>
                    <div class='card'>
                        <table class='job-entry'>
                            <tr>
                                <td> <h2><?php echo htmlspecialchars($job['Title']) . " at " . htmlspecialchars($job['Employer']); ?></h2></td>
                                <td style="text-align: right;"> <p><?php echo htmlspecialchars($job['Start-Date']) . " to " . htmlspecialchars($end_date); ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="2"><p><?php echo nl2br(htmlspecialchars($job['Description'])); ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="2"><p><strong>Skills Used:</strong> <?php echo nl2br(htmlspecialchars($job['Skills'])); ?></p></td>
                            </tr>
                        </table>
                    </div>
                <?php }
            }
        ?>
    </div>
</body>