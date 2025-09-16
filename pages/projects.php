<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../config/styles.css">
    <title>Projects - Renee Kellam</title>
    <style> .projects-nav { background-color: #ddd; } </style>
</head>

<?php include "../config/header.php"; ?>
<?php include __DIR__ . '/../config/database.php'; ?>

<body>
    <div class="page-content">
        <div class="card">
            <h1 class="centered">Projects</h1>
            <p class="centered"> Welcome to my projects page! Here, you'll find a curated selection of my work that showcases my skills and expertise in various areas of software development and engineering. Each project reflects my commitment to quality, innovation, and problem-solving. Feel free to explore the details of each project, including the technologies used and the challenges overcome. I hope you find these projects as exciting and inspiring as I do!</p>
        </div>
        <div class="project-list">
            <?php
                $query = "SELECT * FROM `projects` WHERE `hidden` = 0 ORDER BY `display-order` ASC, `name` DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($projects) === 0) {
                    $projects = [];
                }

                foreach ($projects as $project) {

                    if (!isset($project['link-text']) || trim($project['link-text']) === '') {
                        $link_text = 'View Project';
                    } else {
                        $link_text = $project['link-text'];
                    }

                    if (!isset($project['link']) || trim($project['link']) === '') {
                        $info_display = $project['date-of-project'];
                    } else {
                        $info_display = "<a href='" . htmlspecialchars($project['link']) . "' target='_blank' rel='noopener noreferrer'>" . htmlspecialchars($link_text) . "</a> | " . htmlspecialchars($project['date-of-project']);
                    }

                    echo '<div class="card">';
                    echo '<table class="job-entry">';
                    echo '<tr>';
                    echo '<td><h2>'. htmlspecialchars($project['name']) .'</h2></td>';
                    echo '<td style="text-align: right;"><p>'. $info_display .'</p></td>';
                    echo '</tr>';
                    if (isset($project['super-description']) && trim($project['super-description']) !== '') {
                        echo '<tr><td colspan="2"><p>'. htmlspecialchars($project['super-description']) .'</p></td></tr>';
                    }
                    echo '<tr><td colspan="2"><p>'. $project['description'] .'</p></td></tr>';
                    echo '<tr><td colspan="2"><p><strong>Skills:</strong> '. htmlspecialchars($project['skills']) .'</p></td></tr>';
                    echo '</table>';
                    echo '</div>';
                }
            ?>

        </div>
    </div>