<?php
session_start();
require 'include/db.php';
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
} /* HANDLE ACTIONS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_id'], $_POST['action'])) {
    $storyId = (int) $_POST['story_id'];
    $action = $_POST['action'];
    if ($storyId > 0) {
        if ($action === 'toggle_featured') {
            $stmt = $pdo->prepare('UPDATE stories SET is_featured = 1 - is_featured WHERE id = :id');
            $stmt->execute([':id' => $storyId]);
        } elseif ($action === 'toggle_publish') {
            $stmt = $pdo->prepare('UPDATE stories SET is_published = 1 - is_published WHERE id = :id');
            $stmt->execute([':id' => $storyId]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM stories WHERE id = :id');
            $stmt->execute([':id' => $storyId]);
        }
    }
    header('Location: stories_list.php?updated=1');
    exit;
} /* SEARCH + FILTER */
$search = trim($_GET['q'] ?? '');
$filter = $_GET['filter'] ?? 'all';
$params = [];
$sql = 'SELECT s.id, s.title, s.category, s.is_published, s.is_featured, s.views, s.likes, s.created_at, u.display_name, u.username FROM stories s JOIN users u ON u.id = s.user_id';
$clauses = [];
if ($search !== '') {
    $clauses[] = '(s.title LIKE :q OR u.display_name LIKE :q OR u.username LIKE :q)';
    $params[':q'] = '%' . $search . '%';
}
if ($filter === 'published') {
    $clauses[] = 's.is_published = 1';
} elseif ($filter === 'drafts') {
    $clauses[] = 's.is_published = 0';
} elseif ($filter === 'featured') {
    $clauses[] = 's.is_featured = 1';
}
if ($clauses) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}
$sql .= ' ORDER BY s.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll(); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Stories | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #020617;
            border-right: 1px solid #111827;
            padding: 16px 12px;
        }

        .sidebar-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            font-size: 0.9rem;
            color: #9ca3af;
            border-radius: 8px;
            text-decoration: none;
        }

        .side-link:hover,
        .side-link.active {
            background-color: #111827;
            color: #ffffff;
        }

        .side-link .icon {
            width: 18px;
            text-align: center;
        }

        .main-area {
            flex: 1;
            background-color: #020617;
        }

        .main-header {
            padding: 16px 24px;
            border-bottom: 1px solid #111827;
        }

        .main-content {
            padding: 20px 24px 32px;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
        }

        .card-dark {
            background-color: #020617;
            border: 1px solid #111827;
            border-radius: 14px;
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

        .table-dark-custom tbody tr td {
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

        .badge-published {
            border-radius: 999px;
            font-size: 0.7rem;
            padding: 3px 9px;
        }

        .filter-form .form-select,
        .filter-form .form-control {
            background-color: #020617;
            border-color: #374151;
            color: #e5e7eb;
        }

        .filter-form .form-select:focus,
        .filter-form .form-control:focus {
            border-color: #818cf8;
            box-shadow: none;
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

        .action-form button {
            font-size: 0.75rem;
            padding: 3px 8px;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    <div class="layout-wrapper">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-title">silent_evidence</div>

            <a href="dashboard.php" class="side-link">
                <span class="icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="stories_list.php" class="side-link active">
                <span class="icon">📖</span>
                <span>Stories</span>
            </a>
            <a href="users_list.php" class="side-link">
                <span class="icon">👥</span>
                <span>Users</span>
            </a>

            <div style="margin-top:auto">
                <a href="logout.php" class="side-link">
                    <span class="icon">⏻</span>
                    <span>Sign out</span>
                </a>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="main-area">

            <div class="main-header d-flex justify-content-between align-items-center">
                <h1 class="page-title mb-0">Stories</h1>

                <form method="get" class="d-flex gap-2 filter-form">
                    <input
                        type="text"
                        name="q"
                        class="form-control form-control-sm"
                        placeholder="Search title or author..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <select name="filter" class="form-select form-select-sm" style="max-width: 150px">
                        <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All</option>
                        <option value="published" <?php if ($filter === 'published') echo 'selected'; ?>>Published</option>
                        <option value="drafts" <?php if ($filter === 'drafts') echo 'selected'; ?>>Drafts</option>
                        <option value="featured" <?php if ($filter === 'featured') echo 'selected'; ?>>Featured</option>
                    </select>
                    <button class="btn btn-outline-silent btn-sm" type="submit">Apply</button>
                </form>
            </div>

            <div class="main-content">

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success py-2 small">
                        Story updated
                    </div>
                <?php endif; ?>

                <div class="card-dark">
                    <div class="card-dark-header d-flex justify-content-between align-items-center">
                        <span>Story list</span>
                        <a href="submit_story.php" class="btn btn-outline-silent btn-sm">New story</a>
                    </div>

                    <div class="card-dark-body">
                        <?php if (!$stories): ?>
                            <p class="small text-muted mb-0">No stories found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover align-middle table-dark-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Category</th>
                                            <th>Stats</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stories as $s): ?>
                                            <tr>
                                                <td><?php echo (int)$s['id']; ?></td>
                                                <td><?php echo htmlspecialchars($s['title']); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($s['display_name'] ?: $s['username']); ?>
                                                    <span class="text-muted small d-block">
                                                        @<?php echo htmlspecialchars($s['username']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $cat = $s['category'];
                                                    $label = 'Other';
                                                    $cls = 'badge-cat';
                                                    if ($cat === 'true') {
                                                        $label = 'True story';
                                                        $cls .= ' badge-cat-true';
                                                    } elseif ($cat === 'paranormal') {
                                                        $label = 'Paranormal';
                                                        $cls .= ' badge-cat-paranormal';
                                                    } elseif ($cat === 'urban') {
                                                        $label = 'Urban legend';
                                                        $cls .= ' badge-cat-urban';
                                                    } elseif ($cat === 'short') {
                                                        $label = 'Short nightmare';
                                                        $cls .= ' badge-cat-short';
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
                                                        <span class="badge bg-success badge-published">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary badge-published">Draft</span>
                                                    <?php endif; ?>
                                                    <?php if ($s['is_featured']): ?>
                                                        <span class="badge bg-warning text-dark badge-published ms-1">Featured</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <a href="story.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-outline-silent btn-sm">
                                                            View
                                                        </a>

                                                        <form method="post" class="d-inline action-form">
                                                            <input type="hidden" name="story_id" value="<?php echo (int)$s['id']; ?>">
                                                            <input type="hidden" name="action" value="toggle_publish">
                                                            <button class="btn btn-outline-silent btn-sm" type="submit">
                                                                <?php echo $s['is_published'] ? 'Unpublish' : 'Publish'; ?>
                                                            </button>
                                                        </form>

                                                        <form method="post" class="d-inline action-form">
                                                            <input type="hidden" name="story_id" value="<?php echo (int)$s['id']; ?>">
                                                            <input type="hidden" name="action" value="toggle_featured">
                                                            <button class="btn btn-outline-silent btn-sm" type="submit">
                                                                <?php echo $s['is_featured'] ? 'Unfeature' : 'Feature'; ?>
                                                            </button>
                                                        </form>

                                                        <form method="post" class="d-inline action-form" onsubmit="return confirm('Delete this story');">
                                                            <input type="hidden" name="story_id" value="<?php echo (int)$s['id']; ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button class="btn btn-outline-danger btn-sm" type="submit">
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

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>