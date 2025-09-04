<?php
session_start();
require 'includes/db.php';

if (isset($_SESSION['user_id'])) {
  $stmt = $db->prepare("DELETE FROM user_sessions WHERE user_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
}

session_destroy();
header("Location: log_in.php");
exit();

