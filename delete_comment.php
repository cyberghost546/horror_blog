<?php
session_start();
require __DIR__ . '/includes/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: log_in.php');
    exit;
}

$uid = $_SESSION['user_id'];
$commentId = $_GET['id'] ?? 0;

// Delete only if it belongs to the user
$stmt = $db->prepare("DELETE FROM comments WHERE id=? AND user_id=?");
$stmt->execute([$commentId, $uid]);

header('Location: user_comment_dashboard.php');
exit;
