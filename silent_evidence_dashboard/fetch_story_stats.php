<?php
declare(strict_types=1);
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

function one(PDO $db, string $sql): int {
    $stmt = $db->query($sql);
    return (int) $stmt->fetchColumn();
}

// Story stats
$stories = 0;
$views = 0;
$comments = 0;
$likes = 0;

try { $stories = one($db, 'SELECT COUNT(*) FROM stories'); } catch (Throwable $e) {}
try { $views = one($db, 'SELECT SUM(views) FROM stories'); } catch (Throwable $e) {}
try { $comments = one($db, 'SELECT COUNT(*) FROM comments'); } catch (Throwable $e) {}
try { $likes = one($db, 'SELECT COUNT(*) FROM likes'); } catch (Throwable $e) {}

// Last 7 days views per story
$visits7 = [];
try {
    $stmt = $db->prepare(
        "SELECT DATE(created_at) d, SUM(views) c
         FROM stories
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(created_at)
         ORDER BY d ASC"
    );
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $map = [];
    foreach ($rows as $r) { $map[$r['d']] = (int)$r['c']; }
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $visits7[] = ['d'=>$d,'c'=>$map[$d]??0];
    }
} catch (Throwable $e) {
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $visits7[] = ['d'=>$d,'c'=>0];
    }
}

echo json_encode([
    'stories'=>$stories,
    'views'=>$views,
    'comments'=>$comments,
    'likes'=>$likes,
    'visits7'=>$visits7
]);
exit;
