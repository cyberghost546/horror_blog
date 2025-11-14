<?php
session_start();
require 'include/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $password2  = $_POST['password_confirm'] ?? '';

    // Build username and display name
    $displayName = trim($firstName . ' ' . $lastName);
    $username    = strtolower(preg_replace('/\s+/', '', $displayName));

    // Validation
    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $password2 === '') {
        $errors[] = 'All fields are required';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($password !== $password2) {
        $errors[] = 'Passwords do not match';
    }

    // Only insert when there are no errors
    if (!$errors) {

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e');
        $stmt->execute([':e' => $email]);

        if ($stmt->fetch()) {

            $errors[] = 'Email is already registered';

        } else {

            // Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, email, password_hash, display_name, role)
                 VALUES (:u, :e, :p, :d, :r)'
            );

            $stmt->execute([
                ':u' => $username ?: $email,
                ':e' => $email,
                ':p' => $hash,
                ':d' => $displayName ?: $email,
            ]);

            // Redirect to login
            header('Location: login.php?registered=1');
            exit;
        }
    }
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sign up silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif
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
            overflow: hidden
        }

        .auth-card-topbar {
            height: 6px;
            background: #f60000;
        }

        .auth-card-body {
            padding: 28px 28px 22px 28px
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
            box-shadow: 0 0 0 2px rgba(246, 0, 0, 0.18);
            border-color: #f60000;
        }

        .btn-signup {
            background-color: #1f2937;
            color: #ffffff;
            border-radius: 999px;
            font-size: 0.9rem;
            padding: 0.6rem;
            width: 100%
        }

        .btn-signup:hover {
            background-color: #111827;
            color: #ffffff;
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
            margin: 16px 0 10px 0
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

        .btn-google:hover {
            background-color: #f9fafb;
        }

        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 6px;
        }

        .alert-small {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
            border-radius: 10px;
            margin-bottom: 10px
        }
    </style>
</head>

<body>
    <?php
    include 'header.php'
    ?>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-topbar"></div>
            <div class="auth-card-body">
                <h1 class="auth-title">Sign Up</h1>
                <p class="auth-subtitle">
                    Let’s get you started with your horror account
                </p>

                <?php if ($errors): ?>
                    <div class="alert alert-danger alert-small">
                        <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>' ?>
                    </div>
                <?php endif ?>

                <form method="post" novalidate>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="mb-2">
                                <label class="form-label">First name</label>
                                <input
                                    type="text"
                                    name="first_name"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['first_name'] ?? '') ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label class="form-label">Last name</label>
                                <input
                                    type="text"
                                    name="last_name"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['last_name'] ?? '') ?>"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>"
                            required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input
                            type="password"
                            name="password_confirm"
                            class="form-control"
                            required>
                    </div>

                    <button type="submit" class="btn btn-signup">
                        Sign up
                    </button>
                </form>

                <p class="auth-footer-text">
                    Already have an account
                    <a href="login.php">Log in</a>
                </p>

                <div class="divider">
                    <span>or</span>
                </div>

                <button type="button" class="btn btn-google">
                    <img
                        src="https://www.svgrepo.com/show/475656/google-color.svg"
                        class="google-icon"
                        alt="G">
                    Sign up with Google
                </button>

                <p class="auth-footer-text mt-3">
                    By signing up you agree to our terms and privacy policy
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>