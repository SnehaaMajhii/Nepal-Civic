<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- ================= HEADER ================= -->
<header style="background:#c4161c;color:#fff;padding:15px 0;">
    <div style="max-width:1200px;margin:auto;display:flex;justify-content:space-between;align-items:center;padding:0 15px;">
        <div style="display:flex;align-items:center;">
            <img src="assets/images/logo.png" alt="Nepal Civic Logo" style="height:42px;margin-right:10px;">
            <span style="font-size:22px;font-weight:bold;">Nepal Civic</span>
        </div>

        <nav>
            <a href="index.php" style="color:#fff;margin-right:15px;">Home</a>
            <a href="about.php" style="color:#fff;margin-right:15px;">About</a>
            <a href="services.php" style="color:#fff;margin-right:15px;">Services</a>
            <a href="community_feed.php" style="color:#fff;margin-right:15px;">Community Feed</a>

            <?php if (!isset($_SESSION['user_role'])) { ?>
                <a href="login.php" style="color:#fff;">Login</a>
            <?php } else { ?>
                <a href="logout.php" style="color:#fff;">Logout</a>
            <?php } ?>
        </nav>
    </div>
</header>

<!-- ================= ABOUT CONTENT ================= -->
<section style="max-width:1000px;margin:40px auto;padding:20px;">
    <h2 style="color:#c4161c;margin-bottom:15px;">About Nepal Civic</h2>

    <p style="margin-bottom:15px;">
        Nepal Civic is a web-based citizen complaint management system designed
        to improve communication between citizens and local government bodies.
        The platform allows citizens to report civic issues in their locality
        and track their resolution progress transparently.
    </p>

    <p style="margin-bottom:15px;">
        The system promotes accountability by ensuring that every reported issue
        is verified by administrators and resolved by respective ward staff.
        All actions are logged and notifications are sent to keep users informed.
    </p>

    <p>
        Nepal Civic aims to strengthen civic engagement, improve public service
        delivery, and encourage active participation of citizens in community
        development.
    </p>
</section>

<!-- ================= FOOTER ================= -->
<footer style="background:#333;color:#fff;text-align:center;padding:15px;position:fixed;bottom:0;left:0;width:100%;">
    <p>© <?php echo date("Y"); ?> Nepal Civic | Citizen Complaint Management System</p>
</footer>

</body>
</html>
