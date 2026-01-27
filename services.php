<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Services | Nepal Civic</title>

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

body.page-services {
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
   HEADER (SAME AS LANDING)
=============================== */
header {
    background: var(--red);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

header::after {
    content: "";
    display: block;
    height: 4px;
    background: linear-gradient(to right, #fff, var(--red), #fff);
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
   FANCY PAGE HEADING
=============================== */
.page-heading {
    text-align: center;
    padding: 55px 20px 35px;
    background: #fff;
}

.page-heading h1 {
    font-size: 34px;
    color: var(--red);
    margin-bottom: 12px;
    position: relative;
    display: inline-block;
}

.page-heading h1::before,
.page-heading h1::after {
    content: "";
    position: absolute;
    top: 50%;
    width: 50px;
    height: 2px;
    background: var(--red);
}

.page-heading h1::before {
    left: -65px;
}

.page-heading h1::after {
    right: -65px;
}

.page-heading p {
    max-width: 700px;
    margin: 0 auto;
    font-size: 16px;
    line-height: 1.6;
    color: #555;
}

/* ===============================
   SERVICES SECTION
=============================== */
.section {
    max-width: 1100px;
    margin: 45px auto;
    padding: 20px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
    gap: 22px;
}

.service-card {
    background: #fff;
    padding: 26px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-top: 4px solid var(--red);
    transition: 0.3s;
}

.service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.18);
}

.service-card h3 {
    color: var(--red);
    margin-bottom: 8px;
    font-size: 20px;
}

.service-card p {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
}

/* ===============================
   FOOTER (SAME AS LANDING)
=============================== */
footer {
    background: linear-gradient(to right, #1e1e1e, #2a2a2a);
    color: #ccc;
    text-align: center;
    padding: 18px;
    font-size: 14px;
    border-top: 4px solid var(--red);
    margin-top: auto;
}
</style>
</head>

<body class="page-services">

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

<!-- ================= FANCY HEADING ================= -->
<section class="page-heading">
    <h1>Our Services</h1>
    <p>
        Civic services designed to help citizens report issues
        and improve public service delivery.
    </p>
</section>

<!-- ================= SERVICES CONTENT ================= -->
<section class="section">

    <div class="cards">

        <div class="service-card">
            <h3>💧 Water Supply</h3>
            <p>
                Report issues related to water leakage, shortages,
                pipeline damage, and drinking water availability.
            </p>
        </div>

        <div class="service-card">
            <h3>⚡ Electricity</h3>
            <p>
                Submit complaints regarding power outages,
                faulty street lights, and electrical hazards.
            </p>
        </div>

        <div class="service-card">
            <h3>🗑️ Waste & Sanitation</h3>
            <p>
                Report garbage collection issues, sanitation problems,
                and cleanliness concerns in your locality.
            </p>
        </div>

        <div class="service-card">
            <h3>🛣️ Road & Infrastructure</h3>
            <p>
                Raise complaints about damaged roads, drainage systems,
                and other public infrastructure issues.
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
