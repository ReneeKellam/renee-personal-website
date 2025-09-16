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
    <style> .other-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<div class="page-content">
    <div class="card">
        <h2 class="centered">Skill Builder</h2>

        <form action="create-skill.php" method="GET" class="centered">
            <label for="skill-name">Skill Name:</label><br>
            <input type="text" id="skill-name" name="skill-name" required><br><br>
            <input type="submit" value="Add Skill">
        </form>

        <h3 class="centered">Existing Skills</h3>
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
                            <?php 
                            echo "<a href='delete-skill.php?id=" . $skill['id'] . "' onclick=\"return confirm('Are you sure you want to delete this skill?');\"><button>X</button></a>";
                            ?>
                        </div>
            <?php }
                }
            ?>
        </div>
    </div>
</div>
