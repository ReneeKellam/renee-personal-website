<?php
// Seperate library search configuration into its own file so that it does not have to be loaded on every page, only when needed. 
// This can help improve performance and reduce unnecessary overhead on pages that do not require library search functionality.

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