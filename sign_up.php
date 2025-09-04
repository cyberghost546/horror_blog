<?php
session_start();
require 'includes/db.php'; // Ensure this file defines $db = new PDO(...);

// Set timeout duration (e.g., 10 minutes = 600 seconds)
$timeout_duration = 600;

// Check if last activity is set
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than 10 minutes ago
    session_unset();     // Unset session variables
    session_destroy();   // Destroy the session
    header("Location: log_in.php?timeout=1");
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();


// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
  $redirect = ($_SESSION['role'] === 'admin') ? 'admin-dashboard.php' : 'user-profile.php';
  header("Location: $redirect");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = strtolower(trim($_POST['email']));
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];

  // Passwords must match
  if ($password !== $confirmPassword) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: signup.php");
    exit();
  }

  // Check if user already exists
  $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    $_SESSION['error'] = "Email already registered.";
    header("Location: signup.php");
    exit();
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Insert into DB
  $role = 'user';
  $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$username, $email, $hashedPassword, $role]);

  $userId = $db->lastInsertId();

  // Log them in
  $_SESSION['user_id'] = $userId;
  session_regenerate_id(true);
  $_SESSION['username'] = $username;
  $_SESSION['role'] = $role;

  // Redirect
  $redirect = ($_SESSION['role'] === 'admin') ? 'admin-dashboard.php' : 'profile.php';
  header("Location: $redirect");
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up | Silent Evidence</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />

  <style>
    body {
      background-color: #0d0d0d;
      color: #f8d7da;
      font-family: 'Segoe UI', sans-serif;
    }

    .signup-box {
      background-color: #1a1a1a;
      border: 1px solid #ff0000;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 0 120px rgba(255, 0, 0, 0.3);
    }

    .signup-title {
      font-family: 'Creepster', cursive;
      font-size: 2.5rem;
      color: #ff0000;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-control {
      background-color: #111;
      border: 1px solid #ff4d4d;
      color: #fff;
    }

    .form-control:focus {
      background-color: #111;
      border-color: #ff0000;
      color: #fff;
      box-shadow: none;
    }

    .btn-danger {
      background-color: #ff0000;
      border: none;
    }

    .btn-danger:hover {
      background-color: #cc0000;
    }

    .footer-text {
      text-align: center;
      font-size: 0.9rem;
      margin-top: 20px;
      color: #888;
    }
  </style>
</head>

<body>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="signup-box w-100" style="max-width: 400px;">
      <h1 class="signup-title">Sign Up</h1>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
          <?= $_SESSION['error'];
          unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Direct Sign-Up Form -->
      <form method="POST">
        <div class="mb-3">
          <label for="username" class="form-label text-danger">Username</label>
          <input type="text" class="form-control" id="username" name="username" required />
        </div>

        <div class="mb-3">
          <label for="email" class="form-label text-danger">Email</label>
          <input type="email" class="form-control" id="email" name="email" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label text-danger">Password</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>

        <div class="mb-3">
          <label for="confirm_password" class="form-label text-danger">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required />
        </div>

        <button type="submit" class="btn btn-danger w-100">Sign Up</button>
      </form>

      <div class="footer-text mt-4">
        Already have an account? <a href="log_in.php" class="text-danger text-decoration-none">Log In</a>
      </div>
    </div>
  </div>
</body>
</html>
