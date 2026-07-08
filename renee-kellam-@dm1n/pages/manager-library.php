<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();

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
    <div class="card" style="text-align:center; margin-bottom:20px;">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by title, author, series, or genre" value="<?php echo htmlspecialchars($searchData['search']); ?>">
            <input type="submit" value="Search">
        </form>

        <?php pagination($paginationData); // Call the pagination function to display pagination controls ?>
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
                            <p><strong>Books in Series:</strong> <?php echo htmlspecialchars($book['series_book_count']); ?></p>
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