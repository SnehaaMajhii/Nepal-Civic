<?php
include "includes/db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ======================
   STAFF ACCESS ONLY
====================== */

/* STAFF ACCESS ONLY */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: staff_dashboard.php");
    exit();
}


/* ======================
   GET ISSUE ID
====================== */
if (!isset($_GET['issue_id']) || !is_numeric($_GET['issue_id'])){

    header("Location: staff_dashboard.php");
    exit();
}

$issue_id = (int) $_GET['issue_id'];
$staff_id = (int) $_SESSION['staff_id'];

/* ======================
   HANDLE FORM SUBMIT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $remark = mysqli_real_escape_string($conn, $_POST['remark']);

    /* UPDATE ISSUE STATUS */
    mysqli_query($conn, "
        UPDATE issue
        SET status='resolved',
            resolved_date=NOW()
        WHERE issue_id=$issue_id
    ");

    /* INSERT ISSUE LOG */
    mysqli_query($conn, "
        INSERT INTO issue_logs
        (issue_id, action_by, user_role, action_type, description, created_at)
        VALUES
        ($issue_id, $staff_id, 'staff', 'Resolved', '$remark', NOW())
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
        (
            'Your issue has been resolved by ward staff.',
            {$citizen['citizen_id']},
            0,
            NOW()
        )
    ");

    header("Location: staff_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resolve Issue | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Resolve Issue</h2>

        <form method="POST">
            <label>Resolution details</label>
            <textarea name="remark" required></textarea>

            <button type="submit">Confirm Resolution</button>
        </form>
    </div>
</div>

</body>
</html>
