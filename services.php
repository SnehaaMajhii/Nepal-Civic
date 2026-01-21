<?php
// services.php
include 'includes/db.php'; // Included to fetch dynamic departments count if needed
$page_title = "Our Services - Nepal Civic";
include 'includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Public Services & Departments</h1>

    <div class="features" style="justify-content: center;">
        
        <div class="feature-card">
            <a href="water.php"><h3>🚰 Water Supply</h3></a>
            <p>Report pipeline leaks, contaminated water, or supply shortages directly to the Water Authority under Ministry of Water Supply.</p>
        </div>

        <div class="feature-card">
            <a href="road.php"><h3>🚧 Roads & Infrastructure</h3></a>
            <p>Potholes, broken pavement, and blocked drainage systems can be reported for quick maintenance to bodies under Ministry of Physical Infrastructure and Transport.</p>
        </div>

        <div class="feature-card">
            <a href="waste.php"><h3>♻️ Waste Management</h3></a>
            <p>Schedule pickups or report illegal dumping sites to keep our wards clean and green.</p>
        </div>

        <div class="feature-card">
            <a href="electricity.php"><h3>⚡ Electricity</h3></a>
            <p>Report fallen poles, broken streetlights, or dangerous wiring hazards immediately to bodies under Ministry of Energy, Water Resources and Irrigation</p>
        </div>

    </div>

    <div style="text-align: center; margin-top: 40px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3>Need service in your area?</h3>
        <p style="margin-bottom: 20px;">If you see an issue related to these services, don't ignore it.</p>
        <a href="report.php" class="btn-primary" style="max-width: 200px; margin: 0 auto;">Report an Issue</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>