<?php
session_start();
require 'includes/db.php';
require 'sidebar.php';  // include sidebar file here

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: log_in.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Fetch user's stories
$stmt = $db->prepare("SELECT id, title, created_at FROM stories WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$stories = $stmt->fetchAll();

// Escape output function
function e($string) {
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>My Stories</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    /* Paste your sidebar and page styling here */
    /* Use your existing styles or link a CSS file */
  </style>
</head>

<body>
  <?php include 'sidebar.php'; ?>

  <main class="content" role="main" style="margin-left: 270px; padding: 40px;">
    <h1>My Stories</h1>
    <div class="card">
      <div class="card-header">Your Submitted Stories</div>
      <ul class="list-group list-group-flush">
        <?php if ($stories): ?>
          <?php foreach ($stories as $story): ?>
            <li class="list-group-item">
              <a href="story.php?id=<?= (int)$story['id'] ?>" style="color: inherit; text-decoration: none;">
                <?= e($story['title']) ?>
              </a>
              <small class="text-muted" style="float:right;"><?= e(date('Y-m-d', strtotime($story['created_at']))) ?></small>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="list-group-item text-danger">No stories submitted yet.</li>
        <?php endif; ?>
      </ul>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
