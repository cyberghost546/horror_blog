<?php
declare(strict_types=1);
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

$stmt = $db->query("
    SELECT s.id, s.title, s.author, s.category, s.views, 
           (SELECT COUNT(*) FROM likes l WHERE l.story_id = s.id) as likes,
           DATE_FORMAT(s.created_at,'%Y-%m-%d') created_at
    FROM stories s
    ORDER BY s.created_at DESC
");
$stories = $stmt->fetchAll();
echo json_encode($stories);
exit;
