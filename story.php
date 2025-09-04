<?php
require 'includes/db.php';
session_start(); // make sure sessions work

// Validate story ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$storyId = (int)$_GET['id'];

// Increase views
$db->prepare("UPDATE stories SET views = views + 1 WHERE id = ?")->execute([$storyId]);

// Fetch story details
$stmt = $db->prepare("SELECT s.*, u.username AS author 
                      FROM stories s
                      LEFT JOIN users u ON s.user_id = u.id
                      WHERE s.id = ?");
$stmt->execute([$storyId]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
  echo "<h2 class='text-center text-danger'>Story not found</h2>";
  exit();
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
  if (isset($_SESSION['user_id']) && !empty(trim($_POST['comment']))) {
    $comment = trim($_POST['comment']);
    $userId = $_SESSION['user_id'];

    $insertComment = $db->prepare("INSERT INTO comments (story_id, user_id, comment) VALUES (?, ?, ?)");
    $insertComment->execute([$storyId, $userId, $comment]);

    header("Location: story.php?id=" . $storyId); // reload to prevent resubmit
    exit();
  }
}

// Fetch comments for this story
$commentsStmt = $db->prepare("SELECT c.*, u.username 
                              FROM comments c
                              JOIN users u ON c.user_id = u.id
                              WHERE c.story_id = ?
                              ORDER BY c.created_at DESC");
$commentsStmt->execute([$storyId]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
    crossorigin="anonymous">
  </script>
  <title><?= htmlspecialchars($story['title']) ?> - Silent Evidence</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

  <?php include 'partials/navbar.php'; ?>

  <div class="container py-5">
    <div class="text-center mb-4">
      <h1 class="text-danger"><?= htmlspecialchars($story['title']) ?></h1>
      <p class="text-muted">
        By <?= htmlspecialchars($story['author'] ?? 'Unknown') ?> |
        <?= date('F j, Y', strtotime($story['created_at'])) ?> |
        <?= $story['views'] + 1 ?> views
      </p>
    </div>

    <?php if (!empty($story['image'])): ?>
      <div class="text-center mb-4">
        <img src="<?= htmlspecialchars($story['image']) ?>" alt="<?= htmlspecialchars($story['title']) ?>" class="img-fluid rounded shadow">
      </div>
    <?php endif; ?>

    <div class="story-content mb-5">
      <?= nl2br(htmlspecialchars($story['content'])) ?>
    </div>

    <a href="index.php" class="btn btn-outline-danger mb-5">Back to Homepage</a>

    <!-- Comments Section -->
    <div class="comments-section mt-5">
      <h3 class="text-danger mb-4">Comments</h3>

      
      <!-- Comment Form -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <form id="commentForm" class="mb-4">
          <input type="hidden" name="story_id" value="<?= $storyId ?>">
          <div class="mb-3">
            <textarea name="comment" class="form-control bg-black text-light border-danger" rows="3" placeholder="Write your comment..." required></textarea>
          </div>
          <button type="submit" class="btn btn-danger">Post Comment</button>
        </form>
      <?php else: ?>
        <p><a href="log_in.php" class="text-danger">Log in</a> to post a comment.</p>
      <?php endif; ?>

      <!-- Display Comments -->
      <div id="commentsList">
        <?php if ($comments): ?>
          <?php foreach ($comments as $c): ?>
            <div class="mb-3 p-3 bg-black border border-danger rounded">
              <strong class="text-danger"><?= htmlspecialchars($c['username']) ?></strong>
              <span class="text-muted small"> - <?= date('F j, Y, g:i a', strtotime($c['created_at'])) ?></span>
              <p class="mt-2"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No comments yet. Be the first to comment!</p>
        <?php endif; ?>
      </div>


      <!-- Display Comments -->
      <?php if ($comments): ?>
        <?php foreach ($comments as $c): ?>
          <div class="mb-3 p-3 bg-black border border-danger rounded">
            <strong class="text-danger"><?= htmlspecialchars($c['username']) ?></strong>
            <span class="text-muted small"> - <?= date('F j, Y, g:i a', strtotime($c['created_at'])) ?></span>
            <p class="mt-2"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No comments yet. Be the first to comment!</p>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'partials/footer.php'; ?>

  <script>
    function loadComments() {
      fetch('fetch_comments.php?story_id=<?= $storyId ?>')
        .then(res => res.json())
        .then(data => {
          const commentsList = document.getElementById('commentsList');
          commentsList.innerHTML = '';

          data.forEach(c => {
            const div = document.createElement('div');
            div.classList.add('mb-3', 'p-3', 'bg-black', 'border', 'border-danger', 'rounded');
            div.innerHTML = `
                    <strong class="text-danger">${c.username}</strong>
                    <span class="text-muted small"> - ${new Date(c.created_at).toLocaleString()}</span>
                    <p class="mt-2">${c.comment.replace(/\n/g, '<br>')}</p>
                `;
            commentsList.appendChild(div);
          });
        })
        .catch(err => console.error('Error:', err));
    }

    // Load comments every 5 seconds
    setInterval(loadComments, 5000);

    // Also load immediately on page load
    document.addEventListener('DOMContentLoaded', loadComments);

    // Keep instant post logic from before
    document.getElementById('commentForm')?.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('add_comment.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          loadComments();
          this.reset();
        })
        .catch(err => console.error('Error:', err));
    });
  </script>


</body>

</html>