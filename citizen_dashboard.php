<?php
session_start();
include "includes/db.php";

/* ======================
   CITIZEN ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$citizen_id = (int) $_SESSION['citizen_id'];

/* ======================
   PAGE HANDLER
====================== */
$page = $_GET['page'] ?? 'dashboard';

/* ======================
   DASHBOARD STATS (CITIZEN ONLY)
====================== */
$totalIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM issue 
    WHERE citizen_id = $citizen_id
"))['total'];

$pendingIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM issue 
    WHERE citizen_id = $citizen_id AND status='pending'
"))['total'];

$assignedIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM issue 
    WHERE citizen_id = $citizen_id AND status='assigned'
"))['total'];

$resolvedIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM issue 
    WHERE citizen_id = $citizen_id AND status='resolved'
"))['total'];

/* ======================
   CHART DATA (DASHBOARD)
====================== */
$statusQ = mysqli_query($conn, "
    SELECT status AS label, COUNT(*) AS total
    FROM issue
    WHERE citizen_id = $citizen_id
    GROUP BY status
");

$deptQ = mysqli_query($conn, "
    SELECT department.department_name AS label, COUNT(issue.issue_id) AS total
    FROM issue
    JOIN department ON issue.department_id = department.department_id
    WHERE issue.citizen_id = $citizen_id
    GROUP BY department.department_name
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Citizen Dashboard | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        window.APP_ROLE = "citizen";
    </script>

    <script src="assets/main.js" defer></script>
</head>
<body>

<div class="dashboard-wrapper">
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php if ($page === 'dashboard') { ?>

    <!-- ================= DASHBOARD ================= -->
    <h2>Citizen Dashboard</h2>
    <p>Overview of your reported issues.</p>

    <!-- ================= STATS ================= -->
    <div class="dashboard-cards">
        <div class="stat-card">
            <h3>Total Issues</h3>
            <p><?= $totalIssues ?></p>
        </div>

        <div class="stat-card">
            <h3>Pending</h3>
            <p><?= $pendingIssues ?></p>
        </div>

        <div class="stat-card">
            <h3>Assigned</h3>
            <p><?= $assignedIssues ?></p>
        </div>

        <div class="stat-card">
            <h3>Resolved</h3>
            <p><?= $resolvedIssues ?></p>
        </div>
    </div>

    <!-- ================= ANALYTICS ================= -->
<h3 style="margin-top:30px;">Analytics</h3>

<div class="analytics-stack">

    <div class="stat-card chart-card">
        <h3>Issues by Status</h3>
        <canvas id="statusChart"></canvas>
    </div>
    <br>

    <br><div class="stat-card chart-card">
        <h3>Issues by Department</h3>
        <canvas id="deptChart"></canvas>
    </div>

</div>


<?php } elseif ($page === 'report') { ?>

    <!-- ================= REPORT ISSUE ================= -->
    <?php include "report_issue.php"; ?>

<?php } elseif ($page === 'my_issues') { ?>

    <!-- ================= MY ISSUES ================= -->
    <?php include "my_issues.php"; ?>

<?php } elseif ($page === 'notifications') { ?>

    <!-- ================= NOTIFICATIONS ================= -->
    <?php include "notifications.php"; ?>

<?php } elseif ($page === 'community_feed') { ?>

    <!-- ================= COMMUNITY FEED ================= -->
    <?php include "community_feed.php"; ?>

<?php } else { ?>

    <p>Invalid page requested.</p>

<?php } ?>

</div>
</div>

<!-- ================= CHART DATA ================= -->
<?php if ($page === 'dashboard') { ?>
<script>
const statusData = [
<?php while ($s = mysqli_fetch_assoc($statusQ)) {
    echo "{ label:'{$s['label']}', value:{$s['total']} },";
} ?>
];

const deptData = [
<?php while ($d = mysqli_fetch_assoc($deptQ)) {
    echo "{ label:'{$d['label']}', value:{$d['total']} },";
} ?>
];
</script>
<?php } ?>

</body>
</html>
