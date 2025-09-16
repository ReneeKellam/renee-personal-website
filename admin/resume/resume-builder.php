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

<h1 class="centered">Resume Builder</h1>

<a href="new-job.php"><button class="centered">Add New Job</button></a>
<br>
<button onclick="saveOrder()" class="centered">Save Job Order</button>

<h2 class="centered">Existing Jobs</h2>

<div class="page-content">
    <div class="job-list">
    <?php
        $query = "SELECT * FROM `jobs-resume` ORDER BY `Display-Order` ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($jobs) === 0) {
            echo "<p class='centered'>No jobs found.</p>";
        } else { 
            foreach ($jobs as $job) {
                $end_date = $job['End-Date'] ? $job['End-Date'] : 'Current'; ?>
                    <div class="card job-card" draggable="true" data-id="<?php echo $job['Job ID']; ?>" data-order="<?php echo $job['display-order']; ?>">
                        <table class="job-entry">
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
                <?php 
                echo "<a href='edit-job.php?id=" . $job['Job ID'] . "'><button>Edit</button></a> ";
                echo '<form class="hide-job-form" style="display:inline;">
                        <label for="hide-'.$job['Job ID'].'">Hidden</label>
                        <input type="checkbox" name="hidden" value="1" '.($job['hidden'] == 1 ? 'checked' : '').' data-id="'.$job['Job ID'].'" id="hide-'.$job['Job ID'].'" style="margin-right:5px;">
                    </form>';
                echo "</div>";
            }
        }
    ?>
    </div>
</div>
<script>
document.querySelectorAll('.hide-job-form input[type="checkbox"]').forEach(box => {
    box.addEventListener('change', function() {
        fetch('update-job-hidden.php', {
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

const jobList = document.querySelector('.job-list');
let dragged;

document.querySelectorAll('.job-card').forEach(card => {
    card.addEventListener('dragstart', function(e) {
        dragged = this;
        e.dataTransfer.effectAllowed = 'move';
        this.classList.add('dragging');
    });
    card.addEventListener('dragend', function() {
        this.classList.remove('dragging');
    });
});

jobList.addEventListener('dragover', function(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(jobList, e.clientY);
    if (afterElement == null) {
        jobList.appendChild(dragged);
    } else {
        jobList.insertBefore(dragged, afterElement);
    }
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.job-card:not(.dragging)')];
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
    return Array.from(document.querySelectorAll('.job-card')).map((card, idx) => ({
        id: card.getAttribute('data-id'),
        order: idx + 1
    }));
}

function saveOrder() {
    fetch('save-resume-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(getOrder())
    })
    .then(res => res.text())
    .then(msg => alert(msg));
}
</script>