<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();

    $search = trim($_GET['search']) ?? '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $series = isset($_GET['series']) ? trim($_GET['series']) : '';

    if (!empty($series)) {
        $query = "SELECT * FROM `library` WHERE `series` = :series ORDER BY `author_last`, `volume`";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':series', $series);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $query = "
            SELECT ranked.*
            FROM (
                SELECT
                    l.*,
                    COUNT(*) OVER (
                        PARTITION BY CASE
                            WHEN l.series IS NULL OR l.series = '' THEN l.id
                            ELSE l.series
                        END
                    ) AS series_book_count,
                    l.image AS first_book_image,
                    ROW_NUMBER() OVER (
                        PARTITION BY CASE
                            WHEN l.series IS NULL OR l.series = '' THEN l.id
                            ELSE l.series
                        END
                        ORDER BY
                            CASE WHEN l.volume = 1 THEN 0 ELSE 1 END ASC,
                            l.volume ASC,
                            l.author_last ASC,
                            l.series ASC,
                            l.date_updated ASC,
                            l.id ASC
                    ) AS rn
                FROM `library` l" . (!empty($search) ? " WHERE MATCH(l.title, l.authors, l.series, l.genre) AGAINST(:search IN BOOLEAN MODE)" : "") . "
            ) ranked
            WHERE ranked.series IS NULL OR ranked.series = '' OR ranked.rn = 1
            ORDER BY ranked.author_last ASC, ranked.series ASC, ranked.date_updated ASC, ranked.id ASC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', $search);
        }
        $recordsPerPage = 25;
        $offset = ($page - 1) * $recordsPerPage;
        $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
?>

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
    <a href="creator-library.php?id=new"><button class="centered">Add New Book</button></a>
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
        $countQuery = "
            SELECT COUNT(*)
            FROM (
                SELECT ROW_NUMBER() OVER (
                    PARTITION BY CASE
                        WHEN l.series IS NULL OR l.series = '' THEN l.id
                        ELSE l.series
                    END
                    ORDER BY
                        CASE WHEN l.volume = 1 THEN 0 ELSE 1 END ASC,
                        l.volume ASC,
                        l.author_last ASC,
                        l.series ASC,
                        l.date_updated ASC,
                        l.id ASC
                ) AS rn,
                l.series
                FROM `library` l
                WHERE l.`hidden` = 0
            ) ranked
            WHERE ranked.series IS NULL OR ranked.series = '' OR ranked.rn = 1
        ";
        $stmt = $pdo->prepare($countQuery);
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
            // Select books from the library ordered by author_last then series, if there are more than 1 books in the same series, grab only the first and a count of how many books are in that series, then display the series name with the count of books in that series

            $query = "
                SELECT ranked.*
                FROM (
                    SELECT
                        l.*,
                        COUNT(*) OVER (
                            PARTITION BY CASE
                                WHEN l.series IS NULL OR l.series = '' THEN l.id
                                ELSE l.series
                            END
                        ) AS series_book_count,
                        l.image AS first_book_image,
                        ROW_NUMBER() OVER (
                            PARTITION BY CASE
                                WHEN l.series IS NULL OR l.series = '' THEN l.id
                                ELSE l.series
                            END
                            ORDER BY
                                CASE WHEN l.volume = 1 THEN 0 ELSE 1 END ASC,
                                l.volume ASC,
                                l.author_last ASC,
                                l.series ASC,
                                l.date_updated ASC,
                                l.id ASC
                        ) AS rn
                    FROM `library` l
                ) ranked
                WHERE ranked.series IS NULL OR ranked.series = '' OR ranked.rn = 1
                ORDER BY ranked.author_last ASC, ranked.series ASC, ranked.date_updated ASC, ranked.id ASC
                LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($books) === 0) {
                $books = [];
            }

            foreach ($books as $book) {
        ?>
                <a href="creator-library.php?id=<?php echo $book['id']; ?>">
                    <div class="book-card">
                    
                        <?php if ($book['first_book_image']): ?>
                            <img src="/../../book-covers/<?php echo htmlspecialchars($book['first_book_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" class="book-cover" loading="lazy">
                        <?php else: ?>
                            <div class="book-cover-placeholder">No Image</div>
                        <?php endif; ?>
                        <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                        <p><?php echo htmlspecialchars($book['authors']); ?></p>
                        <p><?php echo htmlspecialchars($book['series']) . ($book['volume'] ? ' Book ' . htmlspecialchars($book['volume']) : ''); ?></p>
                        <p><strong>Books in Series:</strong> <?php echo isset($book['series_book_count']) ? (int)$book['series_book_count'] : 1; ?></p>
                        <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($book['status']); ?></p>
                        <p><strong>Date Updated:</strong> <?php echo htmlspecialchars($book['date_updated']); ?></p>
                    </div>
                </a>
        <?php
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
