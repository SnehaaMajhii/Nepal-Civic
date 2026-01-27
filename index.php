<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Nepal Civic | Citizen Complaint Portal</title>

<style>
/* ===============================
   NEPALI FLAG THEME
=============================== */
:root {
    --red: #c4161c;
    --dark-red: #a01216;
    --light-bg: #f7f7f7;
    --text-dark: #333;
}

/* ===============================
   GLOBAL
=============================== */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Roboto, Arial, sans-serif;
    background: var(--light-bg);
    color: var(--text-dark);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

a {
    text-decoration: none;
}

/* ===============================
   HEADER
=============================== */
header {
    background: var(--red);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.header-container {
    max-width: 1200px;
    margin: auto;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
}

.logo img {
    height: 42px;
    margin-right: 10px;
}

.logo span {
    font-size: 22px;
    font-weight: bold;
    color: #fff;
}

nav a {
    color: #fff;
    margin-left: 18px;
    font-weight: 500;
    position: relative;
}

nav a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 0%;
    height: 2px;
    background: #fff;
    transition: 0.3s;
}

nav a:hover::after {
    width: 100%;
}

/* ===============================
   HERO SECTION
=============================== */
.hero {
    background: linear-gradient(
        rgba(196,22,28,0.95),
        rgba(196,22,28,0.95)
    );
    color: #fff;
    padding: 90px 20px;
    text-align: center;
}

.hero h1 {
    font-size: 40px;
    margin-bottom: 15px;
}

.hero p {
    max-width: 750px;
    margin: 0 auto 25px;
    font-size: 18px;
    line-height: 1.6;
}

/* ===============================
   BUTTON
=============================== */
.primary-btn {
    background: #fff;
    color: var(--red);
    border: none;
    padding: 14px 28px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.primary-btn:hover {
    background: #f1f1f1;
    transform: translateY(-2px);
}

/* ===============================
   SECTIONS
=============================== */
.section {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
}

.section-title {
    text-align: center;
    color: var(--red);
    margin-bottom: 35px;
    font-size: 30px;
}

/* ===============================
   CARDS
=============================== */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap: 20px;
}

.issue-card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border-top: 5px solid var(--red);
    transition: 0.3s;
}

.issue-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.issue-card h3 {
    color: var(--red);
    margin-bottom: 10px;
}

/* ===============================
   FOOTER
=============================== */
footer {
    background: #222;
    color: #ccc;
    text-align: center;
    padding: 15px;
    font-size: 14px;
    margin-top: auto;
}
</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Nepal Civic Logo">
            </a>
            <span>Nepal Civic</span>
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="services.php">Services</a>
            <a href="community_feed.php">Community Feed</a>

            <?php if (!isset($_SESSION['user_role'])) { ?>
                <a href="login.php">Login</a>
            <?php } else { ?>
                <a href="logout.php">Logout</a>
            <?php } ?>
        </nav>
    </div>
</header>

<!-- ================= HERO ================= -->
<section class="hero">
    <h1>Report Local Issues, Build a Better Nepal</h1>
    <p>
        Nepal Civic is an online citizen complaint portal that enables residents
        to report local problems related to water supply, electricity,
        waste management, and road infrastructure.
    </p>

    <a href="community_feed.php">
        <button class="primary-btn">View Community Issues</button>
    </a>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="section">
    <h2 class="section-title">How Nepal Civic Works</h2>

    <div class="cards">
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
                overall performance, ensuring transparency.
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
<footer>
    © <?php echo date("Y"); ?> Nepal Civic | Citizen Complaint Portal
</footer>

</body>
</html>
