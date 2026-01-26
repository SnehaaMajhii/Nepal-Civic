<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services | Nepal Civic</title>
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

<!-- ================= SERVICES CONTENT ================= -->
<section style="max-width:1000px;margin:40px auto;padding:20px;">
    <h2 style="color:#c4161c;margin-bottom:25px;">Our Services</h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

        <div class="issue-card">
            <h3>💧 Water Supply</h3>
            <p>
                Report issues related to water leakage, shortage,
                pipeline damage, and drinking water availability.
            </p>
        </div>

        <div class="issue-card">
            <h3>⚡ Electricity</h3>
            <p>
                Submit complaints regarding power outages,
                faulty street lights, and electrical hazards.
            </p>
        </div>

        <div class="issue-card">
            <h3>🗑️ Waste & Sanitation</h3>
            <p>
                Report garbage collection issues, sanitation problems,
                and cleanliness concerns in your area.
            </p>
        </div>

        <div class="issue-card">
            <h3>🛣️ Road & Infrastructure</h3>
            <p>
                Raise complaints about damaged roads, drainage systems,
                and other public infrastructure issues.
            </p>
        </div>

    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer style="background:#333;color:#fff;text-align:center;padding:15px;position:fixed;bottom:0;left:0;width:100%;">
    <p>© <?php echo date("Y"); ?> Nepal Civic | Citizen Complaint Management System</p>
</footer>

</body>
</html>
