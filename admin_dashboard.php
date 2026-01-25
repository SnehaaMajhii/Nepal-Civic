<?php
session_start();
include "includes/db.php";

/* ======================
   ADMIN ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* ======================
   PAGE HANDLER
====================== */
$page = $_GET['page'] ?? 'dashboard';

/* ======================
   DASHBOARD STATS
====================== */
$totalIssues = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM issue")
)['total'];

$pendingIssues = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM issue WHERE status='pending'")
)['total'];

$resolvedIssues = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM issue WHERE status='resolved'")
)['total'];

$rejectedIssues = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM issue WHERE status='rejected'")
)['total'];

/* ======================
   CHART DATA
====================== */
$statusQ = mysqli_query($conn, "
    SELECT status AS label, COUNT(*) AS total
    FROM issue
    GROUP BY status
");

$deptQ = mysqli_query($conn, "
    SELECT department.department_name AS label, COUNT(issue.issue_id) AS total
    FROM issue
    JOIN department ON issue.department_id = department.department_id
    GROUP BY department.department_name
");

$wardQ = mysqli_query($conn, "
    SELECT CONCAT('Ward ', ward.ward_no) AS label, COUNT(issue.issue_id) AS total
    FROM issue
    JOIN ward ON issue.ward_id = ward.ward_id
    GROUP BY ward.ward_no
    ORDER BY ward.ward_no ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/main.js" defer></script>
</head>
<body>

<div class="dashboard-wrapper">
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php if ($page === 'dashboard') { ?>

<h2>Admin Dashboard</h2>
<p>System overview of reported complaints.</p>

<div class="dashboard-cards">
    <div class="stat-card"><h3>Total Issues</h3><p><?= $totalIssues ?></p></div>
    <div class="stat-card"><h3>Pending</h3><p><?= $pendingIssues ?></p></div>
    <div class="stat-card"><h3>Resolved</h3><p><?= $resolvedIssues ?></p></div>
    <div class="stat-card"><h3>Rejected</h3><p><?= $rejectedIssues ?></p></div>
</div>

<h3 style="margin-top:30px;">Analytics</h3>

<div class="dashboard-cards">
    <div class="stat-card"><h3>Issues by Status</h3><canvas id="statusChart"></canvas></div>
    <div class="stat-card"><h3>Issues by Department</h3><canvas id="deptChart"></canvas></div>
    <div class="stat-card"><h3>Issues by Ward</h3><canvas id="wardChart"></canvas></div>
</div>

<?php } elseif ($page === 'issues') { ?>

<?php
$issues = mysqli_query($conn, "
    SELECT issue.*, citizen.full_name, department.department_name, ward.ward_no
    FROM issue
    JOIN citizen ON issue.citizen_id = citizen.citizen_id
    JOIN department ON issue.department_id = department.department_id
    JOIN ward ON issue.ward_id = ward.ward_id
    ORDER BY issue.date_reported DESC
");
?>

<h2>Manage Issues</h2>

<?php while ($row = mysqli_fetch_assoc($issues)) { ?>
<div class="issue-card">
    <h3><?= htmlspecialchars($row['title']) ?></h3>
    <p><b>Citizen:</b> <?= htmlspecialchars($row['full_name']) ?></p>
    <p><b>Ward:</b> Ward <?= $row['ward_no'] ?></p>
    <p><b>Department:</b> <?= htmlspecialchars($row['department_name']) ?></p>
    <p><b>Status:</b> <span class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></p>
    <p><b>Urgency:</b> <?= ucfirst($row['urgency_level'] ?? 'n/a') ?></p>
    <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>

    <?php if (!empty($row['photo_update'])) { ?>
        <img src="uploads/issues/<?= htmlspecialchars($row['photo_update']) ?>" style="max-width:240px;border-radius:8px;">
    <?php } ?>

    <div class="issue-actions">
        <?php if ($row['status'] === 'pending') { ?>
            <a href="approve_issue.php?id=<?= $row['issue_id'] ?>"><button>Approve</button></a>
            <a href="reject_issue.php?id=<?= $row['issue_id'] ?>"><button>Reject</button></a>
        <?php } ?>
        <a href="generate_report.php?issue_id=<?= $row['issue_id'] ?>"><button>Generate PDF</button></a>
    </div>
</div>
<?php } ?>

<?php } elseif ($page === 'staff') { ?>

<?php
$staffQ = mysqli_query($conn, "
    SELECT ward_staff.*, ward.ward_no
    FROM ward_staff
    JOIN ward ON ward_staff.ward_id = ward.ward_id
    ORDER BY ward.ward_no
");
?>

<h2>Manage Ward Staff</h2>

<a href="create_staff.php"><button style="margin-bottom:15px;">+ Create Staff</button></a>

<table class="data-table">
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Designation</th>
    <th>Ward</th>
    <th>First Login</th>
</tr>
</thead>
<tbody>
<?php while ($s = mysqli_fetch_assoc($staffQ)) { ?>
<tr>
    <td><?= htmlspecialchars($s['full_name']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
    <td><?= htmlspecialchars($s['designation']) ?></td>
    <td>Ward <?= $s['ward_no'] ?></td>
    <td><?= $s['first_login'] == 0 ? '✔ Changed' : '✖ Pending' ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<?php } elseif ($page === 'citizens') { ?>

<?php
$citizenQ = mysqli_query($conn, "
    SELECT 
        citizen.full_name,
        citizen.email,
        citizen.address,
        citizen.national_id,
        ward.ward_no,
        citizen.date_registered
    FROM citizen
    JOIN ward ON citizen.ward_id = ward.ward_id
    ORDER BY citizen.date_registered DESC
");
?>

<h2>Manage Citizens</h2>

<table class="data-table">
<thead>
<tr>
    <th>Full Name</th>
    <th>Email</th>
    <th>Address</th>
    <th>Citizenship No.</th>
    <th>Ward</th>
    <th>Registered On</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($citizenQ) === 0) { ?>
<tr><td colspan="6" style="text-align:center;">No citizens found.</td></tr>
<?php } ?>

<?php while ($c = mysqli_fetch_assoc($citizenQ)) { ?>
<tr>
    <td><?= htmlspecialchars($c['full_name']) ?></td>
    <td><?= htmlspecialchars($c['email']) ?></td>
    <td><?= htmlspecialchars($c['address']) ?></td>
    <td><?= htmlspecialchars($c['national_id']) ?></td>
    <td>Ward <?= $c['ward_no'] ?></td>
    <td><?= date("d M Y", strtotime($c['date_registered'])) ?></td>
</tr>
<?php } ?>

</tbody>
</table>

<?php } elseif ($page === 'notifications') {

    include "notifications.php";

} else { ?>

<p>Invalid page requested.</p>

<?php } ?>

</div>
</div>

<?php if ($page === 'dashboard') { ?>
<script>
const statusData = [<?php while ($s = mysqli_fetch_assoc($statusQ)) echo "{label:'{$s['label']}',value:{$s['total']}},";
?>];
const deptData = [<?php while ($d = mysqli_fetch_assoc($deptQ)) echo "{label:'{$d['label']}',value:{$d['total']}},";
?>];
const wardData = [<?php while ($w = mysqli_fetch_assoc($wardQ)) echo "{label:'{$w['label']}',value:{$w['total']}},";
?>];
</script>
<?php } ?>

</body>
</html>
