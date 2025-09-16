<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $status = $_POST['status'] ?? 'To Read';
    $rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? $_POST['rating'] : null;
    $series = $_POST['series'] ?? null;
    $book_number = isset($_POST['book-number']) && $_POST['book-number'] !== '' ? $_POST['book-number'] : null;


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

    // Insert into database
    $query = "INSERT INTO `library` (Title, Author, Genre, Status, Image, Rating, `Hidden`, `Date-Added`, `Last-Updated`, Series, `Volume`)
              VALUES (:title, :author, :genre, :status, :image, :rating, 0, NOW(), NOW(), :series, :volume)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':image', $imagePath);
    if ($rating === null) {
        $stmt->bindValue(':rating', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
    }
    $stmt->bindParam(':series', $series);
    if ($book_number === null) {
        $stmt->bindValue(':volume', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':volume', $book_number, PDO::PARAM_INT);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Book added successfully!'); window.location.href = 'library-builder.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error adding book.'); window.location.href = 'library-builder.php';</script>";
    }
}