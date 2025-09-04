<?php
session_start();
require 'includes/db.php';

// Check logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['friend_id'])) {
  $friendId = (int)$_POST['friend_id'];

  // Delete friend relation for this user
  $stmt = $db->prepare("DELETE FROM friends WHERE user_id = ? AND friend_id = ?");
  $stmt->execute([$userId, $friendId]);

  // Redirect back to profile
  header('Location: profile.php');
  exit();
} else {
  header('Location: profile.php');
  exit();
}
