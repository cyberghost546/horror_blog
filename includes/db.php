<!-- <?php
try {
    $db = new PDO("mysql:host=localhost;dbname=silent_evidence;", "root", "");
    $query = $db->prepare("SELECT * FROM users");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> -->


<?php
// ================================
// 1. /includes/db.php
// ================================
// Create folder: includes
// Update DB credentials to your local setup

$DB_HOST = getenv('localhost') ?: 'localhost';
$DB_NAME = getenv('silent_evidence') ?: 'silent_evidence';
$DB_USER = getenv('root') ?: 'root';
$DB_PASS = getenv('') ?: '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $db = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Throwable $e) {
    http_response_code(500);
    echo 'DB connection failed';
    exit;
}
?>