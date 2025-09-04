<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Not logged in']);
  exit();
}

$userId = $_SESSION['user_id'];
$storyId = intval($_POST['story_id'] ?? 0);

if ($storyId <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid story']);
  exit();
}

$action = $_POST['action'] ?? 'add'; // add or remove

if ($action === 'add') {
  $stmt = $db->prepare("INSERT IGNORE INTO bookmarks (user_id, story_id) VALUES (?, ?)");
  $stmt->execute([$userId, $storyId]);
} else {
  $stmt = $db->prepare("DELETE FROM bookmarks WHERE user_id = ? AND story_id = ?");
  $stmt->execute([$userId, $storyId]);
}

echo json_encode(['success' => true]);
