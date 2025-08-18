<?php
session_start();
require 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$story_id = (int)$_GET['id'];

// Fetch story
$stmt = $db->prepare("
    SELECT s.*, u.username, u.profile_picture,
           (SELECT COUNT(*) FROM story_votes sv WHERE sv.story_id = s.id AND sv.vote_type='like') AS likes,
           (SELECT COUNT(*) FROM comments c WHERE c.story_id = s.id) AS comments
    FROM stories s
    JOIN users u ON s.user_id = u.id
    WHERE s.id = ?
");
$stmt->execute([$story_id]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
    echo "Story not found.";
    exit();
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    if ($comment !== '') {
        $stmt = $db->prepare("INSERT INTO comments (story_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$story_id, $_SESSION['user_id'], $comment]);
        header("Location: view_story.php?id=" . $story_id);
        exit();
    }
}

// Fetch comments
$stmt = $db->prepare("
    SELECT c.comment, c.created_at, u.username, u.profile_picture
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.story_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$story_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($story['title']); ?> - Silent Evidence</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #111;
      color: #eee;
      font-family: 'Segoe UI', sans-serif;
    }
    .story-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .story-header h1 {
      color: #ff4444;
      text-shadow: 0 0 15px #ff0000;
    }
    .story-img {
      max-height: 400px;
      object-fit: cover;
      border-radius: 15px;
      box-shadow: 0 0 25px #ff0000;
    }
    .comment-box {
      background: #1b1b1b;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }
    .comment-box img {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      object-fit: cover;
      margin-right: 10px;
    }
    .btn-like {
      background: #ff0000;
      border: none;
      color: #fff;
      padding: 6px 12px;
      border-radius: 6px;
      transition: 0.3s;
    }
    .btn-like:hover {
      box-shadow: 0 0 15px #ff0000;
    }
  </style>
</head>
<body class="container py-5">

  <div class="story-header">
    <h1><?php echo htmlspecialchars($story['title']); ?></h1>
    <p class="text-muted">
      By <img src="<?php echo $story['profile_picture'] ?: 'default-avatar.png'; ?>" width="30" height="30" class="rounded-circle">
      <?php echo htmlspecialchars($story['username']); ?> 
      • <?php echo $story['created_at']; ?>
    </p>
  </div>

  <?php if ($story['image']): ?>
    <div class="text-center mb-4">
      <img src="<?php echo $story['image']; ?>" class="story-img w-100">
    </div>
  <?php endif; ?>

  <div class="mb-4">
    <p><?php echo nl2br(htmlspecialchars($story['content'])); ?></p>
  </div>

  <div class="d-flex gap-3 mb-4">
    <button class="btn-like"><i class="fas fa-heart"></i> <?php echo $story['likes']; ?> Likes</button>
    <span><i class="fas fa-comment"></i> <?php echo $story['comments']; ?> Comments</span>
  </div>

  <h3 class="mb-3">Comments</h3>
  <?php if (isset($_SESSION['user_id'])): ?>
    <form method="POST" class="mb-4">
      <textarea name="comment" class="form-control bg-dark text-light mb-2" rows="3" placeholder="Write a comment..."></textarea>
      <button type="submit" class="btn btn-danger">Post Comment</button>
    </form>
  <?php else: ?>
    <p><a href="log_in.php" class="text-danger">Log in</a> to leave a comment.</p>
  <?php endif; ?>

  <?php foreach ($comments as $c): ?>
    <div class="comment-box d-flex">
      <img src="<?php echo $c['profile_picture'] ?: 'default-avatar.png'; ?>">
      <div>
        <strong class="text-danger"><?php echo htmlspecialchars($c['username']); ?></strong>
        <p><?php echo htmlspecialchars($c['comment']); ?></p>
        <small class="text-muted"><?php echo $c['created_at']; ?></small>
      </div>
    </div>
  <?php endforeach; ?>

</body>
</html>
