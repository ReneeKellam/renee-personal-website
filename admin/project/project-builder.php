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

<div class="page-content">
    <h1 class="centered">Project Builder</h1>
    <a href="new-project.php"><button class="centered">Add New Project</button></a>
    <br>
    <button onclick="saveOrder()" class="centered">Save Project Order</button>
    <br>
    <h2 class="centered">Existing Projects</h2>

    <div class="project-list">
            <?php
                $query = "SELECT * FROM `projects` ORDER BY `display-order` ASC, `date-of-project` DESC";
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

                    echo '<div class="card project-card" draggable="true" data-id="' . $project['id'] . '" data-order="' . $project['display-order'] . '">';
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
                    echo "<a href='edit-project.php?id=" . $project['id'] . "'><button>Edit</button></a> ";
                    echo '<form class="hide-project-form" style="display:inline;">
                            <label for="hide-'.$project['id'].'">Hidden</label>
                            <input type="checkbox" name="hidden" value="1" '.($project['hidden'] == 1 ? 'checked' : '').' data-id="'.$project['id'].'" id="hide-'.$project['id'].'" style="margin-right:5px;">
                        </form>';
                    echo '</div>';
                }
            ?>

        </div>
</div>

<script>
document.querySelectorAll('.hide-project-form input[type="checkbox"]').forEach(box => {
    box.addEventListener('change', function() {
        fetch('update-project-hidden.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: this.getAttribute('data-id'),
                hidden: this.checked ? 1 : 0
            })
        })
        .then(res => res.text())
        .then(msg => alert(msg));
    });
});

const projectList = document.querySelector('.project-list');
let dragged;

document.querySelectorAll('.project-card').forEach(card => {
    card.addEventListener('dragstart', function(e) {
        dragged = this;
        e.dataTransfer.effectAllowed = 'move';
        this.classList.add('dragging');
    });
    card.addEventListener('dragend', function() {
        this.classList.remove('dragging');
    });
});

projectList.addEventListener('dragover', function(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(projectList, e.clientY);
    if (afterElement == null) {
        projectList.appendChild(dragged);
    } else {
        projectList.insertBefore(dragged, afterElement);
    }
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.project-card:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function getOrder() {
    return Array.from(document.querySelectorAll('.project-card')).map((card, idx) => ({
        id: card.getAttribute('data-id'),
        order: idx + 1
    }));
}

function saveOrder() {
    fetch('save-project-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(getOrder())
    })
    .then(res => res.text())
    .then(msg => alert(msg));
}
</script>




