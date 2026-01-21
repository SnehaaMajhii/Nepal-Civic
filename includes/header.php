<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($page_title)) { $page_title = "Nepal Civic"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <div class="nav-container">
        <a href="index.php" class="logo">
            <img src="assets/images/logo.png" alt="Logo" style="height: 35px;">
            Nepal Civic
        </a>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="services.php">Services</a>
            <a href="feed.php">Community Issues</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'citizen'): ?>
                    <a href="citizen_dashboard.php">Dashboard</a>
                <?php elseif ($_SESSION['role'] === 'ward_member'): ?>
                    <a href="ward_dashboard.php">Ward Panel</a>
                <?php elseif ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php" style="border: 1px solid white; padding: 5px 10px; border-radius: 4px;">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" style="background: white; color: #DC143C; padding: 5px 12px; border-radius: 4px;">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>