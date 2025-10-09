<?php
session_start();

// Base URL & Path
require_once '../hosttype.php';
require_once BASE_PATH.'config.php';
require_once BASE_PATH.'patterns/factory.php';
include_once BASE_PATH.'public/navbar.php'; // Include navbar

// Optional: get error from query string
$error = $_GET['error'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= BASE_URL."public/css/login.css"; ?>">
<title>NGO Login</title>
</head>
<body>
<div class="box">
    <h1>Login</h1>
    <?php if(!empty($error)) : ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="<?= BASE_URL."src/controller/authController.php"; ?>">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="<?= BASE_URL."public/signup.php"; ?>">Sign Up here</a></p>
</div>
</body>
</html>
