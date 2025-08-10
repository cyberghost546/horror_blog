<?php
$host = "localhost";
$dbname = "horror_blog";
$username = "root"; // Change if needed
$password = ""; // Change if needed

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
