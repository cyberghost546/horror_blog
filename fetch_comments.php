<?php
require 'includes/db.php';
session_start();

if (empty($_GET['story_id'])) {
    http_response_code(400);
    echo "Invalid request";
    exit();
}

$storyId = (int)$_GET['story_id'];

$stmt = $db->prepare("SELECT c.comment, c.created_at, u.username
                      FROM comments c
                      JOIN users u ON c.user_id = u.id
                      WHERE c.story_id = ?
                      ORDER BY c.created_at DESC");
$stmt->execute([$storyId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comments);
