<?php
session_start();
require 'include/db.php';

// defaults so you never get "undefined variable" warnings
$filterLabel = 'All categories';
$topByViews  = [];
$topByLikes  = [];

// optional category filter, same style as stories.php
$categoryKey = $_GET['category'] ?? null;

$categoryMap = [
    'true'       => 'True stories',
    'paranormal' => 'Paranormal',
    'urban'      => 'Urban legends',
    'short'      => 'Short nightmares',
];

$where  = "s.is_published = 1";
$params = [];

if ($categoryKey && isset($categoryMap[$categoryKey])) {
    $where .= " AND s.category = :cat";
    $params[':cat'] = $categoryKey;
    $filterLabel = $categoryMap[$categoryKey];
} else {
    $categoryKey  = null;
    $filterLabel  = 'All categories';
}

// top by views
$sqlViews = "
    SELECT s.id, s.title, s.category, s.content, s.created_at, s.views, s.likes,
           u.display_name, u.username
      FROM stories s
      JOIN users u ON u.id = s.user_id
     WHERE $where
  ORDER BY s.views DESC, s.created_at DESC
     LIMIT 9
";

$stmt = $pdo->prepare($sqlViews);
$stmt->execute($params);
$topByViews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// top by likes
$sqlLikes = "
    SELECT s.id, s.title, s.category, s.content, s.created_at, s.views, s.likes,
           u.display_name, u.username
      FROM stories s
      JOIN users u ON u.id = s.user_id
     WHERE $where
  ORDER BY s.likes DESC, s.created_at DESC
     LIMIT 9
";

$stmt = $pdo->prepare($sqlLikes);
$stmt->execute($params);
$topByLikes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Top stories | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

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

        .card-body {
            padding: 16px;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #f9fafb;
        }

        .card-text {
            font-size: 0.9rem;
            color: #cbd5e1;
        }

        .category-tag {
            font-size: 0.75rem;
            color: #f87171;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .author {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .stat-chip {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .filter-pill {
            border-radius: 999px;
            border: 1px solid #4b5563;
            padding: 4px 10px;
            font-size: 0.8rem;
            color: #e5e7eb;
            text-decoration: none;
        }

        .filter-pill.active,
        .filter-pill:hover {
            background-color: #f60000;
            border-color: #f60000;
            color: #f9fafb;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #f9fafb;
        }
    </style>
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="page-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Top stories</h1>
            <p class="text-secondary mb-0" style="font-size:0.9rem;">
                Most viewed and most liked stories from the community.
            </p>
        </div>
        <div class="text-end">
            <div class="mb-1" style="font-size:0.8rem; color:#9ca3af;">
                Filter by vibe
            </div>
            <div class="d-flex flex-wrap gap-1 justify-content-end">
                <a href="top.php"
                   class="filter-pill <?php echo $categoryKey === null ? 'active' : ''; ?>">
                    All
                </a>
                <a href="top.php?category=true"
                   class="filter-pill <?php echo $categoryKey === 'true' ? 'active' : ''; ?>">
                    True
                </a>
                <a href="top.php?category=paranormal"
                   class="filter-pill <?php echo $categoryKey === 'paranormal' ? 'active' : ''; ?>">
                    Paranormal
                </a>
                <a href="top.php?category=urban"
                   class="filter-pill <?php echo $categoryKey === 'urban' ? 'active' : ''; ?>">
                    Urban
                </a>
                <a href="top.php?category=short"
                   class="filter-pill <?php echo $categoryKey === 'short' ? 'active' : ''; ?>">
                    Short
                </a>
            </div>
        </div>
    </div>

    <hr class="border-secondary mb-4">

    <!-- Most viewed -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title mb-0">Most viewed</h2>
            <span class="text-secondary" style="font-size:0.85rem;">
                <?php echo htmlspecialchars($filterLabel); ?>
            </span>
        </div>

        <?php if (!$topByViews): ?>
            <p class="text-secondary">No stories yet.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($topByViews as $story): ?>
                    <div class="col">
                        <a href="story.php?id=<?php echo (int)$story['id']; ?>" style="text-decoration:none;">
                            <div class="story-card h-100">
                                <div class="card-body">

                                    <div class="category-tag mb-1">
                                        <?php
                                        $cat = $story['category'];
                                        if ($cat === 'true') {
                                            echo 'TRUE STORY';
                                        } elseif ($cat === 'paranormal') {
                                            echo 'PARANORMAL';
                                        } elseif ($cat === 'urban') {
                                            echo 'URBAN LEGEND';
                                        } elseif ($cat === 'short') {
                                            echo 'SHORT NIGHTMARE';
                                        } else {
                                            echo htmlspecialchars($cat);
                                        }
                                        ?>
                                    </div>

                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($story['title']); ?>
                                    </h5>

                                    <p class="card-text mb-0">
                                        <?php
                                        $preview = strip_tags($story['content']);
                                        if (strlen($preview) > 120) {
                                            $preview = substr($preview, 0, 120) . '...';
                                        }
                                        echo htmlspecialchars($preview);
                                        ?>
                                    </p>
                                </div>

                                <div class="card-footer bg-transparent border-0 d-flex justify-content-between px-3 pb-3">
                                    <small class="author">
                                        By <?php echo htmlspecialchars($story['display_name'] ?: $story['username']); ?>
                                    </small>
                                    <small class="stat-chip">
                                        👁 <?php echo (int)$story['views']; ?> • ❤ <?php echo (int)$story['likes']; ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Most liked -->
    <section>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title mb-0">Most liked</h2>
            <span class="text-secondary" style="font-size:0.85rem;">
                <?php echo htmlspecialchars($filterLabel); ?>
            </span>
        </div>

        <?php if (!$topByLikes): ?>
            <p class="text-secondary">No stories yet.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($topByLikes as $story): ?>
                    <div class="col">
                        <a href="story.php?id=<?php echo (int)$story['id']; ?>" style="text-decoration:none;">
                            <div class="story-card h-100">
                                <div class="card-body">

                                    <div class="category-tag mb-1">
                                        <?php
                                        $cat = $story['category'];
                                        if ($cat === 'true') {
                                            echo 'TRUE STORY';
                                        } elseif ($cat === 'paranormal') {
                                            echo 'PARANORMAL';
                                        } elseif ($cat === 'urban') {
                                            echo 'URBAN LEGEND';
                                        } elseif ($cat === 'short') {
                                            echo 'SHORT NIGHTMARE';
                                        } else {
                                            echo htmlspecialchars($cat);
                                        }
                                        ?>
                                    </div>

                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($story['title']); ?>
                                    </h5>

                                    <p class="card-text mb-0">
                                        <?php
                                        $preview = strip_tags($story['content']);
                                        if (strlen($preview) > 120) {
                                            $preview = substr($preview, 0, 120) . '...';
                                        }
                                        echo htmlspecialchars($preview);
                                        ?>
                                    </p>
                                </div>

                                <div class="card-footer bg-transparent border-0 d-flex justify-content-between px-3 pb-3">
                                    <small class="author">
                                        By <?php echo htmlspecialchars($story['display_name'] ?: $story['username']); ?>
                                    </small>
                                    <small class="stat-chip">
                                        👁 <?php echo (int)$story['views']; ?> • ❤ <?php echo (int)$story['likes']; ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
