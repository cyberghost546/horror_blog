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
$vote = intval($_POST['vote'] ?? 0); // 1 or -1

if ($storyId <= 0 || !in_array($vote, [1, -1])) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid input']);
  exit();
}

// Insert or update vote
$stmt = $db->prepare("
  INSERT INTO story_votes (user_id, story_id, vote) VALUES (?, ?, ?)
  ON DUPLICATE KEY UPDATE vote = VALUES(vote)
");
$stmt->execute([$userId, $storyId, $vote]);

// Get updated totals
$totalsStmt = $db->prepare("SELECT SUM(vote = 1) AS likes, SUM(vote = -1) AS dislikes FROM story_votes WHERE story_id = ?");
$totalsStmt->execute([$storyId]);
$totals = $totalsStmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
  'success' => true,
  'likes' => $totals['likes'] ?? 0,
  'dislikes' => $totals['dislikes'] ?? 0,
]);
