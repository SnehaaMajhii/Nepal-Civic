<?php
include "includes/db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* AUTH CHECK*/
if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user_role'];

/* ROLE-BASED QUERY*/
$query = "";

if ($role === 'citizen' && isset($_SESSION['citizen_id'])) {

    $cid = (int) $_SESSION['citizen_id'];

    $query = "
        SELECT *
        FROM notification
        WHERE citizen_id = $cid
        ORDER BY date_sent DESC
    ";

}
elseif ($role === 'staff' && isset($_SESSION['staff_id'])) {

    $sid = (int) $_SESSION['staff_id'];

    $query = "
        SELECT *
        FROM notification
        WHERE staff_id = $sid
        ORDER BY date_sent DESC
    ";

}
elseif ($role === 'admin' && isset($_SESSION['admin_id'])) {

    $aid = (int) $_SESSION['admin_id'];

    $query = "
        SELECT *
        FROM notification
        WHERE admin_id = $aid
        ORDER BY date_sent DESC
    ";

}
else {
    die("Invalid role or session data missing.");
}

/* FETCH NOTIFICATIONS*/
$notifications = mysqli_query($conn, $query);

if (!$notifications) {
    die("QUERY ERROR: " . mysqli_error($conn));
}

/*MARK AS READ*/
if ($role === 'citizen') {
    mysqli_query($conn, "UPDATE notification SET is_read = 1 WHERE citizen_id = $cid");
}
elseif ($role === 'staff') {
    mysqli_query($conn, "UPDATE notification SET is_read = 1 WHERE staff_id = $sid");
}
elseif ($role === 'admin') {
    mysqli_query($conn, "UPDATE notification SET is_read = 1 WHERE admin_id = $aid");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        .issue-card {
            background:#fff;
            padding:15px;
            margin-bottom:12px;
            border-radius:6px;
            border-left:4px solid transparent;
            box-shadow:0 2px 6px rgba(0,0,0,0.08);
        }
        .issue-card.unread {
            border-left-color:#c4161c;
        }
        .empty-state {
            padding:20px;
            color:#777;
        }
        button {
            padding:6px 12px;
            cursor:pointer;
        }
    </style>
</head>
<body>

<h2>Notifications</h2>

<?php if (mysqli_num_rows($notifications) === 0) { ?>
    <div class="empty-state">No notifications found.</div>
<?php } ?>

<?php while ($n = mysqli_fetch_assoc($notifications)) { ?>

    <div class="issue-card <?= $n['is_read'] ? '' : 'unread' ?>">

        <p><?= htmlspecialchars($n['message']) ?></p>

        <small>
            <?= date("d M Y, h:i A", strtotime($n['date_sent'])) ?>
        </small>

        <?php if (!empty($n['issue_id'])) { ?>
            <div style="margin-top:10px;">
                <?php if ($role === 'citizen') { ?>
                    <a href="citizen_dashboard.php?page=my_issues">
                        <button>View Issue</button>
                    </a>
                <?php } elseif ($role === 'admin') { ?>
                    <a href="admin_dashboard.php?page=issues">
                        <button>View Issue</button>
                    </a>
                <?php } elseif ($role === 'staff') { ?>
                    <a href="staff_dashboard.php">
                        <button>View Issue</button>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

<?php } ?>

</body>
</html>
