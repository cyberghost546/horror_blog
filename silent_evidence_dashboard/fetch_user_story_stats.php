<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['error'=>'Not logged in']);
    exit;
}
$uid = $_SESSION['user_id'];

// Story stats
$stmt = $db->prepare("SELECT COUNT(*) FROM stories WHERE author_id=?");
$stmt->execute([$uid]);
$stories = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT SUM(views) FROM stories WHERE author_id=?");
$stmt->execute([$uid]);
$views = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM likes l JOIN stories s ON s.id=l.story_id WHERE s.author_id=?");
$stmt->execute([$uid]);
$likes = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM comments c JOIN stories s ON s.id=c.story_id WHERE s.author_id=?");
$stmt->execute([$uid]);
$comments = (int)$stmt->fetchColumn();

// Last 7 days views per story
$visits7=[];
$stmt = $db->prepare("
    SELECT DATE(created_at) d, SUM(views) c
    FROM stories
    WHERE author_id=? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();
$map=[];
foreach($rows as $r){ $map[$r['d']] = (int)$r['c']; }
for($i=6;$i>=0;$i--){
    $d=date('Y-m-d', strtotime("-{$i} day"));
    $visits7[]=['d'=>$d,'c'=>$map[$d]??0];
}

echo json_encode(['stories'=>$stories,'views'=>$views,'likes'=>$likes,'comments'=>$comments,'visits7'=>$visits7]);
