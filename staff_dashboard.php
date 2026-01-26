<?php
session_start();
include "includes/db.php";

/* ======================
   STAFF ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staff_id = (int) $_SESSION['staff_id'];
$ward_id  = (int) $_SESSION['ward_id'];

$page = $_GET['page'] ?? 'dashboard';

/* ======================
   DASHBOARD STATS (WARD)
====================== */
$totalIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id
"))['total'];

$assignedIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id AND status = 'assigned'
"))['total'];

$pendingIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id AND status = 'pending'
"))['total'];

$resolvedIssues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id AND status = 'resolved'
"))['total'];

/* ======================
   CHART DATA
====================== */
$statusQ = mysqli_query($conn, "
    SELECT status AS label, COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id
    GROUP BY status
");

$urgencyQ = mysqli_query($conn, "
    SELECT urgency_level AS label, COUNT(*) AS total
    FROM issue
    WHERE ward_id = $ward_id
    GROUP BY urgency_level
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ward Staff Dashboard | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/main.js" defer></script>
</head>
<body>

<div class="dashboard-wrapper">
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php if ($page === 'dashboard') { ?>

<h2>Ward Staff Dashboard</h2>
<p>Issues assigned to your ward.</p>

<!-- STAT CARDS -->
<div class="dashboard-cards">
    <div class="stat-card"><h3>Total Issues</h3><p><?= $totalIssues ?></p></div>
    <div class="stat-card"><h3>Assigned</h3><p><?= $assignedIssues ?></p></div>
    <div class="stat-card"><h3>Pending</h3><p><?= $pendingIssues ?></p></div>
    <div class="stat-card"><h3>Resolved</h3><p><?= $resolvedIssues ?></p></div>
</div>

<h3 style="margin-top:30px;">Analytics</h3>

<div class="dashboard-cards">
    <div class="stat-card">
        <h3>Issues by Status</h3>
        <canvas id="statusChart"></canvas>
    </div>

    <div class="stat-card">
        <h3>Issues by Urgency</h3>
        <canvas id="urgencyChart"></canvas>
    </div>
</div>

<?php } elseif ($page === 'assigned_issues') { ?>

<?php
$issues = mysqli_query($conn, "
    SELECT issue.*, citizen.full_name
    FROM issue
    JOIN citizen ON issue.citizen_id = citizen.citizen_id
    WHERE issue.ward_id = $ward_id AND issue.status = 'assigned'
    ORDER BY issue.expected_resolution_date ASC
");
?>

<h2>Assigned Issues</h2>

<?php if (mysqli_num_rows($issues) === 0) { ?>
    <div class="empty-state">No assigned issues.</div>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($issues)) { ?>
<div class="issue-card">
    <h3><?= htmlspecialchars($row['title']) ?></h3>

    <p><b>Citizen:</b> <?= htmlspecialchars($row['full_name']) ?></p>

    <p>
        <b>Urgency:</b>
        <span class="urgency-<?= $row['urgency_level'] ?>">
            <?= ucfirst($row['urgency_level']) ?>
        </span>
    </p>

    <p><b>Expected Resolution:</b>
        <?= date("d M Y", strtotime($row['expected_resolution_date'])) ?>
    </p>

    <div class="issue-actions">
        <a href="resolve_issue.php?id=<?= $row['issue_id'] ?>">
            <button>Resolve</button>
        </a>
    </div>
</div>
<?php } ?>

<?php } elseif ($page === 'notifications') {

    include "notifications.php";

} ?>

</div>
</div>

<?php if ($page === 'dashboard') { ?>
<script>
const statusData = [
<?php while ($s = mysqli_fetch_assoc($statusQ)) {
    echo "{ label:'{$s['label']}', value:{$s['total']} },";
} ?>
];

const urgencyData = [
<?php while ($u = mysqli_fetch_assoc($urgencyQ)) {
    echo "{ label:'{$u['label']}', value:{$u['total']} },";
} ?>
];

drawPieChart("statusChart", statusData);
drawBarChart("urgencyChart", urgencyData);
</script>
<?php } ?>

</body>
</html>
