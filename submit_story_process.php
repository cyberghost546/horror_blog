<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

    // Summary (first 200 characters without HTML)
    $summary = substr(strip_tags($content), 0, 200);

    $category = $_POST['category'] ?? '';

    $stmt = $db->prepare("INSERT INTO stories (title, content, author, image, slug, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $author, $image, $slug, $category]);


    // Image upload handling
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $imageName = uniqid('story_', true) . '.' . $ext;

        move_uploaded_file($imageTmp, __DIR__ . "/uploads/$imageName");
    }

    // Insert story
    $stmt = $db->prepare("INSERT INTO stories (user_id, title, content, slug, summary, image, category, created_at, views, likes, dislikes) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0, 0, 0)");

    $success = $stmt->execute([
        $user_id,
        $title,
        $content,
        $slug,
        $summary,
        $imageName,
        $category
    ]);

    if ($success) {
        header("Location: story.php?slug=$slug");
        exit();
    } else {
        echo "Failed to submit story. Please try again.";
    }
} else {
    echo "Invalid request.";
}
