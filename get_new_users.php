<?php
require 'includes/db.php';

// Define how you determine "new users"
// For example, users who registered today:
$today = date('Y-m-d');

$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$newUsers = $stmt->fetchColumn();

echo $newUsers;
