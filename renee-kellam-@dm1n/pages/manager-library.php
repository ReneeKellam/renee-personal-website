<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();

    $search = urldecode(trim($_GET['search'] ?? '')); // Get the search term from the query string, if it exists, and trim any whitespace. If not provided, default to an empty string.
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page number from the query string, if it exists and is numeric. If not provided, default to page 1.
    $series = isset($_GET['series']) ? urldecode(trim($_GET['series'])) : ''; // Get the series filter from the query string, if it exists, and trim any whitespace. If not provided, default to an empty string.
    $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 25; // Default to 25 records per page if not specified

    $offset = max(0, ($page - 1) * $limit); // Ensure offset is not negative, since page numbers start at 1 need to subtract 1 from the page number to get the correct offset.

    if (!empty($series)) {
        $query = "SELECT * FROM `library` WHERE `series` LIKE :series ORDER BY `author_last`, `volume` LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':series', '%' . $series . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
        if (!empty($search)) { // If a search term is provided, bind it to the prepared statement, otherwise, skip this step.
            $stmt->bindValue(':search', $search);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);    
    }

    // For simple pagination, get the total count of books for the current search and if it isnt equal to the records per page, then display the next button
    $dispNextButton = count($books) === $limit ? true : false;
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

<?php include __DIR__ . '/../admin-header.php'; ?>

<div class="page-content">
    <h1 class="centered">Library Builder</h1>
    <a href="creator-library.php?id=new"><button class="centered">Add New Book</button></a>
    <br>
    <h2 class="centered">Existing Books</h2>
    <div class="card" style="text-align:center; margin-bottom:20px;">CSS:
        <?php
        // Previous page button
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '" style="margin-right:10px;"><button>&lt;</button></a>';
        } else {
            echo '<button disabled style="margin-right:10px;">&lt;</button>';
        }
        ?>
        

        <form class="pagination" method="GET" action="" style="display:inline;">
            <!-- hidden inputs preserve necessary states -->
            <input type="hidden" name="series" value="<?php echo htmlspecialchars($series); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="page" value="1"> <!-- on limit change -->
            <select name="limit" onchange="this.form.submit()">
                <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
            </select>
        </form>

        <?php
        if ($dispNextButton) {
            echo '<a href="?page=' . ($page + 1) . '"><button>&gt;</button></a>';
        } else {
            echo '<button disabled>&gt;</button>';
        }
        ?>
    </div>

    <div class="book-grid">
        <?php
            foreach ($books as $book) {
                $coverImage = "/../../book-covers/" . htmlspecialchars($book['image'] ?? "no-image-found.png"); // Use a default image if none is provided
                if (!isset($book['series_book_count']) || $book['series_book_count'] === 1) {
                    // If the book is not part of a series or is the only book in its series, display it normally.
        ?>
                    <a href="creator-library.php?id=<?php echo $book['id']; ?>">
                        <div class="book-card">
                            <img class="book-cover" src="<?php echo $coverImage; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" loading="lazy">
                            <h2><?php echo $book['title']; ?></h2>
                            <p><?php echo htmlspecialchars($book['author_first'] . ' ' . $book['author_last']); ?></p>
                            <p><strong>Date Updated:</strong> <?php echo htmlspecialchars($book['date_updated']); ?></p>
                        </div>
                    </a>
        <?php
                } else {
                    // If the book is part of a series and not the only book, display it with a link to the series page.
        ?>
                    <a href="manager-library.php?series=<?php echo urlencode($book['series']); ?>">
                        <div class="book-card">
                            <img class="book-cover series" src="<?php echo $coverImage; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" loading="lazy">
                            <h2><?php echo htmlspecialchars($book['series']); ?></h2>
                            <p><?php echo htmlspecialchars($book['author_first'] . ' ' . $book['author_last']); ?></p>
                            <p><strong>Date Updated:</strong> <?php echo htmlspecialchars($book['date_updated']); ?></p>
                        </div>
                    </a>
                    <?php
                }
            }
        ?>
    </div>
</div>

<style>
    .book-cover {
        max-height: 300px;
        object-fit: contain;
        border-radius: 4px; 
        margin: 15px;
    }
    .series {
        box-shadow: 7px -7px rgba(0, 0, 0, 0.6), 14px -14px rgba(0, 0, 0, 0.3); /* Add a shadow effect to series book covers */
    }
    
    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
        padding: 10px 0;
        justify-content: center;
    }
    .book-card {
        width: 250px;
        height: auto;
        margin: 5px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
</style>