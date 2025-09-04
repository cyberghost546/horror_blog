<?php
include 'db.php';
$stmt = $pdo->query("SELECT drop_time FROM story_drop WHERE id = 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['drop_time' => $row['drop_time']]);
