<?php
session_start();
require 'include/db.php';

$q = trim($_GET['q'] ?? '');
$categoryKey = $_GET['category'] ?? null;

$categoryMap = [
    'true'       => 'True stories',
    'paranormal' => 'Paranormal',
    'urban'      => 'Urban legends',
    'short'      => 'Short nightmares',
];

$pageHeading = "Search results";

// Build SQL
$where  = "s.is_published = 1";
$params = [];

// If search text exists
if ($q !== '') {
    $where .= " AND (s.title LIKE :q OR s.content LIKE :q)";
    $params[':q'] = "%$q%";

    $pageHeading = "Results for \"" . htmlspecialchars($q) . "\"";
}

// If category filter exists
if ($categoryKey && isset($categoryMap[$categoryKey])) {
    $where .= " AND s.category = :cat";
    $params[':cat'] = $categoryKey;
}

// Fetch results
$sql = "
    SELECT s.id, s.title, s.category, s.content, s.created_at, s.views, s.likes,
           u.display_name, u.username, u.avatar
    FROM stories s
    JOIN users u ON u.id = s.user_id
    WHERE $where
    ORDER BY s.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Search | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .story-card {
            background-color: #0f172a;
            border-radius: 16px;
            border: 1px solid #1e293b;
            color: #e5e7eb;
            overflow: hidden;
            cursor: pointer;
            transition: 0.2s;
        }

        .story-card:hover {
            transform: translateY(-3px);
            border-color: #f60000;
            box-shadow: 0 0 15px rgba(246, 0, 0, 0.25);
        }

        .category-tag {
            font-size: 0.75rem;
            color: #f87171;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    </style>
</head>

<body>

<?php include "include/header.php"; ?>

<div class="page-wrapper">

    <h2 class="mb-4">
        <?php echo $pageHeading; ?>
    </h2>

    <?php if (!$stories): ?>
        <p class="text-secondary">
            No stories found. Try searching something else.
        </p>
    <?php else: ?>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            <?php foreach ($stories as $story): ?>

                <div class="col">
                    <a href="story.php?id=<?php echo $story['id']; ?>" style="text-decoration:none;">
                        <div class="story-card h-100">

                            <div class="card-body">
                                <div class="category-tag mb-1">
                                    <?php echo htmlspecialchars($story['category']); ?>
                                </div>

                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($story['title']); ?>
                                </h5>

                                <p class="card-text">
                                    <?php
                                        $preview = strip_tags($story['content']);
                                        if (strlen($preview) > 120) {
                                            $preview = substr($preview, 0, 120) . "...";
                                        }
                                        echo htmlspecialchars($preview);
                                    ?>
                                </p>
                            </div>

                            <div class="card-footer bg-transparent border-0 d-flex justify-content-between px-3 pb-3">
                                <small class="text-muted">
                                    By <?php echo htmlspecialchars($story['display_name'] ?: $story['username']); ?>
                                </small>
                                <small class="text-muted">
                                    👁 <?php echo $story['views']; ?> • ❤ <?php echo $story['likes']; ?>
                                </small>
                            </div>

                        </div>
                    </a>
                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
