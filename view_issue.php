<?php
include "includes/session.php";
include "includes/db.php";

$issue_id = (int) $_GET['issue_id'];
$nid      = isset($_GET['nid']) ? (int) $_GET['nid'] : 0;

// Mark notification as read
if ($nid > 0) {
    mysqli_query($conn, "UPDATE notification SET is_read=1 WHERE notification_id=$nid");
}

// Fetch issue details
$issueQ = mysqli_query($conn, "
    SELECT issue.*, citizen.full_name, department.department_name
    FROM issue
    JOIN citizen ON issue.citizen_id = citizen.citizen_id
    JOIN department ON issue.department_id = department.department_id
    WHERE issue.issue_id=$issue_id
");

$issue = mysqli_fetch_assoc($issueQ);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Details | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2><?php echo $issue['title']; ?></h2>

    <p><b>Citizen:</b> <?php echo $issue['full_name']; ?></p>
    <p><b>Department:</b> <?php echo $issue['department_name']; ?></p>
    <p><b>Status:</b> <?php echo ucfirst($issue['status']); ?></p>
    <p><b>Description:</b></p>
    <p><?php echo $issue['description']; ?></p>

    <?php if ($issue['rejection_reason']) { ?>
        <p style="color:red;">
            <b>Rejection Reason:</b> <?php echo $issue['rejection_reason']; ?>
        </p>
    <?php } ?>

    <a href="javascript:history.back()"><button>Back</button></a>
</div>

</body>
</html>
