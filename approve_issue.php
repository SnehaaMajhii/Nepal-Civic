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
   VALIDATE ISSUE ID
====================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?page=issues");
    exit();
}

$issue_id = (int) $_GET['id'];
$admin_id = (int) $_SESSION['admin_id'];

/* ======================
   FETCH ISSUE
====================== */
$issueQ = mysqli_query($conn, "
    SELECT issue_id, title, status, citizen_id 
    FROM issue 
    WHERE issue_id = $issue_id
");

if (mysqli_num_rows($issueQ) === 0) {
    header("Location: admin_dashboard.php?page=issues");
    exit();
}

$issue = mysqli_fetch_assoc($issueQ);

/* ======================
   PREVENT DOUBLE ACTION
====================== */
if ($issue['status'] !== 'pending') {
    header("Location: admin_dashboard.php?page=issues");
    exit();
}

/* ======================
   HANDLE FORM SUBMIT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['expected_resolution_date'])) {
        $error = "Expected resolution date is required.";
    } else {

        $expected_date = mysqli_real_escape_string(
            $conn,
            $_POST['expected_resolution_date']
        );

        /* ---- UPDATE ISSUE ---- */
        mysqli_query($conn, "
            UPDATE issue 
            SET status = 'assigned',
                admin_id = $admin_id,
                expected_resolution_date = '$expected_date'
            WHERE issue_id = $issue_id
        ");

        /* ---- LOG ACTION ---- */
        mysqli_query($conn, "
            INSERT INTO issue_logs
            (issue_id, action_by, user_role, action_type, description, created_at)
            VALUES
            (
                $issue_id,
                $admin_id,
                'admin',
                'Approved',
                'Issue approved with expected resolution date',
                NOW()
            )
        ");

        /* ---- NOTIFY CITIZEN ---- */
        $citizen_id = (int) $issue['citizen_id'];

        mysqli_query($conn, "
            INSERT INTO notification
            (message, citizen_id, is_read, date_sent)
            VALUES
            (
                'Your issue has been approved. Expected resolution by $expected_date.',
                $citizen_id,
                0,
                NOW()
            )
        ");

        header("Location: admin_dashboard.php?page=issues");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Issue | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="dashboard-wrapper">
<?php include "includes/sidebar.php"; ?>

<div class="main-content">

    <h2>Approve Issue</h2>

    <div class="issue-card">
        <h3><?= htmlspecialchars($issue['title']) ?></h3>

        <?php if (!empty($error)) { ?>
            <p style="color:red;margin-bottom:10px;">
                <?= $error ?>
            </p>
        <?php } ?>

        <form method="POST">

            <label>Expected Resolution Date</label>
            <input
                type="date"
                name="expected_resolution_date"
                required
                min="<?= date('Y-m-d') ?>"
            >

            <div style="margin-top:15px;">
                <button type="submit">Approve Issue</button>
                <a href="admin_dashboard.php?page=issues" style="margin-left:10px;">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
</div>

</body>
</html>
