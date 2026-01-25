<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "includes/db.php";

/* ======================
   AUTH CHECK
====================== */
if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user_role'];

/* ======================
   ROLE-BASED QUERY
====================== */
if ($role === 'citizen') {
    $cid = (int) $_SESSION['citizen_id'];
    $query = "
        SELECT *
        FROM notification
        WHERE citizen_id = $cid
        ORDER BY date_sent DESC
    ";
}

elseif ($role === 'staff') {
    $sid = (int) $_SESSION['staff_id'];
    $query = "
        SELECT *
        FROM notification
        WHERE staff_id = $sid
        ORDER BY date_sent DESC
    ";
}

elseif ($role === 'admin') {
    $aid = (int) $_SESSION['admin_id'];
    $query = "
        SELECT *
        FROM notification
        WHERE admin_id = $aid
        ORDER BY date_sent DESC
    ";
}


$notifications = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- <div class="main-content"> -->

    <h2>Notifications</h2>

    <?php if (mysqli_num_rows($notifications) === 0) { ?>
        <p>No notifications found.</p>
    <?php } ?>

    <?php while ($n = mysqli_fetch_assoc($notifications)) { ?>

        <div class="issue-card"
             style="<?= $n['is_read'] ? '' : 'border-left:4px solid #c4161c;' ?>">

            <p><?= htmlspecialchars($n['message']) ?></p>

            <small><?= date("d M Y, h:i A", strtotime($n['date_sent'])) ?></small>

            <?php if (!empty($n['issue_id'])) { ?>
                <div class="issue-actions" style="margin-top:10px;">
                    <a href="generate_report.php?issue_id=<?= (int)$n['issue_id'] ?>">
                        <button>View Issue</button>
                    </a>
                </div>
            <?php } ?>

        </div>

    <?php } ?>

</div>

</body>
</html>
