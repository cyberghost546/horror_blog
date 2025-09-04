<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || empty($_POST['comment']) || empty($_POST['story_id'])) {
    http_response_code(400);
    echo "Invalid request";
    exit();
}

$userId = $_SESSION['user_id'];
$storyId = (int)$_POST['story_id'];
$comment = trim($_POST['comment']);

$insert = $db->prepare("INSERT INTO comments (story_id, user_id, comment) VALUES (?, ?, ?)");
$insert->execute([$storyId, $userId, $comment]);

// get username and created_at
$userStmt = $db->prepare("SELECT username FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$username = $userStmt->fetchColumn();

$created_at = date('Y-m-d H:i:s'); // server time

// notify socket server
$payload = json_encode([
    'storyId' => $storyId,
    'username' => $username,
    'comment' => $comment,
    'created_at' => $created_at
]);

$socketServerUrl = 'http://localhost:3000/notify'; // change if different host or port

$ch = curl_init($socketServerUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);
curl_exec($ch);
curl_close($ch);

// return JSON response for AJAX client
echo json_encode([
    'username' => $username,
    'comment' => htmlspecialchars($comment),
    'created_at' => date('F j, Y, g:i a')
]);
