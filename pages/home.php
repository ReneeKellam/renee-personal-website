<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../config/styles.css">
    <title>Home - Renee Kellam</title>
    <style> .home-nav { background-color: #ddd; } </style>
</head>

<?php include "../config/header.php"; ?>
<?php include __DIR__ . '/../config/database.php'; ?>

<body>
    <div class='page-content'>

        <div class="card">
            <h1 class="centered">Welcome to My Website!</h1>
            <p class="centered">Hello! I'm Renee Kellam, a passionate developer and tech enthusiast. This website serves as a portfolio to showcase my projects, skills, and experiences. Feel free to explore the various sections to learn more about me and my work. If you have any questions or would like to get in touch, don't hesitate to reach out through the contact links at the bottom of the page. Thank you for visiting!</p>
        </div>

        <div class="card">
            <h2 class="centered">Latest Projects</h2>
            <p class="centered">Check out my latest project that demonstrates my skills in web development, programming, and engineering.</p>
            <?php
                $query = "SELECT * FROM `projects` ORDER BY `display-order` ASC, `date-of-project` DESC LIMIT 1";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                    echo '<div class="card" style="background-color: #fff;">';
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

        <div class="card">
            <h2 class="centered">Education</h2>
            <p class="centered">Bachelor's Degree in Aerospace Engineering from Texas A&M University with a Minor in Mathematics.</p>
        </div>

        <div class="card">
            <h2 class="centered">Skills & Expertise</h2>
            <p class="centered">I specialize in a variety of programming languages and technologies, including <code>PHP</code>, <code>Python</code>, <code>MatLab</code> and more. My expertise also extends to database management and responsive web design.</p>
            <div class="skill-gallery">
                <?php
                    $query = "SELECT * FROM `Skills` ORDER BY `skill-name` ASC";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($skills) === 0) {
                        echo "<p class='centered'>No skills found.</p>";
                    } else {
                        foreach ($skills as $skill) { ?>
                            <div class='skill-card'>
                                <p><?php echo htmlspecialchars($skill['skill-name']); ?></p>
                            </div>
                <?php }
                    }
                ?>
            </div>
        </div>

        <div class="card">
            <h2 class="centered">Contact Me</h2>
            <p class="centered">Email: <a href="mailto:kellamrenee@gmail.com">kellamrenee@gmail.com</a></p>
            <p class="centered">LinkedIn: <a href="https://www.linkedin.com/in/ren%C3%A9e-kellam-ab107421b/" target="_blank" rel="noopener noreferrer">linkedin.com/in/renée-kellam-ab107421b/</a></p>
        </div>

    </div>
</body>
