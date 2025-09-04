<?php
session_start();
require 'includes/db.php';

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
  $file = $_FILES['profile_picture'];

  // Validate upload errors
  if ($file['error'] !== UPLOAD_ERR_OK) {
    die('Upload error.');
  }

  // Validate file type (only allow images)
  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
    die('Invalid file type. Only JPG, PNG, GIF allowed.');
  }

  // Limit file size (example: 2MB max)
  if ($file['size'] > 2 * 1024 * 1024) {
    die('File too large. Max 2MB.');
  }

  // Generate unique file name
  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;

  $uploadDir = 'uploads/';
  $uploadPath = $uploadDir . $newFileName;

  // Move uploaded file
  if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    die('Failed to save uploaded file.');
  }

  // Update database with new profile picture filename
  $stmt = $db->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
  $stmt->execute([$newFileName, $userId]);

  // Redirect back to profile
  header('Location: profile.php');
  exit();
} else {
  header('Location: profile.php');
  exit();
}
