<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

/* UPDATE ROLE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    $uid     = (int) $_POST['user_id'];
    $newRole = $_POST['new_role'] === 'admin' ? 'admin' : 'user';

    $stmt = $pdo->prepare("UPDATE users SET role = :r WHERE id = :id");
    $stmt->execute([
        ':r'  => $newRole,
        ':id' => $uid
    ]);

    header("Location: users_list.php?updated=1");
    exit;
}

/* SEARCH + SORT */
$search = trim($_GET['q'] ?? '');
$sort   = $_GET['sort'] ?? '';

$params = [];

/* BASE QUERY */
$sql = 'SELECT id, username, display_name, email, role, created_at, last_login FROM users';

/* SEARCH FILTER */
if ($search !== '') {
    $sql .= ' WHERE username LIKE :q OR email LIKE :q OR display_name LIKE :q';
    $params[':q'] = '%' . $search . '%';
}

/* SORTING */
if ($sort === 'az') {
    $sql .= ' ORDER BY display_name ASC';
} elseif ($sort === 'za') {
    $sql .= ' ORDER BY display_name DESC';
} elseif ($sort === 'id_asc') {
    // ID 1 to 10
    $sql .= ' ORDER BY id ASC';
} elseif ($sort === 'id_desc') {
    // ID 10 to 1
    $sql .= ' ORDER BY id DESC';
} else {
    $sql .= ' ORDER BY created_at DESC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Users | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #000;
            color: #e5e7eb;
            font-family: system-ui;
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

        .side-link.active,
        .side-link:hover {
            background-color: #111827;
            color: #fff;
        }

        .main-area {
            flex: 1;
            background-color: #020617;
        }

        .main-header {
            padding: 16px 24px;
            border-bottom: 1px solid #111827;
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

        form.role-form select {
            background-color: #111827;
            color: #e5e7eb;
            border-color: #374151;
            border-radius: 6px;
            padding: 4px 6px;
        }

        form.role-form button {
            border-radius: 6px;
            padding: 3px 10px;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="layout-wrapper">

        <aside class="sidebar">
            <div class="sidebar-title">silent_evidence</div>

            <a href="dashboard.php" class="side-link"><span>🏠</span>Dashboard</a>
            <a href="stories_list.php" class="side-link"><span>📖</span>Stories</a>
            <a href="users_list.php" class="side-link active"><span>👥</span>Users</a>

            <div style="margin-top:auto">
                <a href="logout.php" class="side-link"><span>⏻</span>Sign out</a>
            </div>
        </aside>

        <div class="main-area">

            <div class="main-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h1 class="page-title">Users</h1>

                <form method="get" class="d-flex gap-2">
                    <input
                        type="text"
                        name="q"
                        class="form-control form-control-sm"
                        placeholder="Search users..."
                        value="<?php echo htmlspecialchars($search); ?>">

                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">

                    <button class="btn btn-outline-light btn-sm">Search</button>
                </form>

                <!-- SORT BUTTONS -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="?sort=az<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                        class="btn btn-sm btn-outline-light">Name A to Z</a>

                    <a href="?sort=za<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                        class="btn btn-sm btn-outline-light">Name Z to A</a>

                    <a href="?sort=id_asc<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                        class="btn btn-sm btn-outline-light">ID 1 to 10</a>

                    <a href="?sort=id_desc<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                        class="btn btn-sm btn-outline-light">ID 10 to 1</a>
                </div>
            </div>

            <div class="main-content p-3">

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success py-2 small">User role updated</div>
                <?php endif; ?>

                <div class="card-dark">
                    <div class="card-dark-header">User list</div>

                    <div class="card-dark-body">
                        <?php if (!$users): ?>
                            <p class="small text-muted">No users found.</p>
                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-dark table-hover align-middle table-dark-custom">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Display name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Change Role</th>
                                            <th>Joined</th>
                                            <th>Last login</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($users as $u): ?>
                                            <tr>
                                                <td><?php echo $u['id']; ?></td>
                                                <td><?php echo htmlspecialchars($u['display_name']); ?></td>
                                                <td>@<?php echo htmlspecialchars($u['username']); ?></td>
                                                <td><?php echo htmlspecialchars($u['email']); ?></td>

                                                <td>
                                                    <?php
                                                    echo $u['role'] === 'admin'
                                                        ? '<span class="badge bg-warning text-dark">Admin</span>'
                                                        : '<span class="badge bg-secondary">User</span>';
                                                    ?>
                                                </td>

                                                <td>
                                                    <form method="post" class="role-form d-flex gap-2">
                                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                        <select name="new_role">
                                                            <option value="user" <?php if ($u['role'] === 'user') echo 'selected'; ?>>User</option>
                                                            <option value="admin" <?php if ($u['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-outline-light">Update</button>
                                                    </form>
                                                </td>

                                                <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>

                                                <td>
                                                    <?php echo $u['last_login']
                                                        ? date('d M Y H:i', strtotime($u['last_login']))
                                                        : '<span class="text-muted small">Never</span>'; ?>
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

</body>

</html>