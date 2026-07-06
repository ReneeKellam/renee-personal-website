<?php
    require_once __DIR__ . '/../../config/new-config.php';
    adminChecker();
    require_once __DIR__ . '/../api-calls/functions-library.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $bookId = (int)$_GET['id'];
        $bookId = "new";
    } else {
        $bookId = "new";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bookData = $_POST;
        $bookData['image'] = $_FILES['image'] ?? null;

        if ($bookId === "new") {
            $result = newBook($bookData);
        } else {
            $result = updateBook($bookData);
        }

        unset($_POST);

        if (isset($result['error'])) {
            $error_message = $result['error'];
        } else {
            header("Location: manager-library.php");
            exit();
        }
    }

    if ($bookId !== "new") {       
        $book = getBookById($bookId);
    }

    // ADJUSTMENT LOGIC
    if (!isset($book["error"])) {
        // adjust book data due to changes in database structure
        if (isset($book['author'])) {
            $authorParts = explode(' ', $book['author'], 2);
            $book['author-first'] = $authorParts[0];
            $book['author-last'] = isset($authorParts[1]) ? $authorParts[1] : '';
            $book["authors"] = $book['author'];
        }
    }

    // Error message display logic
    $error_message = "";
    if (isset($book['error'])) {
        $error_message .= $book['error'] . " <br>";
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

<style>
    .form-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: 75vw;
        margin: auto;
        font-size: 1.25em;
    }
</style>

<div class="page-content">
    <h1 class="centered">Add / Edit a New Book</h1>
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <input type="hidden" name="book-id" value="<?php echo htmlspecialchars($bookId); ?>">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo isset($book['title']) ? htmlspecialchars($book['title']) : ''; ?>" required>


        <label for="author">Primary Author: (If only one Name, use Last Name)</label>
        <div class="form-group">
            <input type="text" id="fname" name="author-first" value="<?php echo isset($book['author-first']) ? htmlspecialchars($book['author-first']) : ''; ?>" placeholder="First Name">
            <input type="text" id="lname" name="author-last" value="<?php echo isset($book['author-last']) ? htmlspecialchars($book['author-last']) : ''; ?>" placeholder="Last Name (if applicable)" required>
        </div>

        <label for="authors">Additional Authors (if applicable):</label>
        <input type="text" id="authors" name="authors" value="<?php echo isset($book['authors']) ? htmlspecialchars($book['authors']) : ''; ?>" placeholder="Separate multiple authors with commas">

        <label for="series">Series (if applicable):</label>
        <div class="form-group">
            <input type="text" id="series" name="series" value="<?php echo isset($book['series']) ? htmlspecialchars($book['series']) : ''; ?>" placeholder="Series Name">
            <input type="number" id="volume" name="volume" value="<?php echo isset($book['volume']) ? htmlspecialchars($book['volume']) : ''; ?>" placeholder="Volume #">
        </div>

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" value="<?php echo isset($book['genre']) ? htmlspecialchars($book['genre']) : ''; ?>">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Read" <?php echo isset($book['status']) && $book['status'] === 'Read' ? 'selected' : ''; ?>>Read</option>
            <option value="Reading" <?php echo isset($book['status']) && $book['status'] === 'Reading' ? 'selected' : ''; ?>>Reading</option>
            <option value="To Read" <?php echo isset($book['status']) && $book['status'] === 'To Read' ? 'selected' : ''; ?>>To Read</option>
            <option value="Abandoned" <?php echo isset($book['status']) && $book['status'] === 'Abandoned' ? 'selected' : ''; ?>>Abandoned</option>
        </select>

        <label for="type">Type:</label>
        <select id="type" name="type">
            <option value="Novel" <?php echo isset($book['type']) && $book['type'] === 'Novel' ? 'selected' : ''; ?>>Novel</option>
            <option value="Novella" <?php echo isset($book['type']) && $book['type'] === 'Novella' ? 'selected' : ''; ?>>Novella</option>
            <option value="Graphic Novel" <?php echo isset($book['type']) && $book['type'] === 'Graphic Novel' ? 'selected' : ''; ?>>Graphic Novel</option>
            <option value="Manga" <?php echo isset($book['type']) && $book['type'] === 'Manga' ? 'selected' : ''; ?>>Manga</option>
            <option value="Short Story" <?php echo isset($book['type']) && $book['type'] === 'Short Story' ? 'selected' : ''; ?>>Short Story</option>
            <option value="Scientific Article" <?php echo isset($book['type']) && $book['type'] === 'Scientific Article' ? 'selected' : ''; ?>>Scientific Article</option>
            <option value="Other" <?php echo isset($book['type']) && $book['type'] === 'Other' ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="image">Upload Image</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (isset($book['image']) && !empty($book['image'])): ?>
            <p>Current Image: </p>
            <img src="/../../book-covers/<?php echo htmlspecialchars($book['image']); ?>" alt="Book Cover" style="max-width: 100px;">
        <?php endif; ?>

        <button type="submit"><?php echo ($bookId === "new") ? "Add Book" : "Update Book"; ?></button>
</div>

<script>
    window.addEventListener('load', function () {
        const errorMessage = <?php echo json_encode($error_message); ?>;
        if (errorMessage) {
            alert(errorMessage);
        }
    });
</script>