<?php
session_start();
require 'include/db.php';
$catKey = $_GET['cat'] ?? '';
$map = [
    // base categories 
    'true' => [
        'label' =>
        'True stories',
        'tag' => 'TRUE',
        'desc' => 'Real experiences users claim actually happened.',
        'parent' => null,
    ],
    'paranormal' => [
        'label' => 'Paranormal',
        'tag' => 'PARANORMAL',
        'desc' => 'Ghosts, spirits, haunted houses, cursed objects.',
        'parent' => null,
    ],
    'urban' => [
        'label' => 'Urban legends',
        'tag' => 'URBAN',
        'desc' => 'Stories that spread online and feel too real.',
        'parent' => null,
    ],
    'short' => [
        'label' => 'Short nightmares',
        'tag' => 'SHORT',
        'desc' => 'Quick reads that hit fast.',
        'parent' => null,
    ],
    // connected sub categories 
    'haunted' => ['label' => 'Haunted places', 'tag' => 'HAUNTED', 'desc' => 'Real locations with disturbing history.', 'parent' => 'paranormal',],
    'ghosts' => ['label' => 'Ghost encounters', 'tag' => 'GHOSTS', 'desc' => 'Unexplainable sightings and hauntings.', 'parent' => 'paranormal',],
    'missing' => ['label' => 'Missing persons', 'tag' => 'MISSING', 'desc' => 'Cases that leave more questions than answers.', 'parent' => 'true',],
    'crime' => ['label' => 'Crime and mystery', 'tag' => 'CRIME', 'desc' => 'Dark events that defy explanation.', 'parent' => 'true',],
    'sleep' => ['label' => 'Sleep paralysis', 'tag' => 'SLEEP', 'desc' => 'The figures you cannot move away from.', 'parent' => 'paranormal',],
    'forest' => ['label' => 'Forest horror', 'tag' => 'FOREST', 'desc' => 'What hides between the trees.', 'parent' => 'paranormal',],
    'night' => ['label' => 'Night shift stories', 'tag' => 'NIGHT', 'desc' => 'Late hours that get way too strange.', 'parent' => 'true',],
    'calls' => ['label' => 'Strange phone calls', 'tag' => 'CALLS', 'desc' => 'Voices that should not exist.', 'parent' => 'urban',],
    'creatures' => ['label' => 'Creature sightings', 'tag' => 'CREATURES', 'desc' => 'Encounters with things not human.', 'parent' => 'urban',],
    'abandoned' => ['label' => 'Abandoned places', 'tag' => 'ABANDONED', 'desc' => 'Ruins that feel alive inside.', 'parent' => 'paranormal',],
    'psychological' => ['label' => 'Psychological horror', 'tag' => 'PSYCHO', 'desc' => 'Mind-bending stories that mess with your head.', 'parent' => 'short',],
];

