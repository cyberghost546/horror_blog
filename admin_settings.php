<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../log_in.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Settings</title>
  <link rel="stylesheet" href="css/admin.css">
  <script>
    function toggleDarkMode() {
      document.body.classList.toggle("dark-mode");
    }
  </script>
  <style>
    .settings-panel { max-width: 600px; margin: 0 auto; }
    .dark-mode { background: #111; color: #eee; }
  </style>
</head>
<body>
  <?php include 'admin_sidebar.php'; ?>
  <main class="admin-content settings-panel">
    <h2 class="text-danger mb-4">Settings</h2>

    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" id="darkModeToggle" onclick="toggleDarkMode()">
      <label class="form-check-label" for="darkModeToggle">Toggle Dark Mode</label>
    </div>

    <div class="mb-3">
      <label for="announcement" class="form-label">Homepage Announcement</label>
      <textarea class="form-control bg-dark text-light border-danger" id="announcement" rows="4" placeholder="Enter message for users..."></textarea>
    </div>

    <button class="btn btn-danger">Save Settings</button>
  </main>
</body>
</html>
