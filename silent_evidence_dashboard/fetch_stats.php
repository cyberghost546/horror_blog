<?php
// ================================
// 2. /fetch_stats.php
// ================================
// Returns JSON like:
// {
//   "users": 5,
//   "stories": 12,
//   "comments": 34,
//   "visits": 789,
//   "visits7": [{"d":"2025-08-07","c":12}, ...]
// }

declare(strict_types=1);
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

function one(PDO $db, string $sql): int {
    $stmt = $db->query($sql);
    return (int) $stmt->fetchColumn();
}

// Ensure tables exist, fail quietly if not
$users = 0;
$stories = 0;
$comments = 0;
$visits = 0;
$visits7 = [];

try { $users = one($db, 'SELECT COUNT(*) FROM users'); } catch (Throwable $e) {}
try { $stories = one($db, 'SELECT COUNT(*) FROM stories'); } catch (Throwable $e) {}
try { $comments = one($db, 'SELECT COUNT(*) FROM comments'); } catch (Throwable $e) {}
try { $visits = one($db, 'SELECT COUNT(*) FROM visits'); } catch (Throwable $e) {}

// Last 7 days visits, returns zero-filled days even if no rows
try {
    $stmt = $db->prepare(
        'SELECT DATE(visited_at) d, COUNT(*) c
         FROM visits
         WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(visited_at)
         ORDER BY d ASC'
    );
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // Zero fill days
    $map = [];
    foreach ($rows as $r) { $map[$r['d']] = (int)$r['c']; }
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $visits7[] = [ 'd' => $d, 'c' => ($map[$d] ?? 0) ];
    }
} catch (Throwable $e) {
    // If table missing, still return zeros
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $visits7[] = [ 'd' => $d, 'c' => 0 ];
    }
}

echo json_encode([
    'users' => $users,
    'stories' => $stories,
    'comments' => $comments,
    'visits' => $visits,
    'visits7' => $visits7,
]);
exit;
?>