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

    $displayName = trim($firstName . ' ' . $lastName);
    $username    = strtolower(preg_replace('/\s+/', '', $displayName));

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $password2 === '') {
        $errors[] = 'All fields are required';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($password !== $password2) {
        $errors[] = 'Passwords do not match';
    }

    if (!$errors) {

        $check = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $check->execute([':e' => $email]);

        if ($check->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (username, email, password_hash, display_name, role)
                 VALUES (:u, :e, :p, :d, "user")'
            );

            $stmt->execute([
                ':u' => $username ?: $email,
                ':e' => $email,
                ':p' => $hash,
                ':d' => $displayName ?: $email
            ]);

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
    <title>Sign Up | Silent Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            font-family: system-ui, sans-serif;
            
        }

        .card-modern {
            width: 100%;
            max-width: 430px;
            margin: auto;
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 0 25px rgba(246, 0, 0, 0.25);
        }

        .card-modern h2 {
            color: #f60000;
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
        }

        .label-text {
            font-size: 0.85rem;
            color: #eaeaeaff;
        }

        .form-control {
            background-color: #1e293b;
            border-color: #334155;
             color: #ffffff !important;
        }


        .form-control:focus {
            background-color: #1e293b;
            border-color: #f60000;
            box-shadow: 0 0 0 2px rgba(246, 0, 0, 0.3);
        }

        .btn-red {
            background-color: #f60000;
            color: #0f172a;
            width: 100%;
            padding: 10px;
            border-radius: 999px;
            font-weight: 600;
        }

        .btn-red:hover {
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

        .google-btn {
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            padding: 10px;
            border-radius: 999px;
        }

        .google-btn:hover {
            background-color: #293548;
        }

        .small-text {
            text-align: center;
            color: #94a3b8;
            margin-top: 12px;
        }

        .small-text a {
            color: #f60000;
            text-decoration: none;
        }

        .small-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="mt-5 pt-4">
        <div class="card-modern">

            <h2>Create Your Account</h2>
            <p class="text-center text-secondary mb-3">Join Silent Evidence and share your horror stories</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <div class="row g-2">
                    <div class="col-6">
                        <label class="label-text">First Name</label>
                        <input type="text" name="first_name" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label class="label-text">Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="label-text">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="mt-3">
                    <label class="label-text">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mt-3">
                    <label class="label-text">Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control">
                </div>

                <button class="btn-red mt-4">Sign Up</button>

            </form>

            <div class="divider"><span>or</span></div>

            <button class="google-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="18" class="me-2">
                Continue with Google
            </button>

            <p class="small-text">
                Already have an account?
                <a href="login.php">Log in</a>
            </p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>