<?php
session_start();
require 'includes/db.php';

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : null;

if (!$category) {
    header("Location: stories.php");
    exit();
}

// Fetch stories in this category
$stmt = $db->prepare("SELECT id, title, content, created_at, views, likes 
                      FROM stories 
                      WHERE category = ? 
                      ORDER BY created_at DESC");
$stmt->execute([$category]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Silent Evidence - <?= htmlspecialchars($category) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

  <div class="container py-5">
    <h1 class="text-danger mb-4">Category: <?= htmlspecialchars($category) ?></h1>

    <?php if (count($stories) > 0): ?>
      <div class="row g-4">
        <?php foreach ($stories as $story): ?>
          <div class="col-md-6">
            <div class="card bg-secondary text-light shadow">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($story['title']) ?></h5>
                <p class="card-text"><?= htmlspecialchars(substr($story['content'], 0, 150)) ?>...</p>
                <a href="view_story.php?id=<?= $story['id'] ?>" class="btn btn-danger btn-sm">Read More</a>
              </div>
              <div class="card-footer d-flex justify-content-between text-muted">
                <small><?= date("M d, Y", strtotime($story['created_at'])) ?></small>
                <small><?= $story['views'] ?> views | <?= $story['likes'] ?> likes</small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">No stories found in this category.</p>
    <?php endif; ?>
  </div>

</body>
</html>
