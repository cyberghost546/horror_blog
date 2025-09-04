<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: log_in.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "No story selected.";
    exit();
}

$storyId = (int)$_GET['id'];

// Fetch the story to edit
$stmt = $db->prepare("SELECT * FROM stories WHERE id = ?");
$stmt->execute([$storyId]);
$story = $stmt->fetch();

if (!$story) {
    echo "Story not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content = trim($_POST['content']);

    $update = $db->prepare("UPDATE stories SET title = ?, category = ?, content = ? WHERE id = ?");
    $update->execute([$title, $category, $content, $storyId]);

    header("Location: admin_stories.php?edited=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Story | Silent Evidence</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
  <div class="container mt-5">
    <h2>Edit Story</h2>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($story['title']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($story['category']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Content</label>
        <textarea name="content" class="form-control" rows="10" required><?= htmlspecialchars($story['content']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-danger">Update Story</button>
      <a href="admin_stories.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>
