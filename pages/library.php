<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../config/styles.css">
    <title>Library - Renee Kellam</title>
    <style> .library-nav { background-color: #ddd; } </style>
</head>

<?php include "../config/header.php"; ?>
<?php include __DIR__ . '/../config/database.php'; ?>

<body>
    <div class="page-content">
        <div class="card">
            <h1 class="centered">Renée's Library</h1>
            <p class="centered">Welcome to the library! Here you will find some of the myriad of books and papers I have read and have shaped the person I have become.</p>
        </div>
        <?php
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;
            $recordsPerPage = 25;
            $offset = ($page - 1) * $recordsPerPage;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            // Build the SQL query with search functionality
            $where = "`hidden` = 0";
            $params = [];

            if ($search !== '') {
                $words = preg_split('/\s+/', $search);
                foreach ($words as $i => $word) {
                    $where .= " AND (
                        `title` LIKE :word{$i}1
                        OR `author` LIKE :word{$i}2
                        OR `genre` LIKE :word{$i}3
                        OR `series` LIKE :word{$i}4
                    )";
                    $params[":word{$i}1"] = '%' . $word . '%';
                    $params[":word{$i}2"] = '%' . $word . '%';
                    $params[":word{$i}3"] = '%' . $word . '%';
                    $params[":word{$i}4"] = '%' . $word . '%';
                }
            }
        ?>

        <div class="card" style="text-align:center; margin-bottom:20px;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div id="search-form">
                    <form method="get" action="">
                        <label for="search">Search:</label>
                        <input type="text" id="search" name="search" placeholder="Search by title, author, genre..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Go</button>
                    </form>
                </div>
                <div id="reset-form">
                    <form method="get" action="">
                        <button type="submit">Reset</button>
                    </form>
                </div>
                <div id="pagination-controls" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <?php
                        // Previous page button
                        if ($page > 1) {
                            echo '<a href="?page=' . ($page - 1) . '" style="margin-right:10px;"><button>&lt;</button></a>';
                        } else {
                            echo '<button disabled style="margin-right:10px;">&lt;</button>';
                        }

                        // Check page count and display
                        $nextPage = $page + 1;
                        $count = $pdo->prepare("SELECT COUNT(*) FROM `library` WHERE $where");
                        foreach ($params as $key => $value) {
                            $count->bindValue($key, $value);
                        }
                        $count->execute();

                        $totalBooks = $count->fetchColumn();
                        $totalPages = ceil($totalBooks / $recordsPerPage);

                        echo '<span>' . $page . ' / ' . $totalPages . '</span>';

                        // Next page button
                        if ($page < $totalPages) {
                            echo '<a href="?page=' . $nextPage . '"><button style="margin-left:10px;">&gt;</button></a>';
                        } else {
                            echo '<button disabled style="margin-left:10px;">&gt;</button>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="book-grid">
            <?php
                $query = "SELECT * FROM `library` WHERE $where ORDER BY `author_last` ASC, `series` ASC, `volume` ASC LIMIT :limit OFFSET :offset";
                $stmt = $pdo->prepare($query);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($books) === 0) {
                    $books = [];
                }

                foreach ($books as $book) {
                    echo '<div class="book-card">';
                    if ($book['image']) {
                        echo '<img src="/../book-covers/' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . ' cover" class="book-cover" loading="lazy">';
                    } else {
                        echo '<div class="book-cover-placeholder">No Image</div>';
                    }
                    echo '<h2>' . $book['title'] . '</h2>';
                    echo '<p>' . $book['authors'] . '</p>';
                    if ($book['series'] || $book['volume']) {
                        echo '<p>' . $book['series'] . " " . ($book['volume'] ? ' Book ' . $book['volume'] : '') . '</p>';
                    }
                    if ($book['genre']) {
                        echo '<p><strong>Genre:</strong> ' . $book['genre'] . '</p>';
                    }
                    echo '<p><strong>Status:</strong> ' . $book['status'] . '</p>';
                    echo '</div>';
                }

            ?>
        </div>
    </div>
    <div class="card centered">
        <?php
            // Previous page button
            if ($page > 1) {
                echo '<a href="?page=' . ($page - 1) . '" style="margin-right:10px;"><button>&lt;</button></a>';
            } else {
                echo '<button disabled style="margin-right:10px;">&lt;</button>';
            }

            // Check page count and display
            $nextPage = $page + 1;
            $count = $pdo->prepare("SELECT COUNT(*) FROM `library` WHERE $where");
            foreach ($params as $key => $value) {
                $count->bindValue($key, $value);
            }
            $count->execute();

            $totalBooks = $count->fetchColumn();
            $totalPages = ceil($totalBooks / $recordsPerPage);

            echo '<span>' . $page . ' / ' . $totalPages . '</span>';

            // Next page button
            if ($page < $totalPages) {
                echo '<a href="?page=' . $nextPage . '"><button style="margin-left:10px;">&gt;</button></a>';
            } else {
                echo '<button disabled style="margin-left:10px;">&gt;</button>';
            }
        ?>
    </div>
</body>
</html>