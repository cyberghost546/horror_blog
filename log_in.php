<?php
session_start();
require 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    $errors[] = "Username and password are required.";
  } else {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      header("Location: index.php");
      exit();
    } else {
      $errors[] = "Incorrect username or password.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log In - Anime Blog</title>
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
    .login-container {
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
      color: #ff0000;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    label {
      color: #cc0000ff;
      font-weight: 600;
    }
    input.form-control {
      background-color: #000;
      border: 1.5px solid #ff0000;
      color: #ff0000;
      transition: border-color 0.3s ease;
    }
    input.form-control:focus {
      border-color: #cc0000ff;
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
      background-color: #cc0000;
      color: #fff;
    }
    .btn-link {
      color: #cc0000;
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

  <div class="login-container">
    <h2>Log In</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error) echo "<div>$error</div>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="log_in.php" novalidate>
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
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          required
          autocomplete="current-password"
        />
      </div>

      <button type="submit" class="btn btn-success">Log In</button>
      <a href="sign_up.php" class="btn-link">Don't have an account? Sign Up</a>
    </form>
  </div>

</body>
</html>
