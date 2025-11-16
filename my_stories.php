<?php session_start();
require 'include/db.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_id'], $_POST['action'])) {
    $storyId = (int) $_POST['story_id'];
    $action = $_POST['action'];
    if ($storyId > 0) {
        if ($action === 'toggle_publish') {
            $stmt = $pdo->prepare('UPDATE stories SET is_published = 1 - is_published WHERE id = :id AND user_id = :uid');
            $stmt->execute([':id' => $storyId, ':uid' => $userId,]);
            $success = 'Story visibility updated';
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM stories WHERE id = :id AND user_id = :uid');
            $stmt->execute([':id' => $storyId, ':uid' => $userId,]);
            $success = 'Story deleted';
        }
    }
    header('Location: my_stories.php?msg=' . urlencode($success));
    exit;
}
if (!empty($_GET['msg'])) {
    $success = $_GET['msg'];
}
$stmt = $pdo->prepare('SELECT id, title, category, is_published, is_featured, views, likes, created_at FROM stories WHERE user_id = :uid ORDER BY created_at DESC');
$stmt->execute([':uid' => $userId]);
$stories = $stmt->fetchAll(); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>My stories | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .card-dark {
            background-color: #020617;
            border-radius: 16px;
            border: 1px solid #111827;
        }

        .card-dark-header {
            border-bottom: 1px solid #111827;
            padding: 12px 16px;
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .card-dark-body {
            padding: 16px;
        }

        .table-dark-custom {
            font-size: 0.85rem;
        }

        .table-dark-custom thead {
            color: #9ca3af;
        }

        .table-dark-custom tbody td {
            border-color: #111827;
        }

        .badge-cat {
            border-radius: 999px;
            font-size: 0.7rem;
            padding: 4px 10px;
            text-transform: uppercase;
        }

        .badge-cat-true {
            background-color: #111827;
            color: #e5e7eb;
        }

        .badge-cat-paranormal {
            background-color: #22c55e;
            color: #022c22;
        }

        .badge-cat-urban {
            background-color: #eab308;
            color: #1f2937;
        }

        .badge-cat-short {
            background-color: #f97316;
            color: #1f2937;
        }

        .badge-status {
            border-radius: 999px;
            font-size: 0.7rem;
            padding: 3px 9px;
        }

        .btn-outline-silent {
            border-color: #4b5563;
            color: #e5e7eb;
            font-size: 0.8rem;
        }

        .btn-outline-silent:hover {
            background-color: #111827;
            border-color: #6b7280;
            color: #ffffff;
        }

        .empty-state {
            text-align: center;
            padding: 40px 10px 10px;
        }

        .empty-state h2 {
            font-size: 1.3rem;
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .action-form button {
            font-size: 0.75rem;
            padding: 3px 10px;
        }
    </style>

</head>

<body> <?php include 'include/header.php'; ?> <div class="page-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">My stories</h1>
                <p class="page-subtitle">Manage everything you have posted on Silent Evidence</p>
            </div>
            <a href="submit_story.php" class="btn btn-outline-silent">
                New story
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success py-2">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="card card-dark">
            <div class="card-dark-header d-flex justify-content-between align-items-center">
                <span>Your stories</span>
                <span class="small text-muted">
                    Total: <?php echo count($stories); ?>
                </span>
            </div>
            <div class="card-dark-body">

                <?php if (!$stories): ?>
                    <div class="empty-state">
                        <h2>No stories yet</h2>
                        <p>Start with your first creepy experience or nightmare and share it with the community.</p>
                        <a href="submit_story.php" class="btn btn-outline-silent mt-2">
                            Write a story
                        </a>
                    </div>
                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Stats</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stories as $s): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['title']); ?></td>
                                        <td>
                                            <?php
                                            $cat = $s['category'];
                                            $cls = 'badge-cat';
                                            $label = 'Other';
                                            if ($cat === 'true') {
                                                $cls .= ' badge-cat-true';
                                                $label = 'True story';
                                            } elseif ($cat === 'paranormal') {
                                                $cls .= ' badge-cat-paranormal';
                                                $label = 'Paranormal';
                                            } elseif ($cat === 'urban') {
                                                $cls .= ' badge-cat-urban';
                                                $label = 'Urban legend';
                                            } elseif ($cat === 'short') {
                                                $cls .= ' badge-cat-short';
                                                $label = 'Short nightmare';
                                            }
                                            ?>
                                            <span class="<?php echo $cls; ?>">
                                                <?php echo htmlspecialchars($label); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small text-muted">
                                                👁 <?php echo (int)$s['views']; ?>
                                                · ❤ <?php echo (int)$s['likes']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($s['is_published']): ?>
                                                <span class="badge bg-success badge-status">Published</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary badge-status">Draft</span>
                                            <?php endif; ?>
                                            <?php if ($s['is_featured']): ?>
                                                <span class="badge bg-warning text-dark badge-status ms-1">Featured</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap gap-1 justify-content-end">

                                                <a
                                                    href="story.php?id=<?php echo (int)$s['id']; ?>"
                                                    class="btn btn-outline-silent btn-sm"
                                                    target="_blank">
                                                    View
                                                </a>

                                                <a
                                                    href="edit_story.php?id=<?php echo (int)$s['id']; ?>"
                                                    class="btn btn-outline-silent btn-sm">
                                                    Edit
                                                </a>

                                                <form method="post" class="d-inline action-form">
                                                    <input type="hidden" name="story_id" value="<?php echo (int)$s['id']; ?>">
                                                    <input type="hidden" name="action" value="toggle_publish">
                                                    <button type="submit" class="btn btn-outline-silent btn-sm">
                                                        <?php echo $s['is_published'] ? 'Unpublish' : 'Publish'; ?>
                                                    </button>
                                                </form>

                                                <form
                                                    method="post"
                                                    class="d-inline action-form"
                                                    onsubmit="return confirm('Delete this story?');">
                                                    <input type="hidden" name="story_id" value="<?php echo (int)$s['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>