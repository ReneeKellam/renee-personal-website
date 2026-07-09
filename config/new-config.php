<?php
// General configuration settings for the application that are required for the application to function properly.

// start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// error reporting settings
$errorLogPath = __DIR__ . '/../renee-kellam-@dm1n/error.log';
ini_set('log_errors', 1);
ini_set('error_log', $errorLogPath);
ini_set('display_errors', 0);

// 404 error handling
$backtrace = debug_backtrace();
if (isset($backtrace[0]['file']) && strpos($backtrace[0]['file'], 'error404.php') !== false) {
    if (!headers_sent()) {
        http_response_code(404);
    }
}

// Load environment variables from .env file
$secretsDir = getenv('SECRETS_DIR') ?: ($_SERVER['SECRETS_DIR'] ?? ($_ENV['SECRETS_DIR'] ?? null));
$envPath = $secretsDir ? rtrim($secretsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env' : __DIR__ . '/../.env';

if (file_exists($envPath) && is_readable($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^".*"$/', $value) || preg_match("/^'.*'$/", $value)) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

} else {
    throw new Exception('.env not found or unreadable: ' . $envPath);
}

function env(string $key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// Database connection settings
$dbHost = env('DB_HOST', 'localhost');
$dbUser = env('DB_USER', 'root');
$dbPass = env('DB_PASS', '');
$dbName = env('DB_NAME', 'test');

$charset = 'utf8mb4';

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$options = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// simple function to check if an admin is logged in
function adminChecker() {
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        header('Location: /home');
        exit;
    }
}

function logoutAdmin() {
    session_unset();
    session_destroy();
    header('Location: /home');
    exit;
}

function modalDisplay(string $message) {
    echo '<div class="modal">';
    echo '<div class="modal-content">';
    echo '<p>' . htmlspecialchars($message) . '</p>';
    echo '<button class="close-button" onclick="this.parentElement.parentElement.style.display=\'none\';">Close</button>';
    echo '</div>';
    echo '</div>';
}

// Pagination function to generate dynamic pagination controls
function pagination(array $paginationData, int $defaultLimit = 25, array $limits = [25, 50, 100]): void {
    $pg = max(1, ((int)$paginationData['pg'] ?? 1)); // Ensure page number is at least 1
    $lim = (int)$paginationData['lim'] ?? $defaultLimit; // Ensure limit is an integer
    $totalPages = (int)$paginationData['totalPages'] ?? 1; // Ensure total pages is an integer, default to 1 if not provided
    $additionalParams = $paginationData['additionalParams'] ?? []; // Additional parameters to preserve in the query string

    echo '<div class="pagination-card">';
    echo '<form class="pagination" method="GET" action="">'; // Use GET method to preserve query parameters in the URL & prevent any refresh issues
    // Preserve additional parameters as hidden inputs
    foreach ($additionalParams as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '<button type="submit" name="pg" id="pg" value="' . htmlspecialchars($pg-1) . '" ' . ($pg === 1 ? 'disabled="disabled"' : '') . '> &lt; </button>'; // Previous page button, disable on page 1
    echo '<select name="lim" id="lim" onchange="this.form.submit()">'; // Limit selection dropdown, submit form on change, does not send page number, thus resetting to page 1
    foreach ($limits as $limit) {
        $selected = $lim === $limit ? 'selected' : '';
        echo '<option value="' . htmlspecialchars($limit) . '" ' . $selected . '>' . htmlspecialchars($limit) . '</option>';
    }
    echo '</select>';
    echo '<button type="submit" name="pg" id="pg" value="' . htmlspecialchars($pg+1) . '" ' . ($pg === $totalPages ? 'disabled="disabled"' : '') . '> &gt; </button>'; // Next page button, disable on last page
    echo '<span>' . $pg . ' / ' . $totalPages . '</span>'; // Display current page and total pages
    echo '</form>';
    echo '</div>';
}

// End of config file