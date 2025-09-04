<?php
session_start();
require 'includes/db.php';

// Check user logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['friend_username'])) {
  $friendUsername = trim($_POST['friend_username']);

  // Prevent adding self
  $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
  $stmt->execute([$friendUsername]);
  $friend = $stmt->fetch();

  if (!$friend) {
    die('User not found.');
  }

  if ($friend['id'] == $userId) {
    die("You can't add yourself.");
  }

  // Check if already friends
  $stmt = $db->prepare("SELECT * FROM friends WHERE user_id = ? AND friend_id = ?");
  $stmt->execute([$userId, $friend['id']]);
  if ($stmt->fetch()) {
    die('Already friends.');
  }

  // Insert friendship (assuming one-way friend list)
  $stmt = $db->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?)");
  $stmt->execute([$userId, $friend['id']]);

  // Redirect back to profile
  header('Location: profile.php');
  exit();
} else {
  header('Location: profile.php');
  exit();
}
