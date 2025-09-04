<?php
$host = 'localhost';
$dbname = 'silent_evidence';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Ensure the global variable is set
if (!isset($GLOBALS['db'])) {
    $GLOBALS['db'] = $db;
}   

if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("UPDATE user_sessions SET last_active = NOW() WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
