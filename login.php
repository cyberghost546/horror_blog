<?php
session_start();
require 'include/db.php';

// always define this so PHP does not complain
$errors = [];

// If already logged in redirect
if (!empty($_SESSION['user_id'])) {
    if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $errors[] = 'Login and password are required';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash, display_name, avatar, role
               FROM users
              WHERE username = :login OR email = :login
              LIMIT 1'
        );
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Login or password is wrong';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
            $stmt->execute([':id' => $user['id']]);

            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_name']   = $user['display_name'] ?: $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $_SESSION['user_role']   = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
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
            max-width: 430px;
            background-color: #0f172a;
            border-radius: 20px;
            border: 1px solid #1e293b;
            padding: 28px;
            box-shadow: 0 0 25px rgba(246, 0, 0, 0.25);
        }

        .auth-title {
            color: #f60000;
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
        }

        .auth-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 18px;
        }

        .label-text {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .form-control {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0 !important;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background-color: #1e293b;
            border-color: #f60000;
            box-shadow: 0 0 0 2px rgba(246, 0, 0, 0.3);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .btn-login {
            background-color: #f60000;
            color: #0f172a;
            width: 100%;
            padding: 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .btn-login:hover {
            background-color: #ca0000;
        }

        .divider {
            text-align: center;
            color: #64748b;
            margin: 18px 0;
        }

        .divider span {
            padding: 0 12px;
            background-color: #0f172a;
        }

        .divider::before,
        .divider::after {
            content: "";
            display: inline-block;
            width: 30%;
            border-bottom: 1px solid #1f2937;
            vertical-align: middle;
        }

        .google-btn {
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            padding: 10px;
            border-radius: 999px;
            font-size: 0.9rem;
        }

        .google-btn:hover {
            background-color: #293548;
        }

        .small-text {
            text-align: center;
            color: #94a3b8;
            margin-top: 12px;
            font-size: 0.85rem;
        }

        .small-text a {
            color: #f60000;
            text-decoration: none;
        }

        .small-text a:hover {
            text-decoration: underline;
        }

        .alert-small {
            font-size: 0.8rem;
            padding: 0.45rem 0.6rem;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="auth-wrapper">
        <div class="auth-card">

            <h1 class="auth-title">Log in</h1>
            <p class="auth-subtitle">
                Welcome back to Silent Evidence
            </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-small">
                    <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_GET['registered'])): ?>
                <div class="alert alert-success alert-small">
                    Account created, you can log in now.
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="label-text">Username or email</label>
                    <input
                        type="text"
                        name="login"
                        class="form-control"
                        placeholder="yourname or you@example.com"
                        value="<?php echo htmlspecialchars($_POST['login'] ?? '') ?>"
                        required>
                </div>

                <div class="mb-2">
                    <label class="label-text">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Your password"
                        required>
                </div>

                <button type="submit" class="btn btn-login mt-3">
                    Log in
                </button>
            </form>

            <p class="small-text">
                No account yet
                <a href="signup.php">Sign up</a>
            </p>

            <div class="divider">
                <span>or</span>
            </div>

            <button type="button" class="google-btn">
                <img
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    width="18"
                    class="me-2"
                    alt="G">
                Continue with Google
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>