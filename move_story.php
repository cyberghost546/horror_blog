<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: log_in.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_id'], $_POST['new_category'])) {
    $storyId = (int)$_POST['story_id'];
    $newCategory = trim($_POST['new_category']);

    $stmt = $db->prepare("UPDATE stories SET category = ? WHERE id = ?");
    $stmt->execute([$newCategory, $storyId]);

    header("Location: admin_stories.php?moved=1");
    exit();
}

echo "Invalid request.";
?>
<form action="move_story.php" method="POST">
  <input type="hidden" name="story_id" value="<?= $story['id'] ?>">
  <select name="new_category">
    <option value="Haunted Houses">Haunted Houses</option>
    <option value="Urban Legends">Urban Legends</option>
    <option value="Roadside Horrors">Roadside Horrors</option>
  </select>
  <button type="submit" class="btn btn-sm btn-warning">Move</button>
</form>
