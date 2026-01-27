<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role        = $_SESSION['user_role'] ?? '';
$currentPage = $_GET['page'] ?? 'dashboard';
$currentFile = basename($_SERVER['PHP_SELF']);

if (!function_exists('isActive')) {
    function isActive($condition) {
        return $condition ? 'active' : '';
    }
}
?>

<div class="sidebar">

    <div class="logo">
        <img src="assets/images/logo.png" alt="Nepal Civic">
        <h3>Nepal Civic</h3>
    </div>

    <!-- ================= ADMIN ================= -->
    <?php if ($role === 'admin') { ?>

        <a href="admin_dashboard.php"
           class="<?= isActive($currentPage === 'dashboard') ?>">
            Dashboard
        </a>

        <a href="admin_dashboard.php?page=issues"
           class="<?= isActive($currentPage === 'issues') ?>">
            Manage Issues
        </a>

        <a href="admin_dashboard.php?page=citizens"
           class="<?= isActive($currentPage === 'citizens') ?>">
            Manage Citizens
        </a>

        <a href="admin_dashboard.php?page=staff"
           class="<?= isActive($currentPage === 'staff') ?>">
            Manage Staff
        </a>

        <!-- <a href="admin_dashboard.php?page=notifications"
           class="<?= isActive($currentPage === 'notifications') ?>">
            Notifications
        </a> -->

    <?php } ?>

    <!-- ================= STAFF ================= -->
    <?php if ($role === 'staff') { ?>

        <a href="staff_dashboard.php"
           class="<?= isActive($currentPage === 'dashboard') ?>">
            Dashboard
        </a>

        <a href="staff_dashboard.php?page=assigned_issues"
           class="<?= isActive($currentPage === 'assigned_issues') ?>">
            Assigned Issues
        </a>

        <!-- <a href="staff_dashboard.php?page=notifications"
           class="
            Notifications
        </a> -->

    <?php } ?>

    <!-- ================= CITIZEN ================= -->
    <?php if ($role === 'citizen') { ?>

        <a href="citizen_dashboard.php"
            class="<?= isActive(
 $currentPage === 'dashboard' 
            && $currentFile === 'citizen_dashboard.php') ?>">
            Dashboard
        </a>

        <a href="citizen_dashboard.php?page=report"
           class="<?= isActive($currentPage === 'report') ?>">
            Report Issue
        </a>

        <a href="citizen_dashboard.php?page=my_issues"
           class="<?= isActive($currentPage === 'my_issues') ?>">
            My Issues
        </a>

        <a href="citizen_dashboard.php?page=notifications"
           class="<?= isActive($currentPage === 'notifications') ?>">
            Notifications
        </a>

        <a href="community_feed.php"
            class="<?= isActive($currentFile === 'community_feed.php') ?>">
            Community Feed
        </a>
        
    <?php } ?>

    <a href="logout.php" style="margin-top:20px;">
        Logout
    </a>

</div>
