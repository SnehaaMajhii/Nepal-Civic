<?php
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

/* ======================
   MARK ALL AS READ
====================== */
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

<h2>Notifications</h2>

<?php if (mysqli_num_rows($notifications) === 0) { ?>
    <div class="empty-state">No notifications found.</div>
<?php } ?>

<?php while ($n = mysqli_fetch_assoc($notifications)) { ?>

    <div class="issue-card"
         style="<?= $n['is_read'] ? '' : 'border-left:4px solid #c4161c;' ?>">

        <p><?= htmlspecialchars($n['message']) ?></p>

        <small>
            <?= date("d M Y, h:i A", strtotime($n['date_sent'])) ?>
        </small>

        <?php if (!empty($n['issue_id'])) { ?>
            <div class="issue-actions" style="margin-top:10px;">
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
