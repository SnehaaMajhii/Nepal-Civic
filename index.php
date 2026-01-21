<?php
session_start();
$page_title = "Welcome to Nepal Civic";
include 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <img src="assets/images/logo.png" alt="Nepal Civic" class="hero-logo-img">
        
        <h1 class="hero-title">Let's Build a Better Community</h1>
        
        <p class="hero-subtitle">
            Report local issues like potholes, water leaks, or waste management problems directly to your Ward Officer. Track progress in real-time.
        </p>
        
        <div class="btn-group">
            
            <a href="<?php echo isset($_SESSION['user_id']) ? 'report_issue.php' : 'login.php'; ?>" class="btn btn-red">
                Report an Issue Now
            </a>

            <a href="feed.php" class="btn btn-outline">
                View Community Issues
            </a>

        </div>

        <?php if(!isset($_SESSION['user_id'])): ?>
            <p style="font-size: 0.9em; color: #777; margin-top: 15px;">
                New here? <a href="register.php" style="color: var(--nepal-blue); font-weight: bold;">Register as a Citizen</a> to get started.
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="blue-divider"></div>

<div class="features-container">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <h3>1. Report</h3>
                <p>See a problem? Snap a photo or describe it. Select your Ward and Category instantly.</p>
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
</div>

<?php include 'includes/footer.php'; ?>