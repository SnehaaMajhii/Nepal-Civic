<?php
/* =========================
   SAFE SESSION START
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   CURRENT CONTEXT
========================= */
$role        = $_SESSION['user_role'] ?? '';
$currentPage = $_GET['page'] ?? 'dashboard';
$currentFile = basename($_SERVER['PHP_SELF']);

/* =========================
   ACTIVE HELPER (SAFE)
========================= */
if (!function_exists('isActive')) {
    function isActive($condition) {
        return $condition ? 'active' : '';
    }
}
?>

<div class="sidebar">

    <!-- LOGO -->
    <div class="logo">
        <img src="assets/images/logo.png" alt="Nepal Civic">
        <h3>Nepal Civic</h3>
    </div>

    <!-- ================= ADMIN ================= -->
    <?php if ($role === 'admin') { ?>

        <a href="admin_dashboard.php"
           class="<?= isActive($currentFile === 'admin_dashboard.php' && $currentPage === 'dashboard') ?>">
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

        <a href="admin_dashboard.php?page=notifications"
           class="<?= isActive($currentPage === 'notifications') ?>">
            Notifications
        </a>

    <?php } ?>

    <!-- ================= STAFF ================= -->
    <?php if ($role === 'staff') { ?>

        <a href="staff_dashboard.php"
           class="<?= isActive($currentFile === 'staff_dashboard.php' && $currentPage === 'dashboard') ?>">
            Dashboard
        </a>

        <a href="staff_dashboard.php?page=notifications"
           class="<?= isActive($currentPage === 'notifications') ?>">
            Notifications
        </a>

    <?php } ?>

    <!-- ================= CITIZEN ================= -->
    <?php if ($role === 'citizen') { ?>

        <a href="citizen_dashboard.php"
           class="<?= isActive($currentFile === 'citizen_dashboard.php' && $currentPage === 'dashboard') ?>">
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

    <!-- ================= LOGOUT ================= -->
    <a href="logout.php" class="logout-link" style="margin-top:20px;">
        Logout
    </a>

</div>
