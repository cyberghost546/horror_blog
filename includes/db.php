<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=silent_evidence;", "root", "");
    $query = $db->prepare("SELECT * FROM users");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
