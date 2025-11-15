<?php
session_start();
require 'include/db.php';

// Load TRUE category stories
$stmt = $pdo->prepare("
    SELECT s.id, s.title, s.content, s.category, s.views, s.likes, s.created_at,
           u.display_name, u.username
    FROM stories s
    JOIN users u ON u.id = s.user_id
    WHERE s.category = 'true' AND s.is_published = 1
    ORDER BY s.created_at DESC
");
$stmt->execute();
$stories = $stmt->fetchAll();
?>
<!doctype html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<title>True Horror Stories | silent_evidence</title>
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>

<style>
body {
    background:#020617;
    color:#e5e7eb;
    font-family:system-ui;
}
.story-card {
    background:#0b1120;
    border:1px solid #1e293b;
    border-radius:18px;
    padding:18px;
    transition:0.2s;
    cursor:pointer;
}
.story-card:hover {
    border-color:#f60000;
    transform:translateY(-3px);
}
.story-tag {
    font-size:11px;
    color:#f87171;
    text-transform:uppercase;
    letter-spacing:0.12em;
}
</style>
</head>

<body>

<?php include 'include/header.php'; ?>

<div class='container py-4'>
    <h1 class='mb-4'>True horror stories</h1>

    <div class='row g-4'>
        <?php if (!$stories): ?>
            <p>No true stories available yet.</p>
        <?php endif; ?>

        <?php foreach ($stories as $story): ?>
        <div class='col-md-4'>
            <a href='story.php?id=<?php echo $story['id']; ?>' class='text-decoration-none'>
                <div class='story-card h-100'>

                    <div class='story-tag mb-2'>TRUE</div>

                    <h4 class='text-light mb-1'>
                        <?php echo htmlspecialchars($story['title']); ?>
                    </h4>

                    <p class='text-secondary small mb-2'>
                        <?php echo htmlspecialchars(substr($story['content'],0,60)); ?>...
                    </p>

                    <p class='small mb-0 text-light'>
                        By <?php echo htmlspecialchars($story['display_name']); ?>
                    </p>

                    <div class='d-flex gap-3 mt-2 text-secondary small'>
                        <span>👁 <?php echo $story['views']; ?></span>
                        <span>❤ <?php echo $story['likes']; ?></span>
                    </div>

                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
