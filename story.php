<?php
session_start();
require 'include/db.php';

$storyId = $_GET['id'] ?? null;

// validate id
if (!$storyId || !ctype_digit($storyId)) {
    http_response_code(404);
    $story   = null;
    $storyId = null;
} else {
    $storyId = (int)$storyId;
}

$currentUserId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$isAdmin       = !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

$liked        = false;
$bookmarked   = false;
$message      = '';
$commentError = '';
$comments     = [];

// handle like / bookmark / comment actions
if ($storyId && $_SERVER['REQUEST_METHOD'] === 'POST' && $currentUserId) {
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_like') {
        // check if already liked
        $stmt = $pdo->prepare(
            'SELECT id FROM story_likes WHERE user_id = :uid AND story_id = :sid LIMIT 1'
        );
        $stmt->execute([':uid' => $currentUserId, ':sid' => $storyId]);
        $likeRow = $stmt->fetch();

        if ($likeRow) {
            // unlike
            $pdo->prepare('DELETE FROM story_likes WHERE id = :id')
                ->execute([':id' => $likeRow['id']]);
            $pdo->prepare('UPDATE stories SET likes = GREATEST(likes - 1, 0) WHERE id = :sid')
                ->execute([':sid' => $storyId]);
            $message = 'You removed your like.';
        } else {
            // like
            $pdo->prepare(
                'INSERT INTO story_likes (user_id, story_id, created_at)
                 VALUES (:uid, :sid, NOW())'
            )->execute([':uid' => $currentUserId, ':sid' => $storyId]);

            $pdo->prepare('UPDATE stories SET likes = likes + 1 WHERE id = :sid')
                ->execute([':sid' => $storyId]);
            $message = 'You liked this story.';
        }
    }

    if ($action === 'toggle_bookmark') {
        // check if already bookmarked
        $stmt = $pdo->prepare(
            'SELECT id FROM story_bookmarks WHERE user_id = :uid AND story_id = :sid LIMIT 1'
        );
        $stmt->execute([':uid' => $currentUserId, ':sid' => $storyId]);
        $bmRow = $stmt->fetch();

        if ($bmRow) {
            $pdo->prepare('DELETE FROM story_bookmarks WHERE id = :id')
                ->execute([':id' => $bmRow['id']]);
            $message = 'Removed from bookmarks.';
        } else {
            $pdo->prepare(
                'INSERT INTO story_bookmarks (user_id, story_id, created_at)
                 VALUES (:uid, :sid, NOW())'
            )->execute([':uid' => $currentUserId, ':sid' => $storyId]);
            $message = 'Added to your bookmarks.';
        }
    }

    if ($action === 'add_comment') {
        $commentText = trim($_POST['comment'] ?? '');

        if ($commentText === '') {
            $commentError = 'Comment cannot be empty.';
        } elseif (mb_strlen($commentText) > 2000) {
            $commentError = 'Comment is too long.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO story_comments (story_id, user_id, content, created_at)
                 VALUES (:sid, :uid, :content, NOW())'
            );
            $stmt->execute([
                ':sid'     => $storyId,
                ':uid'     => $currentUserId,
                ':content' => $commentText,
            ]);
            $message = 'Your comment has been posted.';
        }
    }

    if ($action === 'delete_comment') {
        $commentId = (int)($_POST['comment_id'] ?? 0);

        if ($commentId > 0) {
            // check ownership or admin
            $stmt = $pdo->prepare(
                'SELECT user_id
                   FROM story_comments
                  WHERE id = :id AND story_id = :sid
                  LIMIT 1'
            );
            $stmt->execute([':id' => $commentId, ':sid' => $storyId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && ($row['user_id'] == $currentUserId || $isAdmin)) {
                $del = $pdo->prepare('DELETE FROM story_comments WHERE id = :id');
                $del->execute([':id' => $commentId]);
                $message = 'Comment deleted.';
            } else {
                $message = 'You cannot delete this comment.';
            }
        }
    }
}

// only bump views on first GET load, not on POST
if ($storyId && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['viewed_stories'])) {
        $_SESSION['viewed_stories'] = [];
    }

    if (empty($_SESSION['viewed_stories'][$storyId])) {
        $update = $pdo->prepare('UPDATE stories SET views = views + 1 WHERE id = :id');
        $update->execute([':id' => $storyId]);
        $_SESSION['viewed_stories'][$storyId] = true;
    }
}

// load story with author, now also grab image_path
if ($storyId) {
    $stmt = $pdo->prepare(
        'SELECT s.id,
                s.title,
                s.category,
                s.content,
                s.created_at,
                s.views,
                s.likes,
                s.image_path,
                u.display_name,
                u.username,
                u.avatar
           FROM stories s
           JOIN users u ON u.id = s.user_id
          WHERE s.id = :id AND s.is_published = 1
          LIMIT 1'
    );
    $stmt->execute([':id' => $storyId]);
    $story = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $story = false;
}

