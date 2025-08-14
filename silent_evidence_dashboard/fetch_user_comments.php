<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode([]);
    exit;
}
$uid = $_SESSION['user_id'];

$stmt = $db->prepare("
    SELECT c.id, c.comment, DATE_FORMAT(c.created_at,'%Y-%m-%d') as created_at, s.title as story_title
    FROM comments c
    JOIN stories s ON s.id=c.story_id
    WHERE c.user_id=?
    ORDER BY c.created_at DESC
");
$stmt->execute([$uid]);
$comments = $stmt->fetchAll();

echo json_encode($comments);
