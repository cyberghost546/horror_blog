<?php
// auth.php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("UPDATE users SET last_active = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}


$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT id, username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$userId]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
  session_destroy();
  header('Location: log_in.php');
  exit();
}
