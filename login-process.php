<?php
session_start();
include 'track_visit.php';
require 'includes/db.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['username_email'];
    $password = $_POST['password'];

    // Remember Me: store in cookie
    if (isset($_POST['remember_me'])) {
        setcookie('username_email', $input, time() + (86400 * 30), "/"); // 30 days
    }

    // Check for username or email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$input, $input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: profile.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid login credentials.";
        header("Location: log_in.php");
        exit();
    }
} else {
    header("Location: log_in.php");
    exit();
}
