<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $file = $_FILES['profile_picture'];
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type. Only JPG, PNG, GIF allowed.");
    }

    // Limit file size to 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        die("File too large. Max 2MB.");
    }

    // Create a unique filename
    $newName = 'profile_' . uniqid() . '.' . $ext;
    $uploadDir = 'uploads/profiles/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Update database
        $stmt = $db->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$destination, $user_id]);

        header("Location: profile.php");
        exit();
    } else {
        die("Failed to upload file.");
    }

} else {
    die("No file selected.");
}
?>
