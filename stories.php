<?php
session_start();
require 'include/db.php';
$storyId = $_GET['id'] ?? null;
if (!$storyId || !ctype_digit($storyId)) {
    http_response_code(404);
    $story = null;
} else {
    $storyId = (int) $storyId;
    // bump views first 
    $update = $pdo->prepare('UPDATE stories SET views = views + 1 WHERE id = :id');
    $update->execute([':id' => $storyId]);
    // load story with author 
    $stmt = $pdo->prepare('SELECT s.id, s.title, s.category, s.content, s.created_at, s.views, s.likes, u.display_name, u.username, u.avatar FROM stories s JOIN users u ON u.id = s.user_id WHERE s.id = :id AND s.is_published = 1 LIMIT 1');
    $stmt->execute([':id' => $storyId]);
    $story = $stmt->fetch();
}
if ($story) {
    $authorName = $story['display_name'] ?:
        $story['username'];
    $authorAvatar = $story['avatar'] ?: 'uploads/avatars/default.png';
    // quick read time estimate 
    $wordCount = str_word_count(strip_tags($story['content']));
    $minutes = max(1, ceil($wordCount / 200));
} else {
    $authorName = null;
    $authorAvatar = null;
    $minutes = null;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>
        <?php
        echo $story ? htmlspecialchars($story['title']) . ' | silent_evidence' : 'Story not found | silent_evidence'; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .breadcrumb {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .breadcrumb a {
            color: #9ca3af;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #e5e7eb;
        }

        .story-card {
            background-color: #020617;
            border-radius: 18px;
            border: 1px solid #111827;
            padding: 22px 20px 26px;
        }

        .story-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #f87171;
        }

        .story-title {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #f9fafb;
        }

        .story-meta {
            font-size: 0.8rem;
            color: #9ca3af;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 18px;
        }

        .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background-color: #4b5563;
        }

        .author-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .author-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid #111827;
        }

        .author-name {
            font-size: 0.85rem;
            color: #e5e7eb;
        }

        .author-username {
            font-size: 0.78rem;
            color: #9ca3af;
        }

        .story-body {
            font-size: 0.97rem;
            line-height: 1.7;
            color: #e5e7eb;
            white-space: pre-wrap;
        }

        .story-body p {
            margin-bottom: 1rem;
        }

        .stats-row {
            margin-top: 20px;
            font-size: 0.8rem;
            color: #9ca3af;
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background-color: #020617;
            border: 1px solid #111827;
            font-size: 0.78rem;
        }

        .action-row {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-outline-silent {
            border-color: #4b5563;
            color: #e5e7eb;
            font-size: 0.85rem;
            border-radius: 999px;
            padding: 6px 14px;
        }

        .btn-outline-silent:hover {
            background-color: #111827;
            border-color: #6b7280;
            color: #ffffff;
        }

        .empty-state {
            text-align: center;
            margin-top: 80px;
        }

        .empty-state h1 {
            font-size: 1.6rem;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 0.95rem;
            color: #9ca3af;
        }
    </style>
</head>

<body> <?php include 'include/header.php'; ?> <div class="page-wrapper"> <?php if (!$story): ?>
            <div class="empty-state">
                <h1>Story not found</h1>
                <p>This story does not exist or is no longer available.</p>
                <a href="stories.php" class="btn btn-outline-silent mt-3">Back to stories</a>
            </div>
        <?php else: ?>
            <nav class="breadcrumb mb-2">
                <a href="index.php">Home</a>
                <span class="mx-1">/</span>
                <a href="stories.php">Stories</a>
                <span class="mx-1">/</span>
                <span><?php echo htmlspecialchars($story['title']); ?></span>
            </nav>

            <article class="story-card">
                <div class="story-category mb-1">
                    <?php
                                                                                $cat = $story['category'];
                                                                                if ($cat === 'true') {
                                                                                    echo 'True story';
                                                                                } elseif ($cat === 'paranormal') {
                                                                                    echo 'Paranormal';
                                                                                } elseif ($cat === 'urban') {
                                                                                    echo 'Urban legend';
                                                                                } elseif ($cat === 'short') {
                                                                                    echo 'Short nightmare';
                                                                                } else {
                                                                                    echo htmlspecialchars($cat);
                                                                                }
                    ?>
                </div>

                <h1 class="story-title">
                    <?php echo htmlspecialchars($story['title']); ?>
                </h1>

                <div class="story-meta">
                    <div class="author-block">
                        <img src="<?php echo htmlspecialchars($authorAvatar); ?>" class="author-avatar" alt="Author">
                        <div>
                            <div class="author-name">
                                <?php echo htmlspecialchars($authorName); ?>
                            </div>
                            <div class="author-username">
                                @<?php echo htmlspecialchars($story['username']); ?>
                            </div>
                        </div>
                    </div>

                    <span class="dot"></span>

                    <span>
                        Posted
                        <?php echo date('d M Y', strtotime($story['created_at'])); ?>
                    </span>

                    <span class="dot"></span>

                    <span>
                        <?php echo $minutes; ?> min read
                    </span>
                </div>

                <div class="story-body">
                    <?php
                                                                                echo nl2br(htmlspecialchars($story['content']));
                    ?>
                </div>

                <div class="stats-row">
                    <div>
                        <span class="stat-chip">
                            👁
                            <span><?php echo (int) $story['views'] + 1; ?></span>
                            <span>views</span>
                        </span>

                        <span class="stat-chip ms-1">
                            ❤
                            <span><?php echo (int) $story['likes']; ?></span>
                            <span>likes</span>
                        </span>
                    </div>

                    <div class="action-row">
                        <?php if (!empty($_SESSION['user_id'])): ?>
                            <button type="button" class="btn btn-outline-silent">
                                Like story
                            </button>
                            <button type="button" class="btn btn-outline-silent">
                                Bookmark
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-silent">
                                Log in to react
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>