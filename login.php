<?php
session_start();
require 'include/db.php';

// If already logged in redirect
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $errors[] = 'Login and password are required';
    }

    if (!$errors) {

        // Fetch user
        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash, display_name, avatar, role
               FROM users
              WHERE username = :login OR email = :login
              LIMIT 1'
        );
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();

        // Validate password
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Login or password is wrong';
        } else {

            // Update last login
            $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
            $stmt->execute([':id' => $user['id']]);

            // Set session
            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_name']   = $user['display_name'] ?: $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background-color: #ffffff;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.45);
            overflow: hidden;
        }

        .auth-card-topbar {
            height: 6px;
            background: #16a34a;
        }

        .auth-card-body {
            padding: 28px 28px 22px 28px;
        }

        .auth-title {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 4px;
            color: #0f172a;
        }

        .auth-subtitle {
            font-size: 0.85rem;
            text-align: center;
            color: #64748b;
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 0.8rem;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .form-control {
            font-size: 0.85rem;
            padding: 0.55rem 0.7rem;
            border-radius: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.18);
            border-color: #16a34a;
        }

        .btn-login {
            background-color: #1f2937;
            color: #ffffff;
            border-radius: 999px;
            font-size: 0.9rem;
            padding: 0.6rem;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #111827;
        }

        .auth-footer-text {
            font-size: 0.8rem;
            text-align: center;
            color: #6b7280;
            margin-top: 10px;
        }

        .auth-footer-text a {
            color: #0f172a;
            font-weight: 500;
            text-decoration: none;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 16px 0 10px 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider span {
            font-size: 0.75rem;
            color: #9ca3af;
            padding: 0 8px;
        }

        .btn-google {
            font-size: 0.85rem;
            border-radius: 999px;
            border-color: #e5e7eb;
            background-color: #ffffff;
            width: 100%;
            padding: 0.55rem;
        }
    </style>
</head>

<body>

<?php include 'header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card-topbar"></div>

        <div class="auth-card-body">
            <h1 class="auth-title">Log in</h1>
            <p class="auth-subtitle">Welcome back to silent_evidence</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger alert-small">
                    <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_GET['registered'])): ?>
                <div class="alert alert-success alert-small">
                    Account created. You can log in now.
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-2">
                    <label class="form-label">Username or email</label>
                    <input type="text" name="login" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['login'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-login">Log in</button>
            </form>

            <p class="auth-footer-text">
                Do not have an account yet?
                <a href="signup.php">Sign up</a>
            </p>

            <div class="divider"><span>or</span></div>

            <button type="button" class="btn btn-google">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="google-icon" alt="G">
                Log in with Google
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
