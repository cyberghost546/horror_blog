<?php
session_start();
require 'includes/db.php';

// Fetch stories
$stmt = $db->query("SELECT id, title, content, category, created_at, views FROM stories ORDER BY created_at DESC");
$stories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Silent Evidence - Stories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #111;
      color: #eee;
      font-family: 'Arial', sans-serif;
    }
    .story-card {
      background-color: #1c1c1c;
      border: 1px solid #ff0000;
      border-radius: 12px;
      padding: 20px;
      transition: transform 0.2s ease;
    }
    .story-card:hover {
      transform: scale(1.02);
      box-shadow: 0 0 12px #ff0000;
    }
    .story-title {
      color: #ff0000;
      font-weight: bold;
    }
    .read-more {
      color: #fff;
      background: #ff0000;
      border: none;
      padding: 8px 14px;
      border-radius: 8px;
      text-decoration: none;
      transition: background 0.2s ease;
    }
    .read-more:hover {
      background: #cc0000;
    }
    .meta {
      font-size: 0.9em;
      color: #aaa;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h1 class="text-center mb-4" style="color:#ff0000;">All Horror Stories</h1>

    <div class="row g-4">
      <?php foreach ($stories as $story): ?>
        <div class="col-md-6">
          <div class="story-card h-100">
            <h3 class="story-title"><?= htmlspecialchars($story['title']) ?></h3>
            <p class="meta">Category: <?= htmlspecialchars($story['category']) ?> | Views: <?= $story['views'] ?> | <?= date("M d, Y", strtotime($story['created_at'])) ?></p>
            <p><?= substr(htmlspecialchars($story['content']), 0, 150) ?>...</p>
            <a href="view_story.php?id=<?= $story['id'] ?>" class="read-more">Read More</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
