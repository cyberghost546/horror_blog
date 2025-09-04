<?php

session_start();
require 'includes/db.php'; // This should define $db

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch the user
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login success: set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Save to user_sessions for tracking
        $track = $db->prepare("REPLACE INTO user_sessions (user_id, last_active) VALUES (?, NOW())");
        $track->execute([$user['id']]);

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: profile.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Silent Evidence</title>
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

    .login-box {
      background-color: #1a1a1a;
      border: 1px solid #ff0000;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 0 70px rgba(255, 0, 0, 0.3);
    }

    .login-title {
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
    <div class="login-box w-100" style="max-width: 400px;">
      <h1 class="login-title">Log In to the Darkness</h1>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
          <?= $_SESSION['error'];
          unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="login-process.php" method="POST">
        <div class="mb-3">
          <label for="username_email" class="form-label text-danger">Username or Email</label>
          <input type="text" class="form-control" id="username_email" name="username_email"
            value="<?= $_COOKIE['username_email'] ?? '' ?>" required />

        </div>

        <div class="mb-3">
          <label for="password" class="form-label text-danger">Password</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>

        <button type="submit" class="btn btn-danger w-100">Log In</button>
        <br>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me" />
          <label class="form-check-label text-danger" for="rememberMe">Remember Me</label>
        </div>

      </form>


      <div class="footer-text mt-4">
        New here? <a href="signup.php" class="text-danger text-decoration-none">Create Account</a>
      </div>
    </div>
  </div>
</body>

</html>