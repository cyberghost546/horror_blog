<?php session_start();
require 'include/db.php';
// read category filter from URL, like ?category=true 
$categoryKey = $_GET['category'] ?? null;
// ALL categories including your new ones 
$categoryMap = [
    'true' => 'True stories',
    'paranormal' => 'Paranormal',
    'urban' => 'Urban legends',
    'short' => 'Short nightmares',
    'haunted' => 'Haunted places',
    'ghosts' => 'Ghost encounters',
    'missing' => 'Missing persons',
    'crime' => 'Crime & mystery',
    'sleep' => 'Sleep paralysis',
    'forest' => 'Forest horror',
    'night' => 'Night shift stories',
    'calls' => 'Strange phone calls',
    'creatures' => 'Creature sightings',
    'abandoned' => 'Abandoned places',
    'psychological' => 'Psychological horror'
];

$where = 's.is_published = 1';

$params = [];

// if a valid category is selected, filter on it 
if ($categoryKey && isset($categoryMap[$categoryKey])) {
    $where .= ' AND s.category = :cat';
    $params[':cat'] = $categoryKey;
    $pageHeading = $categoryMap[$categoryKey];
} else {
    $categoryKey = null;
    $pageHeading = 'All stories';
}

// load stories 
$sql = " SELECT s.id, s.title, s.category, s.content, s.created_at, s.views, s.likes, s.image_path, u.display_name, u.username, u.avatar FROM stories s JOIN users u ON u.id = s.user_id WHERE $where ORDER BY s.created_at DESC ";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// NICE LABELS for the small category tag 
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
    'psychological' => 'PSYCHOLOGICAL'
];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Stories | silent_evidence</title>
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
            font-size: 1.15rem;
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

        .category-card {
            text-decoration: none;
        }

        .cat-box {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 18px 20px;
            color: #e5e7eb;
            transition: 0.2s;
        }

        .cat-box:hover {
            border-color: #f60000;
            box-shadow: 0 0 15px rgba(246, 0, 0, 0.25);
            transform: translateY(-3px);
        }

        .cat-box h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #f8fafc;
        }

        .cat-box p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin: 0;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    <div class="page-wrapper">

        <h2 class="mb-3">Categories</h2>

        <div class="mb-4">
            <label class="form-label text-light fw-semibold">Browse categories</label>
            <select class="form-select bg-dark text-light border-secondary"
                onchange="if (this.value) window.location.href = this.value;">

                <option value="">Select a category</option>

                <option value="stories.php?category=true">True stories</option>
                <option value="stories.php?category=paranormal">Paranormal</option>
                <option value="stories.php?category=urban">Urban legends</option>
                <option value="stories.php?category=short">Short nightmares</option>

                <optgroup label="Paranormal related">
                    <option value="stories.php?category=haunted">Haunted places</option>
                    <option value="stories.php?category=ghosts">Ghost encounters</option>
                    <option value="stories.php?category=sleep">Sleep paralysis</option>
                    <option value="stories.php?category=forest">Forest horror</option>
                    <option value="stories.php?category=abandoned">Abandoned places</option>
                </optgroup>

                <optgroup label="True story related">
                    <option value="stories.php?category=missing">Missing persons</option>
                    <option value="stories.php?category=crime">Crime and mystery</option>
                    <option value="stories.php?category=night">Night shift stories</option>
                </optgroup>

                <optgroup label="Urban related">
                    <option value="stories.php?category=calls">Strange phone calls</option>
                    <option value="stories.php?category=creatures">Creature sightings</option>
                </optgroup>

                <optgroup label="Short nightmare related">
                    <option value="stories.php?category=psychological">Psychological horror</option>
                </optgroup>

            </select>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <?php echo htmlspecialchars($pageHeading); ?>
            </h2>
            <?php if ($categoryKey): ?>
                <a href="stories.php" class="small text-secondary">
                    Clear filter
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$stories): ?>
            <p class="text-secondary">No stories in this category yet.</p>
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
                                        • 🔖
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