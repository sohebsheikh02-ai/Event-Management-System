<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title ?? 'Event Management System'; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="container nav-inner">
    <a href="<?php echo BASE_URL; ?>/index.php" class="brand">🎫 EventHub</a>
    <ul class="nav-links">
      <li><a href="<?php echo BASE_URL; ?>/index.php">Events</a></li>
      <?php if (is_logged_in()): ?>
        <li><a href="<?php echo BASE_URL; ?>/user/dashboard.php">Dashboard</a></li>
        <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout (<?php echo sanitize($_SESSION['user_name']); ?>)</a></li>
      <?php else: ?>
        <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
        <li><a href="<?php echo BASE_URL; ?>/register.php" class="btn-nav">Sign Up</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/login.php" class="muted">Admin</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
<main class="container main">
