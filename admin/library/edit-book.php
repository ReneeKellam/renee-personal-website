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
    <h1 class="centered">Edit Book</h1>
    <?php
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo "<script>alert('Invalid book ID.'); window.location.href = 'library-builder.php';</script>";
            exit;
        }

        $book_id = (int)$_GET['id'];

        // Fetch existing book details
        $query = "SELECT * FROM `library` WHERE `Book-ID` = :book_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            echo "<script>alert('Book not found.'); window.location.href = 'library-builder.php';</script>";
            exit;
        }
    ?>

    <form action="update-book.php" method="POST" enctype="multipart/form-data" class="form-container" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
        <input type="hidden" name="book-id" value="<?php echo htmlspecialchars($book['Book-ID']); ?>">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['Title']); ?>" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['Author']); ?>" required>

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($book['Genre']); ?>" required>

        <label for="series">Series (optional):</label>
        <input type="text" id="series" name="series" value="<?php echo htmlspecialchars($book['Series']); ?>">

        <label for="book-number">Book Number in Series (optional):</label>
        <input type="number" id="book-number" name="book-number" min="1" value="<?php echo htmlspecialchars($book['Volume']); ?>">

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <?php
                $statuses = ['To Read', 'Reading', 'Read'];
                foreach ($statuses as $status) {
                    $selected = ($book['Status'] === $status) ? 'selected' : '';
                    echo "<option value='$status' $selected>$status</option>";
                }
            ?>
        </select>

        <label for="rating">Rating (1-5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($book['Rating']); ?>">

        <?php if ($book['Image']): ?>
            <p>Current Image:</p>
            <img src="/<?php echo htmlspecialchars($book['Image']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?> cover" style="max-width: 200px; max-height: 300px;">
        <?php else: ?>
            <p>No current image.</p>
        <?php endif; ?>

        <label for="image">Cover Image: (Leave blank to keep existing)</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Update Book</button>
    </form>
</div>
