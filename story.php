<?php
session_start();
require 'includes/db.php'; // DB connection

// Get story ID from URL
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$story_id = intval($_GET['id']);

// Fetch story
$stmt = $db->prepare("SELECT * FROM stories WHERE id = ?");
$stmt->execute([$story_id]);
$story = $stmt->fetch();

if (!$story) {
    echo "Story not found.";
    exit();
}

// Increment views
$db->prepare("UPDATE stories SET views = views + 1 WHERE id = ?")->execute([$story_id]);

// Fetch comments
$stmt = $db->prepare("
    SELECT c.comment, u.username, u.profile_picture, c.created_at
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.story_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$story_id]);
$comments = $stmt->fetchAll();

// Handle comment submission
if (isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("INSERT INTO comments (story_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$story_id, $_SESSION['user_id'], $_POST['comment']]);
    header("Location: story.php?id=$story_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($story['title']) ?> - Silent Evidence</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body style="background:#000; color:#fff;">

<div class="container my-4">

    <!-- Story Header -->
    <h1 class="text-danger"><?= htmlspecialchars($story['title']) ?></h1>
    <p class="text-muted">By <?= htmlspecialchars($story['author']) ?> | <?= $story['views'] ?> views</p>
    <img src="<?= $story['image'] ?? 'https://placehold.co/800x400?text=No+Image' ?>" class="img-fluid mb-3">

    <!-- Story Content -->
    <div class="story-content mb-5">
        <p><?= nl2br(htmlspecialchars($story['content'])) ?></p>
    </div>

    <!-- Comments Section -->
    <section>
        <h3 class="text-danger">Comments</h3>
        <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" class="mb-3">
            <textarea name="comment" class="form-control mb-2" rows="3" placeholder="Write your comment..." required></textarea>
            <button class="btn btn-danger">Submit Comment</button>
        </form>
        <?php else: ?>
        <p class="text-muted">You must <a href="log_in.php" class="text-danger">log in</a> to comment.</p>
        <?php endif; ?>

        <ul class="list-group bg-dark text-white">
        <?php foreach($comments as $c): ?>
            <li class="list-group-item bg-dark text-white d-flex align-items-start gap-2">
                <img src="uploads/<?= $c['profile_picture'] ?? 'default.png' ?>" alt="Profile" width="40" class="rounded-circle">
                <div>
                    <strong class="text-danger"><?= htmlspecialchars($c['username']) ?></strong>
                    <small class="text-muted"><?= $c['created_at'] ?></small>
                    <p><?= htmlspecialchars($c['comment']) ?></p>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
