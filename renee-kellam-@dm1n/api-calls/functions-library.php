<?php
// Get book information by ID
function getBookById(int $bookId): array {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM library WHERE id = :bookId");
        $stmt->execute(['bookId' => $bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($book)) {
            error_log("No book found with ID: " . $bookId);
            return ["error" => "No book found with ID: " . $bookId];
        }
        return $book;
    } catch (PDOException $e) {
        error_log("Error fetching book by ID: " . $e->getMessage());
        return ["error" => "Error fetching book by ID: " . $e->getMessage()];
    }
}

// Upload book cover image
function uploadBookCover(array $bookData): string {
    $targetDir = __DIR__ . '/../../book-covers/';
    $file = $bookData['image'] ?? null;
    $fileName = basename($file['name']);
    $newFileName = str_replace(' ', '_', $bookData['author-last'] . '_' . $bookData['title']) . '_' . date('Ymd_His') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    $targetFile = $targetDir . $newFileName;

    // Check if file is an image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return "error - file is not an image.";
    }

    // Prevent overwrite
    if (file_exists($targetFile)) {
        return "error - file already exists.";
    }

    // Move file
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        return "error - there was an error uploading your file.";
    }

    return $newFileName; // Return the new file name for database storage
}

function normalizeNullableInt($value): ?int {
    if ($value === null) {
        return null;
    }

    if (is_string($value) && trim($value) === '') {
        return null;
    }

    return (int) $value;
}

// Add a new book to the library
function newBook(array $bookData): array {
    global $pdo;

    $filename = uploadBookCover($bookData);

    if (strpos($filename, "error") === 0) {
        return ["error" => $filename];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO library (title, author_first, author_last, authors, series, volume, genre, status, image, hidden, type) VALUES (:title, :author_first, :author_last, :authors, :series, :volume, :genre, :status, :image, :hidden, :type)");
        $stmt->execute([
            'title' => $bookData['title'],
            'author_first' => $bookData['author-first'],
            'author_last' => $bookData['author-last'],
            'authors' => $bookData['authors'] ?? null,
            'series' => $bookData['series'] ?? null,
            'volume' => normalizeNullableInt($bookData['volume'] ?? null),
            'genre' => $bookData['genre'],
            'status' => $bookData['status'],
            'type' => $bookData['type'] ?? null,
            'image' => $filename ?? null,
            'hidden' => $bookData['hidden'] ?? 0
        ]);
        return ["success" => "Book added successfully."];
    } catch (PDOException $e) {
        error_log("Error adding new book: " . $e->getMessage());
        return ["error" => "Error adding new book: " . $e->getMessage()];
    }
}

// Update an existing book in the library
function updateBook(array $bookData): array {
    global $pdo;

    $bookId = $bookData['book-id'];
    $filename = null;

    if (isset($bookData['image']) && $bookData['image']['error'] === UPLOAD_ERR_OK) {
        $filename = uploadBookCover($bookData);
        if (strpos($filename, "error") === 0) {
            return ["error" => $filename];
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE library SET title = :title, author_first = :author_first, author_last = :author_last, authors = :authors, series = :series, volume = :volume, genre = :genre, status = :status, type = :type" . ($filename ? ", image = :image" : "") . " WHERE id = :bookId");
        $params = [
            'title' => $bookData['title'],
            'author_first' => $bookData['author-first'],
            'author_last' => $bookData['author-last'],
            'authors' => $bookData['authors'] ?? null,
            'series' => $bookData['series'] ?? null,
            'volume' => normalizeNullableInt($bookData['volume'] ?? null),
            'genre' => $bookData['genre'],
            'status' => $bookData['status'],
            'type' => $bookData['type'] ?? null,
            'bookId' => $bookId
        ];
        if ($filename) {
            $params['image'] = $filename;
        }
        $stmt->execute($params);
        return ["success" => "Book updated successfully."];
    } catch (PDOException $e) {
        error_log("Error updating book: " . $e->getMessage());
        return ["error" => "Error updating book: " . $e->getMessage()];
    }
}
?>