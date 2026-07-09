<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();
    require_once __DIR__ . '/../../config/library-search.php';

    $searchData = [
        "search" => urldecode(trim($_GET['search'] ?? '')), // Get the search term from the query string, if it exists, and trim any whitespace. If not provided, default to an empty string.
        "pg" => isset($_GET['pg']) && is_numeric($_GET['pg']) ? (int)$_GET['pg'] : 1, // Get the current page number from the query string, if it exists and is numeric. If not provided, default to page 1.
        "series" => isset($_GET['series']) ? urldecode(trim($_GET['series'])) : '', // Get the series filter from the query string, if it exists, and trim any whitespace. If not provided, default to an empty string.
        "lim" => isset($_GET['lim']) && is_numeric($_GET['lim']) ? (int)$_GET['lim'] : 25, // Default to 25 records per page if not specified
    ];

    $output = searchBooks($searchData); // Call the searchBooks function with the search data and get the results and whether to display the next button.
    if (isset($output['error'])) {
        modalDisplay($output['error']);
        $books = [];
        $paginationData = [
            'pg' => 1,
            'lim' => 25,
            'totalPages' => 1,
            'additionalParams' => []
        ];
    } else {
        $books = $output['books'];
        $paginationData = $output['paginationData']; // Get the pagination data from the output of the searchBooks function.
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

<?php include __DIR__ . '/../admin-header.php'; ?>

<div class="page-content">
    <h1 class="centered">Library Builder</h1>
    <a href="creator-library.php?id=new"><button class="centered">Add New Book</button></a>
    <br>
    <h2 class="centered">Existing Books</h2>
    <div class="card search" style="text-align:center; margin-bottom:20px;">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by title, author, series, or genre" value="<?php echo htmlspecialchars($searchData['search']); ?>">
            <input type="submit" value="Search">
        </form>

        <?php pagination($paginationData); // Call the pagination function to display pagination controls ?>
    </div>

    <div class="book-grid">
        <?php
            foreach ($books as $book) {      
                if (!isset($book['series_book_count']) || $book['series_book_count'] === 1) {
                    $coverImage = "/../../book-covers/" . htmlspecialchars($book['image'] ?? "no-image-found.png"); // Use a default image if none is provided
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
                    $frontCover = "/../../book-covers/" . htmlspecialchars($book['first_book_image'] ?? "no-image-found.png"); // Use a default image if none is provided
                    // If the book does not have a third book, the second one will be polaced at the very back to showcase it better
                    if (!empty($book['third_book_image'])) {
                        $secondCover = "/../../book-covers/" . (htmlspecialchars($book['second_book_image']) ?? "no-image-found.png");
                        $thirdCover = "/../../book-covers/" . (htmlspecialchars($book['third_book_image']) ?? "no-image-found.png");
                    } else {
                        $secondCover = null;
                        $thirdCover = "/../../book-covers/" . htmlspecialchars($book['second_book_image'] ?? "no-image-found.png");
                    }
                    // If the book is part of a series and not the only book, display it with a link to the series page.
        ?>
                    <a href="?series=<?php echo urlencode($book['series']); ?>">
                        <div class="book-card">
                            <div class="cover-stack">
                                <img class="book-cover first" src="<?php echo $frontCover; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" loading="lazy">
                                <?php if (isset($secondCover)) { ?>
                                    <img class="book-cover second" src="<?php echo $secondCover; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" loading="lazy">
                                <?php } ?>
                                <img class="book-cover third" src="<?php echo $thirdCover; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" loading="lazy">
                            </div>  
                            <h2><?php echo htmlspecialchars($book['series']); ?></h2>
                            <p><?php echo htmlspecialchars($book['author_first'] . ' ' . $book['author_last']); ?></p>
                            <p><strong>Books in Series:</strong> <?php echo htmlspecialchars($book['series_book_count']); ?></p>
                        </div>
                    </a>
                    <?php
                }
            }
        ?>
    </div>
</div>