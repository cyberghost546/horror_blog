<?php
session_start();

// Kill all session data
$_SESSION = [];
session_unset();
session_destroy();

// Optional: clear any cookies if you set them
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to home or login page
header("Location: log_in.php");
exit();
