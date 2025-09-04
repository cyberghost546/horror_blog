<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: log_in.php");
  exit;
}

$story_id = (int) $_POST['story_id'];
$rating = (int) $_POST['rating'];
$comment = trim($_POST['comment']);
$user_id = $_SESSION['user_id'];

if ($rating < 1 || $rating > 5) {
  die("Invalid rating.");
}

$stmt = $db->prepare("INSERT INTO story_feedback (story_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->execute([$story_id, $user_id, $rating, $comment]);

header("Location: story.php?slug=" . $_GET['slug']);
exit;
