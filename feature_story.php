<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: log_in.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_id'])) {
    $storyId = intval($_POST['story_id']);

    // Un-feature all stories
    $db->query("UPDATE stories SET featured = 0");

    // Feature selected story
    $stmt = $db->prepare("UPDATE stories SET featured = 1 WHERE id = ?");
    $stmt->execute([$storyId]);

    header("Location: admin-dashboard.php");
    exit();
}
?>
