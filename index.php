<?php
// index.php
session_start();
$page_title = "Welcome to Nepal Civic";
include 'includes/header.php';
?>

<div class="hero">
    <img src="assets/images/logo.png" alt="Nepal Civic Logo" class="hero-logo">
    <h1>Let's Build a Better Community</h1>
    <p>Report local issues like potholes, water leaks, or waste management problems directly to your Ward Officer. Track progress in real-time.</p>
    
    <div class="action-buttons">
        <a href="report.php" class="btn-large btn-red">Report an Issue Now</a>
        
        <a href="feed.php" class="btn-large btn-blue">View Community Feed</a>
    </div>
    
    <?php if(!isset($_SESSION['user_id'])): ?>
        <p style="margin-top: 20px; font-size: 0.9rem;">
            New here? <a href="register.php">Register as a Citizen</a> to start reporting.
        </p>
    <?php endif; ?>
</div>

<div class="container">
    <div class="features">
        <div class="feature-card">
            <h3>1. Report</h3>
            <p>See a problem? Snap a photo or describe it. Select your Ward and Category.</p>
        </div>
        <div class="feature-card">
            <h3>2. Track</h3>
            <p>Your Ward Officer receives the alert instantly. Watch the status change from "Pending" to "Resolved".</p>
        </div>
        <div class="feature-card">
            <h3>3. Resolve</h3>
            <p>Get notified when the issue is fixed. Promoting transparency in local governance.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>