// labels for the tags on each card, same style as stories.php 
$categoryLabelMap = [
    'true' => 'TRUE STORY',
    'paranormal' => 'PARANORMAL',
    'urban' => 'URBAN LEGEND',
    'short' => 'SHORT NIGHTMARE',
    'haunted' => 'HAUNTED',
    'ghosts' => 'GHOST ENCOUNTER',
    'missing' => 'MISSING PERSON',
    'crime' => 'CRIME MYSTERY',
    'sleep' => 'SLEEP PARALYSIS',
    'forest' => 'FOREST HORROR',
    'night' => 'NIGHT SHIFT',
    'calls' => 'STRANGE CALL',
    'creatures' => 'CREATURE SIGHTING',
    'abandoned' => 'ABANDONED',
    'psychological' => 'PSYCHOLOGICAL',
];
if (!isset($map[$catKey])) {
    http_response_code(404);
    $pageTitle = 'Category not found';
    $catInfo = null;
    $stories = [];
} else {
    $catInfo = $map[$catKey];
    $pageTitle = $catInfo['label'] . ' | silent_evidence';
    // build list of categories to include in query 
    $categoriesForQuery = [$catKey];
    // if this is a base category, also include all its children 
    if ($catInfo['parent'] === null) {
        foreach ($map as $key => $info) {
            if (!empty($info['parent']) && $info['parent'] === $catKey) {
                $categoriesForQuery[] = $key;
            }
        }
    }
    // build dynamic IN() clause
    $placeholders = [];
    $params = [];
    foreach ($categoriesForQuery as $index => $key) {
        $ph = ':cat' . $index;
        $placeholders[] = $ph;
        $params[$ph] = $key;
    }
    $inClause = implode(',', $placeholders);
    $sql = " SELECT s.id, s.title, s.content, s.category, s.views, s.likes, s.created_at, s.image_path, u.display_name, u.username, u.avatar FROM stories s JOIN users u ON u.id = s.user_id WHERE s.category IN ($inClause) AND s.is_published = 1 ORDER BY s.created_at DESC ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
        }

        .section-sub {
            font-size: 0.9rem;
            color: #94a3b8;
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

        .story-thumb {
            height: 160px;
            overflow: hidden;
            background-color: #020617;
        }

        .story-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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

        .breadcrumb {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .breadcrumb a {
            color: #9ca3af;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #e5e7eb;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="page-wrapper">

        <?php if (!$catInfo): ?>

            <p>Category not found.</p>

        <?php else: ?>

            <nav class="breadcrumb mb-2">
                <a href="index.php">Home</a>
                <span class="mx-1">/</span>
                <a href="stories.php">Stories</a>
                <span class="mx-1">/</span>
                <span><?php echo htmlspecialchars($catInfo['label']); ?></span>
            </nav>

            <header class="mb-4">
                <h1 class="section-title mb-1">
                    <?php echo htmlspecialchars($catInfo['label']); ?>
                </h1>
                <p class="section-sub mb-0">
                    <?php echo htmlspecialchars($catInfo['desc']); ?>
                </p>
            </header>

            <?php if (!$stories): ?>
                <p class="text-secondary small">No stories in this category yet.</p>
            <?php else: ?>

                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($stories as $story): ?>

                        <?php
                        $rawContent = strip_tags($story['content']);
                        if (function_exists('mb_substr')) {
                            $preview = mb_substr($rawContent, 0, 120);
                        } else {
                            $preview = substr($rawContent, 0, 120);
                        }

                        $catSlug  = $story['category'];
                        $catLabel = $categoryLabelMap[$catSlug] ?? strtoupper($catSlug);

                        $thumb = !empty($story['image_path'])
                            ? $story['image_path']
                            : 'assets/img/default_story.jpg';
                        ?>

                        <div class="col">
                            <a href="story.php?id=<?php echo (int)$story['id']; ?>" class="text-decoration-none">
                                <div class="story-card h-100">

                                    <div class="story-thumb">
                                        <img src="<?php echo htmlspecialchars($thumb); ?>" alt="Story image">
                                    </div>

                                    <div class="card-body">
                                        <div class="category-tag mb-1">
                                            <?php echo htmlspecialchars($catLabel); ?>
                                        </div>

                                        <h5 class="card-title">
                                            <?php echo htmlspecialchars($story['title']); ?>
                                        </h5>

                                        <p class="card-text">
                                            <?php echo htmlspecialchars($preview); ?>...
                                        </p>
                                    </div>

                                    <div class="card-footer bg-transparent border-0 d-flex justify-content-between px-3 pb-3">
                                        <small class="author">
                                            By <?php echo htmlspecialchars($story['display_name'] ?: $story['username']); ?>
                                        </small>
                                        <small class="stat-chip">
                                            👁 <?php echo (int)$story['views']; ?>
                                            • ❤ <?php echo (int)$story['likes']; ?>
                                        </small>
                                    </div>

                                </div>
                            </a>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>