if ($story) {
    $authorName   = $story['display_name'] ?: $story['username'];
    $authorAvatar = $story['avatar'] ?: 'uploads/avatars/default.png';

    $wordCount = str_word_count(strip_tags($story['content']));
    $minutes   = max(1, ceil($wordCount / 200));

    // check like / bookmark state for current user
    if ($currentUserId) {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM story_likes WHERE user_id = :uid AND story_id = :sid LIMIT 1'
        );
        $stmt->execute([':uid' => $currentUserId, ':sid' => $storyId]);
        $liked = (bool)$stmt->fetch();

        $stmt = $pdo->prepare(
            'SELECT 1 FROM story_bookmarks WHERE user_id = :uid AND story_id = :sid LIMIT 1'
        );
        $stmt->execute([':uid' => $currentUserId, ':sid' => $storyId]);
        $bookmarked = (bool)$stmt->fetch();
    }

    // fetch comments for this story
    $cStmt = $pdo->prepare(
        'SELECT c.id, c.user_id, c.content, c.created_at,
                u.username, u.display_name, u.avatar
           FROM story_comments c
           JOIN users u ON u.id = c.user_id
          WHERE c.story_id = :sid
       ORDER BY c.created_at DESC'
    );
    $cStmt->execute([':sid' => $storyId]);
    $comments = $cStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $authorName   = null;
    $authorAvatar = null;
    $minutes      = null;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>
        <?php echo $story ? htmlspecialchars($story['title']) . ' | silent_evidence' : 'Story not found | silent_evidence'; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

        .story-image {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 16px;
            background-color: #020617;
            border: 1px solid #111827;
        }

        .story-image img {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
            display: block;
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

        .btn-outline-silent.active {
            background-color: #f60000;
            border-color: #f60000;
            color: #f9fafb;
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

        /* comments */
        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid #111827;
        }

        .comment-author {
            font-size: 0.85rem;
            color: #e5e7eb;
        }

        .comment-meta {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .comment-body {
            font-size: 0.9rem;
            color: #e5e7eb;
            margin-top: 4px;
            white-space: pre-wrap;
        }

        .comment-form textarea.form-control {
            background-color: #0f172a;
            border-color: #1f2937;
            color: #e5e7eb;
        }

        .comment-form textarea.form-control:focus {
            border-color: #f60000;
            box-shadow: none;
        }

        .btn-comment {
            background-color: #1f2937;
            color: #ffffff;
            border-radius: 999px;
            font-size: 0.85rem;
            padding: 6px 16px;
        }

        .btn-comment:hover {
            background-color: #111827;
        }
    </style>
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="page-wrapper">

        <?php if ($message): ?>
            <div class="alert alert-success py-2 small">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($commentError): ?>
            <div class="alert alert-danger py-2 small">
                <?php echo htmlspecialchars($commentError); ?>
            </div>
        <?php endif; ?>

        <?php if (!$story): ?>

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

                <?php if (!empty($story['image_path'])): ?>
                    <div class="story-image">
                        <img src="<?php echo htmlspecialchars($story['image_path']); ?>" alt="Story image">
                    </div>
                <?php endif; ?>

                <div class="story-category mb-1">
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
                        echo htmlspecialchars(strtoupper($cat));
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
                        Posted <?php echo date('d M Y', strtotime($story['created_at'])); ?>
                    </span>

                    <span class="dot"></span>

                    <span>
                        <?php echo $minutes; ?> min read
                    </span>
                </div>

                <div class="story-body">
                    <?php echo nl2br(htmlspecialchars($story['content'])); ?>
                </div>

                <div class="stats-row">
                    <div>
                        <span class="stat-chip">
                            👁
                            <span><?php echo (int)$story['views']; ?></span>
                            <span>views</span>
                        </span>

                        <span class="stat-chip ms-1">
                            ❤
                            <span><?php echo (int)$story['likes']; ?></span>
                            <span>likes</span>
                        </span>
                    </div>

                    <div class="action-row">
                        <?php if ($currentUserId): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="action" value="toggle_like">
                                <button
                                    type="submit"
                                    class="btn btn-outline-silent <?php echo $liked ? 'active' : ''; ?>">
                                    <?php echo $liked ? 'Liked' : 'Like'; ?>
                                </button>
                            </form>

                            <form method="post" class="d-inline">
                                <input type="hidden" name="action" value="toggle_bookmark">
                                <button
                                    type="submit"
                                    class="btn btn-outline-silent <?php echo $bookmarked ? 'active' : ''; ?>">
                                    <?php echo $bookmarked ? 'Bookmarked' : 'Bookmark'; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-silent">
                                Log in to like, bookmark, or comment
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </article>

            <?php if ($currentUserId): ?>
                <form method="post" class="comment-form mb-3 mt-4">
                    <input type="hidden" name="action" value="add_comment">
                    <div class="mb-2">
                        <label class="form-label small mb-1">Your remark</label>
                        <textarea
                            name="comment"
                            rows="3"
                            class="form-control"
                            placeholder="What did this story make you feel?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-comment">
                        Post comment
                    </button>
                </form>
            <?php else: ?>
                <p class="text-muted small mb-3 mt-4">
                    <a href="login.php">Log in</a> to leave a comment.
                </p>
            <?php endif; ?>

            <?php if (!$comments): ?>
                <p class="text-muted small mb-0">
                    No comments yet. Be the first.
                </p>
            <?php else: ?>
                <div class="mt-2">
                    <?php foreach ($comments as $c): ?>
                        <div class="d-flex mb-3">
                            <img
                                src="<?php echo htmlspecialchars($c['avatar'] ?: 'uploads/avatars/default.png'); ?>"
                                class="comment-avatar me-2"
                                alt="Avatar">

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="comment-author">
                                            <?php echo htmlspecialchars($c['display_name'] ?: $c['username']); ?>
                                        </div>
                                        <div class="comment-meta">
                                            <?php echo date('d M Y H:i', strtotime($c['created_at'])); ?>
                                        </div>
                                    </div>

                                    <?php if ($currentUserId && ($currentUserId == $c['user_id'] || $isAdmin)): ?>
                                        <form method="post" class="ms-2">
                                            <input type="hidden" name="action" value="delete_comment">
                                            <input type="hidden" name="comment_id" value="<?php echo (int)$c['id']; ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-link text-danger p-0 small">
                                                Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <div class="comment-body mt-1">
                                    <?php echo nl2br(htmlspecialchars($c['content'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>