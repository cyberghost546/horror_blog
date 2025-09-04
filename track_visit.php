<?php
// track_visit.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'includes/db.php';

// Only count once per session
if (!isset($_SESSION['has_visited'])) {
    $_SESSION['has_visited'] = true;

    $stmt = $db->prepare("INSERT INTO visits (ip_address, visit_time) VALUES (?, NOW())");
    $stmt->execute([$_SERVER['REMOTE_ADDR']]);
}
?>