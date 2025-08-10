<?php
session_start();
require 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $password_confirm = $_POST['password_confirm'];

  if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
    $errors[] = "All fields are required.";
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  }

  if ($password !== $password_confirm) {
    $errors[] = "Passwords do not match.";
  }

  if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
  }

  if (!$errors) {
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
      $errors[] = "Username or email already taken.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
      $stmt->execute([$username, $email, $hash]);

      $_SESSION['user_id'] = $db->lastInsertId();
      $_SESSION['username'] = $username;

      header("Location: index.php");
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - Anime Blog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #000;
      color: #ff0000;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .signup-container {
      background-color: #111;
      border: 2px solid #ff0000;
      padding: 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 0 120px #ff0000aa;
    }
    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #39ff14;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    label {
      color: #cc0000;
      font-weight: 600;
    }
    input.form-control {
      background-color: #000;
      border: 1.5px solid #cc0000;
      color: #ff0000;
      transition: border-color 0.3s ease;
    }
    input.form-control:focus {
      border-color: #cc0000;
      box-shadow: 0 0 8px #cc0000aa;
      color: #ff0000;
      background-color: #000;
    }
    .btn-success {
      background-color: #ff0000;
      border: none;
      color: #000;
      font-weight: 700;
      width: 100%;
      padding: 0.5rem;
      margin-top: 1rem;
      transition: background-color 0.3s ease;
    }
    .btn-success:hover {
      background-color: #cc0000ff;
      color: #fff;
    }
    .btn-link {
      color: #ff0000;
      display: block;
      text-align: center;
      margin-top: 1rem;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .btn-link:hover {
      color: #cc0000ff;
      text-decoration: underline;
    }
    .alert-danger {
      background-color: #330000;
      border-color: #660000;
      color: #ff4444;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>

  <div class="signup-container">
    <h2>Sign Up</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error) echo "<div>$error</div>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="sign_up.php" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input
          type="text"
          class="form-control"
          id="username"
          name="username"
          required
          value="<?=htmlspecialchars($_POST['username'] ?? '')?>"
          autocomplete="username"
        />
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
          type="email"
          class="form-control"
          id="email"
          name="email"
          required
          value="<?=htmlspecialchars($_POST['email'] ?? '')?>"
          autocomplete="email"
        />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password (min 6 chars)</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          required
          autocomplete="new-password"
        />
      </div>

      <div class="mb-3">
        <label for="password_confirm" class="form-label">Confirm Password</label>
        <input
          type="password"
          class="form-control"
          id="password_confirm"
          name="password_confirm"
          required
          autocomplete="new-password"
        />
      </div>

      <button type="submit" class="btn btn-success">Sign Up</button>
      <a href="log_in.php" class="btn-link">Already have an account? Log In</a>
    </form>
  </div>

</body>
</html>
