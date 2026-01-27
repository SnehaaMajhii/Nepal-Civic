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
<!-- ================= STAFF FILTERS ================= -->
<div class="issue-filters">
    <input
        type="text"
        id="staffSearchTitle"
        placeholder="Search by title"
    >

    <select id="staffFilterDepartment">
        <option value="">All Departments</option>
        <?php
        $dQ = mysqli_query($conn, "SELECT department_id, department_name FROM department");
        while ($d = mysqli_fetch_assoc($dQ)) {
            echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
        }
        ?>
    </select>

    <select id="staffFilterStatus">
        <option value="">All Status</option>
        <option value="assigned">Assigned</option>
        <option value="resolved">Resolved</option>
    </select>
</div>


<div class="table-card">
<table class="styled-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Citizen</th>
            <th>Department</th>
            <th>Urgency</th>
            <th>Expected Resolution</th>
            <th>Date Issued</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="staffIssueTableBody">
        <tr>
            <td colspan="8" style="text-align:center; padding:20px;">
                Loading issues...
            </td>
        </tr>
    </tbody>
</table>
</div>

<div id="pagination" class="pagination"></div>
<!-- ================= ISSUE DETAIL MODAL ================= -->
<div id="issueModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <span class="modal-close">&times;</span>

        <h2 id="m_title"></h2>

        <p><b>Department:</b> <span id="m_department"></span></p>
        <p><b>Status:</b> <span id="m_status"></span></p>
        <p><b>Urgency:</b> <span id="m_urgency"></span></p>
        <p><b>Ward:</b> <span id="m_ward"></span></p>
        <p><b>Date Reported:</b> <span id="m_reported"></span></p>
        <p><b>Expected Resolution:</b> <span id="m_expected"></span></p>

        <p id="m_description" class="modal-desc"></p>

        <img
            id="m_image"
            class="modal-image"
            style="display:none;"
        >
    </div>
</div>



 <?php }?>

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
</script>
<?php } ?>

</body>
</html>
