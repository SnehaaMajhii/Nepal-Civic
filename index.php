<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nepal Civic | Citizen Complaint Portal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="min-height:100vh;display:flex;flex-direction:column;">


<!-- ================= HEADER ================= -->
<header style="background:#c4161c;color:#fff;padding:15px 0;">
    <div style="max-width:1200px;margin:auto;display:flex;justify-content:space-between;align-items:center;padding:0 15px;">

        <!-- Logo + Site Name -->
        <div style="display:flex;align-items:center;">
            <img src="assets/images/logo.png" alt="Nepal Civic Logo" style="height:42px;margin-right:10px;">
            <span style="font-size:22px;font-weight:bold;">Nepal Civic</span>
        </div>

        <!-- Navigation -->
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

<!-- ================= HERO SECTION ================= -->
<section style="background:#f5f5f5;padding:60px 20px;text-align:center;">
    <h1 style="color:#c4161c;margin-bottom:15px;">
        Report Local Issues, Build a Better Nepal
    </h1>

    <p style="max-width:750px;margin:0 auto 20px;">
        Nepal Civic is an online citizen complaint portal that enables residents
        to report local problems related to water supply, electricity,
        waste management, and road infrastructure.
    </p>

    <a href="community_feed.php">
        <button style="padding:12px 22px;">View Community Issues</button>
    </a>
</section>

<!-- ================= FEATURES ================= -->
<section style="max-width:1200px;margin:40px auto;padding:20px;">
    <h2 style="text-align:center;color:#c4161c;margin-bottom:30px;">
        How Nepal Civic Works
    </h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

        <div class="issue-card">
            <h3>📢 Report</h3>
            <p>
                Citizens can register, submit complaints, upload issue images,
                vote on urgent issues, and track resolution status.
            </p>
        </div>

        <div class="issue-card">
            <h3>👁️‍🗨️ Analyse</h3>
            <p>
                Issues are verified, managed and monitored for
                overall performance, and ensures transparency.
            </p>
        </div>

        <div class="issue-card">
            <h3>✅ Resolve</h3>
            <p>
                Ward staff receive assigned issues, resolve them on the field,
                and update the system with progress details.
            </p>
        </div>

    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer style="background:#333;color:#fff;text-align:center;padding:15px;margin-top:auto;">
    <p>
        © <?php echo date("Y"); ?> Nepal Civic |
        Citizen Complaint Management System
    </p>
</footer>

</body>
</html>
