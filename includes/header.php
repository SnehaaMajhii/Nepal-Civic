<?php
// includes/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($page_title)) {
    $page_title = "Nepal Civic - Community Issue Tracker";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <a href="index.php" class="nav-brand-link">
            <img src="assets/images/logo.png" alt="Logo" class="nav-logo">
            Nepal Civic
        </a>
    </div>

    <div class="nav-links">
        
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="services.php">Services</a>
        <a href="feed.php">Community Issues</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            
            <?php if ($_SESSION['role'] === 'citizen'): ?>
                <a href="report.php">Report Issue</a>
                <a href="citizen_dashboard.php">Dashboard</a>
                <a href="notification.php" class="nav-alert">Alerts</a>
            
            <?php elseif ($_SESSION['role'] === 'manager'): ?>
                <a href="department_dashboard.php">Ward Dashboard</a>
                <a href="notification.php" class="nav-alert">Alerts</a>
            
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Admin Panel</a>
            <?php endif; ?>

            <a href="logout.php" class="btn-logout">Logout</a>

        <?php else: ?>
            <a href="login.php" class="nav-login-btn">Login</a>
            <a href="register.php" class="nav-register-btn">Register</a>
        <?php endif; ?>
    </div>
</nav>