<?php
session_start();
include 'track_visit.php';
require 'includes/db.php'; // Make sure this file connects to your database
global $db;

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($title) || empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Title and content are required.']);
        exit();
    }

    try {
        // Insert into database
        $stmt = $db->prepare("INSERT INTO stories (title, content, category, author_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $category, $author_id]);

        echo json_encode(['status' => 'success', 'message' => 'Story added successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
