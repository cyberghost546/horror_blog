<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success = '';

// handle settings form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_latest   = isset($_POST['show_latest'])   ? 1 : 0;
    $show_popular  = isset($_POST['show_popular'])  ? 1 : 0;
    $show_featured = isset($_POST['show_featured']) ? 1 : 0;

    $stmt = $pdo->prepare(
        'UPDATE homepage_settings
            SET show_latest   = :sl,
                show_popular  = :sp,
                show_featured = :sf
          WHERE id = 1'
    );
    $stmt->execute([
        ':sl' => $show_latest,
        ':sp' => $show_popular,
        ':sf' => $show_featured,
    ]);

    $success = 'Homepage settings updated';
}

// fetch settings
$stmt = $pdo->query('SELECT * FROM homepage_settings WHERE id = 1 LIMIT 1');
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// fallback if table is empty
if (!$settings) {
    $settings = [
        'show_latest'   => 1,
        'show_popular'  => 1,
        'show_featured' => 1,
    ];
}

// fetch settings 
$stmt = $pdo->query('SELECT * FROM homepage_settings WHERE id = 1 LIMIT 1');
$settings = $stmt->fetch();

// quick stats 
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); 
$totalStories = $pdo->query('SELECT COUNT(*) FROM stories')->fetchColumn(); 
$totalFeatured = $pdo->query('SELECT COUNT(*) FROM stories WHERE is_featured = 1')->fetchColumn(); 
// top 5 popular stories by views 
$stmt = $pdo->query( 'SELECT title, views, likes, created_at FROM stories ORDER BY views DESC LIMIT 5' ); 
$popularStories = $stmt->fetchAll(); 
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Dashboard silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000ff;
            color: #ff0000ff;
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

        .nav-section-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            margin: 18px 0 6px 8px;
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

        .side-link span.icon {
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

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .badge-pill {
            border-radius: 999px;
        }

        .form-check-label {
            font-size: 0.9rem;
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

        .btn-outline-silent {
            border-color: #374151;
            color: #e5e7eb;
            font-size: 0.8rem;
        }

        .btn-outline-silent:hover {
            background-color: #111827;
            border-color: #4b5563;
            color: #ffffff;
        }
    </style>
</head>

<body> 
    <?php include 'include/header.php' ?> 

    <div class="layout-wrapper"> <!-- sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">Company name</div>
            <a href="dashboard.php" class="side-link active">
                <span class="icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="stories_list.php" class="side-link">
                <span class="icon">📖</span>
                <span>Stories</span>
            </a>
            <a href="users_list.php" class="side-link">
                <span class="icon">👥</span>
                <span>Users</span>
            </a>

            <div class="nav-section-label">Saved views</div>
            <a href="dashboard.php?range=month" class="side-link">
                <span class="icon">🗓️</span>
                <span>Current month</span>
            </a>
            <a href="dashboard.php?range=week" class="side-link">
                <span class="icon">📈</span>
                <span>Last week</span>
            </a>

            <div style="margin-top:auto">
                <div class="nav-section-label">Account</div>
                <a href="profile.php" class="side-link">
                    <span class="icon">⚙️</span>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="side-link">
                    <span class="icon">⏻</span>
                    <span>Sign out</span>
                </a>
            </div>

        </aside> <!-- main -->
        <div class="main-area">
            <div class="main-header d-flex justify-content-between align-items-center">
                <h1 class="page-title mb-0">Dashboard</h1>
                <div class="d-flex gap-2"> <button class="btn btn-outline-silent">Share</button> <button class="btn btn-outline-silent">Export</button> <button class="btn btn-outline-silent">This week</button> </div>
            </div>
            <div class="main-content">
                <?php if ($success): ?>
                    <div class="alert alert-success py-2 small">
                        <?php echo htmlspecialchars($success) ?>
                    </div>
                <?php endif ?>

                <!-- stats row -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header">Total users</div>
                            <div class="card-dark-body">
                                <div class="stat-number">
                                    <?php echo (int)$totalUsers ?>
                                </div>
                                <div class="text-muted small">Registered accounts</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header">Total stories</div>
                            <div class="card-dark-body">
                                <div class="stat-number">
                                    <?php echo (int)$totalStories ?>
                                </div>
                                <div class="text-muted small">All published stories</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header">Featured stories</div>
                            <div class="card-dark-body">
                                <div class="stat-number">
                                    <?php echo (int)$totalFeatured ?>
                                </div>
                                <div class="text-muted small">Shown as highlights</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- left column  popular stories -->
                    <div class="col-lg-7">
                        <div class="card-dark h-100">
                            <div class="card-dark-header d-flex justify-content-between align-items-center">
                                <span>Top popular stories</span>
                                <a href="stories_list.php" class="btn btn-outline-silent">Manage stories</a>
                            </div>
                            <div class="card-dark-body">
                                <?php if (!$popularStories): ?>
                                    <p class="small text-muted mb-0">No stories yet</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-dark table-hover table-sm align-middle table-dark-custom">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Views</th>
                                                    <th>Likes</th>
                                                    <th>Created</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($popularStories as $story): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($story['title']) ?></td>
                                                        <td><?php echo (int)$story['views'] ?></td>
                                                        <td><?php echo (int)$story['likes'] ?></td>
                                                        <td><?php echo date('d M Y', strtotime($story['created_at'])) ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <!-- right column  homepage settings -->
                    <div class="col-lg-5">
                        <div class="card-dark h-100">
                            <div class="card-dark-header">Homepage sections</div>
                            <div class="card-dark-body">
                                <p class="small text-muted">
                                    Turn sections on or off. Your home page will follow these settings.
                                </p>

                                <form method="post">
                                    <div class="form-check form-switch mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="show_latest"
                                            name="show_latest"
                                            <?php if ($settings['show_latest']) echo 'checked' ?>>
                                        <label class="form-check-label" for="show_latest">
                                            Show "Latest stories"
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="show_popular"
                                            name="show_popular"
                                            <?php if ($settings['show_popular']) echo 'checked' ?>>
                                        <label class="form-check-label" for="show_popular">
                                            Show "Popular stories"
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="show_featured"
                                            name="show_featured"
                                            <?php if ($settings['show_featured']) echo 'checked' ?>>
                                        <label class="form-check-label" for="show_featured">
                                            Show "Featured stories" section
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-outline-silent">
                                        Save settings
                                    </button>
                                </form>

                                <p class="small text-muted mt-3 mb-0">
                                    Use is_featured on a story to decide which ones appear in the featured block.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>