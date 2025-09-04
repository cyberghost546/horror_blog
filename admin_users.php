<?php
session_start();
require 'includes/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: log_in.php');
    exit();
}

// Set sort order (default: DESC)
$order = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'ASC' : 'DESC';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT id, username, email, role FROM users";
$params = [];

if ($search) {
    $sql .= " WHERE username LIKE :search OR email LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY id $order";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            color: #fff;
        }

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            margin-top: 20px;
        }

        .form-control,
        .form-select {
            background-color: #222;
            color: #fff;
            border: 1px solid #444;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .btn-outline-danger:hover {
            background-color: #ff0000;
            color: #fff;
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-danger text-center mb-4">User Management</h2>

        <a href="admin-dashboard.php" class="btn btn-outline-light mb-3">
            ← Back to Dashboard
        </a>

        <div class="search-bar mb-3">
            <form class="d-flex w-75" method="GET">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by username or email..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-outline-light">Search</button>
            </form>

            <div>
                <a href="?sort=asc<?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-outline-info btn-sm me-1">Sort ID ↑</a>
                <a href="?sort=desc<?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-outline-info btn-sm">Sort ID ↓</a>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form action="update_role.php" method="POST" class="d-flex">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="role" class="form-select me-2">
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="user_profile.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">View</a>
                                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>