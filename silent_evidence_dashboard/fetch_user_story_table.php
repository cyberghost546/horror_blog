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
    SELECT id, title, category, views, 
           (SELECT COUNT(*) FROM likes l WHERE l.story_id = s.id) as likes,
           DATE_FORMAT(created_at,'%Y-%m-%d') created_at
    FROM stories s
    WHERE s.author_id=?
    ORDER BY created_at DESC
");
$stmt->execute([$uid]);
$stories = $stmt->fetchAll();

echo json_encode($stories);
