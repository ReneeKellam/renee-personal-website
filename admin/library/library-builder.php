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
    <style> .library-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<div class="page-content">
    <h1 class="centered">Library Builder</h1>
    <a href="new-book.php"><button class="centered">Add New Book</button></a>
    <br>
    <h2 class="centered">Existing Books</h2>
    <?php
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $recordsPerPage = 25;
        $offset = ($page - 1) * $recordsPerPage;
    ?>
    <div class="card" style="text-align:center; margin-bottom:20px;">
        <?php
        // Previous page button
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '" style="margin-right:10px;"><button>&lt;</button></a>';
        } else {
            echo '<button disabled style="margin-right:10px;">&lt;</button>';
        }

        // Next page button
        // You may want to check if there are more records before showing the next button
        $nextPage = $page + 1;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `library` WHERE `hidden` = 0");
        $stmt->execute();
        $totalBooks = $stmt->fetchColumn();
        $totalPages = ceil($totalBooks / $recordsPerPage);

        if ($page < $totalPages) {
            echo '<a href="?page=' . $nextPage . '"><button>&gt;</button></a>';
        } else {
            echo '<button disabled>&gt;</button>';
        }
        ?>
    </div>

    <div class="book-grid">
        <?php
            $query = "SELECT * FROM `library` ORDER BY `date-added` DESC  LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($books) === 0) {
                $books = [];
            }

            foreach ($books as $book) {
                echo '<div class="book-card">';
                if ($book['Image']) {
                    echo '<img src="/' . htmlspecialchars($book['Image']) . '" alt="' . htmlspecialchars($book['Title']) . ' cover" class="book-cover" loading="lazy">';
                } else {
                    echo '<div class="book-cover-placeholder">No Image</div>';
                }
                echo '<h2>' . $book['Title'] . '</h2>';
                echo '<p>' . $book['Author'] . '</p>';
                echo '<p>' . $book['Series'] . " " . ($book['Volume'] ? ' Book ' . $book['Volume'] : '') . '</p>';
                echo '<p><strong>Genre:</strong> ' . $book['Genre'] . '</p>';
                echo '<p><strong>Status:</strong> ' . $book['Status'] . '</p>';
                if ($book['Rating']) {
                    echo '<p><strong>Rating:</strong> ' . $book['Rating'] . '/5</p>';
                }
                echo '<p><strong>Date Added:</strong> ' . $book['Date-Added'] . '</p>';
                echo '<a href="edit-book.php?id=' . $book['Book-ID'] . '"><button>Edit</button></a> ';
                echo '<form class="hide-book-form" style="display:inline;">
                            <label for="hide-'.$book['Book-ID'].'">Hidden</label>
                            <input type="checkbox" name="hidden" value="1" '.($book['hidden'] == 1 ? 'checked' : '').' data-id="'.$book['Book-ID'].'" id="hide-'.$book['Book-ID'].'" style="margin-right:5px;">
                        </form>';
                
                echo '</div>';
            }

        ?>
    </div>
</div>

<script>
document.querySelectorAll('.hide-book-form input[type="checkbox"]').forEach(box => {
    box.addEventListener('change', function() {
        fetch('update-book-hidden.php', {
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
</script>
