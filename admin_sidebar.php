<?php
         
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../log_in.php');
    exit();
}

$stmt = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-dark text-light">
  <div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>

    <main class="admin-content p-4 flex-grow-1">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger"><i class="fas fa-users me-2"></i>Registered Users</h2>
        <span class="badge bg-danger fs-6">Total: <?= count($users) ?></span>
      </div>

      <div class="table-responsive">
        <table class="table table-dark table-hover table-bordered align-middle rounded">
          <thead class="table-danger text-center">
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr class="text-center">
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                    <?= ucfirst($user['role']) ?>
                  </span>
                </td>
                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
