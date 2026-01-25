<?php
include "includes/db.php";

// Staff only
if ($_SESSION['user_role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$ward_id  = $_SESSION['ward_id'];

// Fetch assigned issues for this ward
$issues = mysqli_query($conn, "
    SELECT issue.*, citizen.full_name, department.department_name
    FROM issue
    JOIN citizen ON issue.citizen_id = citizen.citizen_id
    JOIN department ON issue.department_id = department.department_id
    WHERE issue.ward_id = $ward_id
      AND issue.status = 'assigned'
    ORDER BY issue.date_reported DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ward Staff Dashboard | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <?php include "includes/sidebar.php"; ?>

    <!-- Main Content -->
    <div class="main-content">

        <h2>Ward Staff Dashboard</h2>

        <h3>Assigned Issues</h3>

        <?php if (mysqli_num_rows($issues) == 0) { ?>
            <p>No assigned issues.</p>
        <?php } ?>

        <?php while ($row = mysqli_fetch_assoc($issues)) { ?>
            <div class="issue-card">
                <h3><?php echo $row['title']; ?></h3>

                <p><b>Citizen:</b> <?php echo $row['full_name']; ?></p>
                <p><b>Department:</b> <?php echo $row['department_name']; ?></p>
                <p><?php echo $row['description']; ?></p>
                <p><b>Status:</b> <?php echo ucfirst($row['status']); ?></p>

                <div class="issue-actions">
                    <a href="update_status.php?id=<?php echo $row['issue_id']; ?>">
                        <button>Update Status</button>
                    </a>
                </div>
            </div>
        <?php } ?>

    </div>
</div>

</body>

</html>
