<?php
include "includes/db.php";

/* ======================
   ADMIN ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* ======================
   GET ISSUE ID
====================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?page=issues");
    exit();
}

$issue_id = (int) $_GET['id'];
$admin_id = (int) $_SESSION['admin_id'];

/* ======================
   HANDLE FORM SUBMIT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    /* UPDATE ISSUE */
    mysqli_query($conn, "
        UPDATE issue 
        SET status='rejected',
            rejection_reason='$reason',
            admin_id=$admin_id
        WHERE issue_id=$issue_id
    ");

    /* INSERT ISSUE LOG */
    mysqli_query($conn, "
        INSERT INTO issue_logs
        (issue_id, action_by, user_role, action_type, description, created_at)
        VALUES
        ($issue_id, $admin_id, 'admin', 'Rejected', '$reason', NOW())
    ");

    /* GET CITIZEN ID */
    $citizenQ = mysqli_query($conn, "
        SELECT citizen_id FROM issue WHERE issue_id=$issue_id
    ");
    $citizen = mysqli_fetch_assoc($citizenQ);

    /* NOTIFY CITIZEN */
    mysqli_query($conn, "
        INSERT INTO notification
        (message, citizen_id, is_read, date_sent)
        VALUES
        ('Your issue was rejected. Reason: $reason',
         {$citizen['citizen_id']},
         0,
         NOW())
    ");

    header("Location: admin_dashboard.php?page=issues");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reject Issue | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Reject Issue</h2>

        <form method="POST">
            <label>Reason for rejection</label>
            <textarea name="reason" required></textarea>

            <button type="submit">Reject Issue</button>
        </form>
    </div>
</div>

</body>
</html>
