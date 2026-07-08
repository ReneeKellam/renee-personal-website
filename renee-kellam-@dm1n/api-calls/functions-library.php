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

// Search Functionality
function searchBooks(array $searchData): array {
    global $pdo;

    // Extract search parameters with defaults
    $search = $searchData['search'] ?? '';
    $series = $searchData['series'] ?? '';
    $pg = max(1, (int)($searchData['pg'] ?? 1)); // page number cannot be less than 1
    $lim = abs((int)($searchData['lim'] ?? 25)); // limit cannot be negative

    // Generate the offset
    $offset = ($pg - 1) * $lim;

    if (!empty($series) || !empty($search)) {
        // if any search term is specified, the page will display all matching books in the series rather than just the first book in the series. This is to allow users to see all books in a series when searching for a specific series name and ensures desired searches are not buried.
        try {
            // Search by series
            $params = [':limit' => $lim, ':offset' => $offset];
            $query = "SELECT *, COUNT(*) OVER () AS total_count FROM `library` WHERE 1=1"; // Base query to select all books
            if (!empty($series)) {
                $query .= " AND `series` LIKE :series";
                $params[':series'] = '%' . $series . '%';
            }
            if (!empty($search)) {
                $query .= " AND (MATCH(`title`, `authors`, `series`, `genre`) AGAINST(:search IN BOOLEAN MODE))"; // Full-text search on title, authors, series, and genre
                $params[':search'] = $search;
            }
            $query .= " ORDER BY `author_last`, `volume` LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching books by series: " . $e->getMessage());
            return ["error" => "Error searching books by series: " . $e->getMessage()];
        }
    } else {
        // If no search term is specified, display only the first book in each series and any standalone books. This is to prevent overwhelming the user with too many results and to provide a cleaner view of the library. (Blame Jim Butcher for having 18 Books in one amazing series)
        try {
            $query = "
                WITH grouped AS (
                    SELECT
                        CASE
                            WHEN l.series IS NULL OR l.series = '' THEN CONCAT('book:', l.id)
                            ELSE CONCAT('series:', l.series)
                        END AS group_key,
                        COUNT(*) AS series_book_count,
                        MAX(CASE WHEN l.volume = 1 THEN l.image END) AS first_book_image,
                        MAX(CASE WHEN l.volume = 2 THEN l.image END) AS second_book_image,
                        MAX(CASE WHEN l.volume = 3 THEN l.image END) AS third_book_image,
                        COALESCE(MAX(CASE WHEN l.volume = 1 THEN l.id END), MIN(l.id)) AS display_book_id
                    FROM library l
                    GROUP BY
                        CASE
                            WHEN l.series IS NULL OR l.series = '' THEN CONCAT('book:', l.id)
                            ELSE CONCAT('series:', l.series)
                        END
                ),
                cards AS (
                    SELECT
                        b.*,
                        g.series_book_count,
                        COALESCE(g.first_book_image, b.image) AS first_book_image,
                        g.second_book_image,
                        g.third_book_image
                    FROM grouped g
                    JOIN library b ON b.id = g.display_book_id
                )
                SELECT cards.*, COUNT(*) OVER () AS total_count
                FROM cards
                ORDER BY cards.author_last, cards.series, cards.date_updated, cards.id
                LIMIT :limit OFFSET :offset;
            ";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':limit', $lim, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching books: " . $e->getMessage());
            return ["error" => "Error fetching books: " . $e->getMessage()];
        }
    }

    $totalCount = !empty($books) ? (int)$books[0]['total_count'] : 0;
    $totalPages = $lim > 0 ? (int)ceil($totalCount / $lim) : 1;

    return [
        'books' => $books,
        'paginationData' => [
            'pg' => $pg,
            'lim' => $lim,
            'totalPages' => $totalPages ?? 1,
            'additionalParams' => [
                'search' => $search,
                'series' => $series,
            ],
        ],
    ];

}
?>