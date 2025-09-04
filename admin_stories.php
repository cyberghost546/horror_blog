<?php
session_start();
require 'includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM stories WHERE id = ?");
    $stmt->execute([$deleteId]);
    $_SESSION['msg'] = "Story deleted successfully.";
    header("Location: admin_stories.php");
    exit();
}

// Fetch all stories
$stmt = $db->query("SELECT * FROM stories ORDER BY created_at DESC");
$stories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Stories | Silent Evidence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d0d0d;
            color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .table {
            background-color: #1a1a1a;
            color: #f8f9fa;
        }

        .btn-sm {
            margin-right: 5px;
        }

        .alert {
            margin-top: 15px;
        }

        a {
            color: #ff4d4d;
        }

        a:hover {
            color: #ff1a1a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-danger text-center mb-4">Manage All Stories</h1>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped table-dark">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stories as $story): ?>
                    <tr>
                        <td><?= htmlspecialchars($story['id']) ?></td>
                        <td><?= htmlspecialchars($story['title']) ?></td>
                        <td><?= htmlspecialchars($story['category']) ?></td>
                        <td><?= htmlspecialchars($story['author']) ?></td>
                        <td><?= htmlspecialchars($story['views']) ?></td>
                        <td><?= htmlspecialchars($story['created_at']) ?></td>
                        <td>
                            <a href="edit_story.php?id=<?= $story['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="admin_stories.php?delete=<?= $story['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this story?');">Delete</a>
                            <a href="move_story.php?id=<?= $story['id'] ?>" class="btn btn-sm btn-secondary">Move</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($stories)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No stories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
