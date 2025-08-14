<?php
session_start();
require 'includes/db.php';

$user = null;
if (isset($_SESSION['user_id'])) {
  $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>navbar</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg shadow-sm py-3" style="background: linear-gradient(90deg, #000000ff, #000000ff);">
    <div class="container">
      <a class="navbar-brand fs-3 fw-bold" href="#" style="color: #ff0000; text-shadow: 0 0 60px #ff0000;">
        Silent Evidence
      </a>
      <button class="navbar-toggler border-danger" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon" style="filter: drop-shadow(0 0 2px #ff0000);"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 fs-6" style="text-transform: uppercase; letter-spacing: 1.2px;">
          <li class="nav-item"><a href="#" class="nav-link active" style="color: #ff0000; font-weight:600;">Home</a></li>
          <li class="nav-item"><a href="#" class="nav-link" style="color: #ff0000; font-weight:600;">Stories</a></li>
          <li class="nav-item"><a href="#" class="nav-link" style="color: #ff0000; font-weight:600;">Categories</a></li>
          <li class="nav-item"><a href="#" class="nav-link" style="color: #ff0000; font-weight:600;">Contact</a></li>
        </ul>

        <div class="d-flex gap-3 align-items-center">
          <?php if ($user): ?>
            <div class="d-flex align-items-center gap-2">
              <img src="uploads/<?= $user['profile_picture'] ?? 'default.png' ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; border: 2px solid #ff0000;">
              <span style="color: #ff0000; font-weight: 600;">Hello, <?= htmlspecialchars($user['username']) ?></span>
            </div>
            <a href="logout.php" class="btn px-4 fw-semibold rounded-3 shadow-sm" style="background-color: #ff0000; border: none; color: #fff; box-shadow: 0 0 8px #ff0000;">Logout</a>
          <?php else: ?>
            <a href="log_in.php" class="btn px-4 fw-semibold rounded-3 shadow-sm" style="background-color: #731919ff; border: none; color: #fff; box-shadow: 0 0 20px #ff0000;">Log In</a>
            <a href="sign_up.php" class="btn px-4 fw-semibold rounded-3 shadow-sm" style="background-color: #ff0000; border: none; color: #fff; box-shadow: 0 0 8px #ff0000;">Sign Up</a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </nav>
</body>

</html>