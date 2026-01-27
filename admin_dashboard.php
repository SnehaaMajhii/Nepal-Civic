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
    <script>
        window.APP_ROLE = "admin";
    </script>

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

<div class="analytics-vertical">
    <div class="stat-card chart-card">
        <h3>Issues by Status</h3>
        <canvas id="statusChart"></canvas>
    </div>

    <div class="stat-card chart-card">
        <h3>Issues by Department</h3>
        <canvas id="deptChart"></canvas>
    </div>

    <div class="stat-card chart-card">
        <h3>Issues by Ward</h3>
        <canvas id="wardChart"></canvas>
    </div>
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

<div class="issue-filters">
    <input type="text" id="filterTitle" placeholder="Search by title">

    <select id="filterDepartment">
        <option value="">All Departments</option>
        <?php
        $dQ = mysqli_query($conn, "SELECT department_id, department_name FROM department");
        while ($d = mysqli_fetch_assoc($dQ)) {
            echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
        }
        ?>
    </select>

    <select id="filterStatus">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="assigned">Assigned</option>
        <option value="resolved">Resolved</option>
        <option value="rejected">Rejected</option>
    </select>

    <select id="filterWard">
        <option value="">All Wards</option>
        <?php
        $wQ = mysqli_query($conn, "SELECT ward_id, ward_no FROM ward ORDER BY ward_no");
        while ($w = mysqli_fetch_assoc($wQ)) {
            echo "<option value='{$w['ward_id']}'>Ward {$w['ward_no']}</option>";
        }
        ?>
    </select>
</div>

<div class="table-card">
<table class="styled-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Citizen</th>
            <th>Ward</th>
            <th>Department</th>
            <th>Status</th>
            <th>Urgency</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="issueTableBody"></tbody>
</table>
</div>

<div id="pagination" class="pagination"></div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    if (typeof USER_ROLE === "undefined") return;
    if (USER_ROLE !== "admin") return;

    // Force-init admin issue table
    if (document.getElementById("issueTableBody")) {
        // small delay to ensure main.js is loaded
        setTimeout(() => {
            if (typeof window.initIssueTable === "function") {
                window.initIssueTable();
            }
        }, 0);
    }
});
</script>


<?php } elseif ($page === 'staff') { ?>

<?php
$staffQ = mysqli_query($conn, "
    SELECT 
        ward_staff.staff_id,
        ward_staff.full_name,
        ward_staff.email,
        ward_staff.designation,
        ward_staff.first_login,
        ward.ward_no
    FROM ward_staff
    JOIN ward ON ward_staff.ward_id = ward.ward_id
    ORDER BY ward.ward_no
");
?>

<h2>Manage Ward Staff</h2>

<a href="create_staff.php">
    <button style="margin-bottom:15px;">+ Create Staff</button>
</a>

<table class="data-table">
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Designation</th>
    <th>Ward</th>
    <th>First Login</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($staffQ) === 0) { ?>
<tr>
    <td colspan="6" style="text-align:center;">No staff found.</td>
</tr>
<?php } ?>

<?php while ($s = mysqli_fetch_assoc($staffQ)) { ?>
<tr>
    <td><?= htmlspecialchars($s['full_name']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
    <td><?= htmlspecialchars($s['designation']) ?></td>
    <td>Ward <?= $s['ward_no'] ?></td>
    <td><?= $s['first_login'] == 0 ? '✔ Changed' : '✖ Pending' ?></td>
    <td>
        <a 
          href="delete_user.php?type=staff&id=<?= $s['staff_id'] ?>"
          onclick="return confirm('Are you sure you want to delete this staff member?');"
          style="color:red;font-weight:bold;"
        >
            Delete
        </a>
    </td>
</tr>
<?php } ?>

</tbody>
</table>


<?php } elseif ($page === 'citizens') { ?>

<?php
$citizenQ = mysqli_query($conn, "
    SELECT 
        citizen.citizen_id,
        citizen.full_name,
        citizen.email,
        citizen.address,
        citizen.national_id,
        ward.ward_no,
        citizen.date_registered
    FROM citizen
    JOIN ward ON citizen.ward_id = ward.ward_id
    WHERE citizen.is_active = 1
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
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($citizenQ) === 0) { ?>
<tr><td colspan="7" style="text-align:center;">No citizens found.</td></tr>
<?php } ?>

<?php while ($c = mysqli_fetch_assoc($citizenQ)) { ?>
<tr>
    <td><?= htmlspecialchars($c['full_name']) ?></td>
    <td><?= htmlspecialchars($c['email']) ?></td>
    <td><?= htmlspecialchars($c['address']) ?></td>
    <td><?= htmlspecialchars($c['national_id']) ?></td>
    <td>Ward <?= $c['ward_no'] ?></td>
    <td><?= date("d M Y", strtotime($c['date_registered'])) ?></td>
    <td>
       <a href="delete_user.php?type=citizen&id=<?= $c['citizen_id'] ?>"

           onclick="return confirm('Are you sure you want to delete this citizen?');"
           style="color:red;font-weight:bold;">
            Delete
        </a>
    </td>
</tr>
<?php } ?>

</tbody>
</table>

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
