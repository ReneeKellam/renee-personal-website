<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book-id'] ?? '';
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $status = $_POST['status'] ?? 'To Read';
    $rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? $_POST['rating'] : null;
    $series = $_POST['series'] ?? null;
    $book_number = isset($_POST['book-number']) && $_POST['book-number'] !== '' ? $_POST['book-number'] : null;

    if ($book_id === '') {
        echo "<script>alert('Invalid Book ID.'); window.location.href = 'library-builder.php';</script>";
        exit;
    }

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../pages/book-covers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        // Store relative path for database
        $imagePath = 'pages/book-covers/' . basename($_FILES['image']['name']);
    }

    // Update database
    $query = "UPDATE `library` SET Title = :title, Author = :author, Genre = :genre, Status = :status, 
              Rating = :rating, `Last-Updated` = NOW(), Series = :series, `Volume` = :volume" . 
              ($imagePath ? ", Image = :image" : "") . 
              " WHERE `Book-ID` = :book_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':status', $status);
    if ($rating === null) {
        $stmt->bindValue(':rating', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    }
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->bindParam(':series', $series);
    if ($book_number === null) {
        $stmt->bindValue(':volume', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(':volume', $book_number, PDO::PARAM_INT);
    }
    if ($imagePath) {
        $stmt->bindParam(':image', $imagePath);
    }
    if ($stmt->execute()) {
        echo "<script>alert('Book updated successfully!'); window.location.href = 'library-builder.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating book.'); window.location.href = 'library-builder.php';</script>";
    }
}
?>